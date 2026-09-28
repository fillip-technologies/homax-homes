<?php

namespace App\Services\Chatbot;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Sleep;
use Illuminate\Support\Str;

/**
 * generateContent over REST, with a retry for transient errors and a fallback
 * through the configured models. A model that is rate-limited or down is put on
 * a cooldown in the cache, so later visitors skip it instead of waiting on it.
 */
class GeminiClient
{
    private const ENDPOINT = 'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent';

    /** Set when Google rejects the key; every model would fail the same way. */
    private const KEY_REJECTED = 'chatbot.gemini.key_rejected';

    public function configured(): bool
    {
        return filled(config('chatbot.gemini.key')) && config('chatbot.gemini.models');
    }

    /**
     * @param  float  $deadline  microtime(true) after which no new call is started
     * @return array{model: string, content: ?array, finishReason: ?string}
     *         content is null when Gemini answered but returned nothing usable (e.g. a safety block)
     *
     * @throws GeminiUnavailable
     */
    public function generate(array $payload, float $deadline): array
    {
        if (Cache::has(self::KEY_REJECTED)) {
            throw new GeminiUnavailable('API key was rejected recently');
        }

        $lastError = 'every model is cooling down';

        foreach (config('chatbot.gemini.models') as $model) {
            if (Cache::has($this->cooldownKey($model))) {
                continue;
            }

            $cooldown = 0;
            for ($attempt = 1; $attempt <= 2; $attempt++) {
                $remaining = $deadline - microtime(true);
                if ($remaining < 3) {
                    throw new GeminiUnavailable("ran out of time ({$lastError})");
                }

                try {
                    $response = Http::timeout((int) min(config('chatbot.gemini.timeout'), floor($remaining - 1)))
                        ->connectTimeout(5)
                        ->withHeaders(['x-goog-api-key' => config('chatbot.gemini.key')])
                        ->post(sprintf(self::ENDPOINT, $model), $payload);
                } catch (ConnectionException $e) {
                    $lastError = "{$model}: " . $e->getMessage();
                    Log::warning('Chatbot: Gemini connection failed', ['model' => $model, 'error' => $e->getMessage()]);
                    $cooldown = 30;
                    continue;
                }

                if ($response->successful()) {
                    return $this->parse($model, $response);
                }

                $status = $response->status();
                $lastError = "{$model}: HTTP {$status}";
                Log::warning('Chatbot: Gemini error', [
                    'model' => $model,
                    'status' => $status,
                    'body' => Str::limit($response->body(), 500),
                ]);

                if ($status === 401 || $status === 403) {
                    Cache::put(self::KEY_REJECTED, true, 600);
                    throw new GeminiUnavailable("API key rejected (HTTP {$status})");
                }

                if ($status === 429) {
                    $cooldown = $this->retryDelay($response);
                    break;
                }

                if ($status >= 500) {
                    $cooldown = 30;
                    Sleep::usleep(400_000 * $attempt);
                    continue;
                }

                // 404: the model name is retired or wrong. Other 4xx: this model
                // rejected the request; another model may still accept it.
                $cooldown = $status === 404 ? 3600 : 0;
                break;
            }

            if ($cooldown > 0) {
                Cache::put($this->cooldownKey($model), true, $cooldown);
            }
        }

        throw new GeminiUnavailable($lastError);
    }

    private function parse(string $model, Response $response): array
    {
        $candidate = $response->json('candidates.0');
        $content = $candidate['content'] ?? null;

        return [
            'model' => $model,
            'content' => !empty($content['parts']) ? $content : null,
            'finishReason' => $candidate['finishReason'] ?? $response->json('promptFeedback.blockReason'),
        ];
    }

    /** Seconds Google asks us to wait (RetryInfo), clamped to 10s - 1h; 60s when not given. */
    private function retryDelay(Response $response): int
    {
        foreach ((array) $response->json('error.details') as $detail) {
            if (str_ends_with($detail['@type'] ?? '', 'RetryInfo') && preg_match('/^(\d+)/', $detail['retryDelay'] ?? '', $m)) {
                return max(10, min(3600, (int) $m[1]));
            }
        }

        return 60;
    }

    private function cooldownKey(string $model): string
    {
        return 'chatbot.gemini.cooldown.' . $model;
    }
}
