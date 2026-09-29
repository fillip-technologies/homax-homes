<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Services\Chatbot\ChatAssistant;
use App\Services\Chatbot\GeminiUnavailable;
use App\Services\Chatbot\OfflineAssistant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * POST /chatbot. Answers {reply, mode}: mode "ai" is Gemini; "offline" is the
 * rule-based assistant, used when Gemini is not configured, fails, or the daily
 * limit is spent - so the widget always gets a real answer.
 */
class ChatbotController extends Controller
{
    private const MAX_TURNS = 12;

    public function __invoke(Request $request, ChatAssistant $assistant, OfflineAssistant $offline): JsonResponse
    {
        $data = $request->validate([
            'messages' => 'required|array|min:1|max:40',
            'messages.*.role' => 'required|in:user,model',
            'messages.*.text' => 'required|string|max:1000',
            'page' => 'nullable|string|max:500',
        ]);

        // Gemini needs the conversation to start and end on a visitor turn.
        $messages = array_slice(array_values($data['messages']), -self::MAX_TURNS);
        while ($messages && $messages[0]['role'] !== 'user') {
            array_shift($messages);
        }
        if (!$messages || end($messages)['role'] !== 'user') {
            return response()->json(['message' => 'The conversation must end with a visitor message.'], 422);
        }

        $viewing = null;
        if (preg_match('~/property/([^/?#]+)~', (string) ($data['page'] ?? ''), $m)) {
            $param = $m[1];
            if (is_numeric($param)) {
                $viewing = (int) $param;
            } elseif ($param !== '') {
                $viewing = Property::where('slug', $param)->value('id');
            }
        }
        $ip = (string) $request->ip();
        $answerOffline = fn () => response()->json([
            'reply' => $offline->reply(end($messages)['text'], $viewing, $ip),
            'mode' => 'offline',
        ]);

        if (!$assistant->available()) {
            return $answerOffline();
        }

        if (!$this->withinDailyLimit()) {
            Log::warning('Chatbot: daily limit reached', ['limit' => config('chatbot.daily_limit')]);

            return $answerOffline();
        }

        try {
            $reply = $assistant->reply($messages, $viewing, $ip);
        } catch (GeminiUnavailable $e) {
            Log::warning('Chatbot: Gemini unavailable, answering offline', ['reason' => $e->getMessage()]);

            return $answerOffline();
        } catch (\Throwable $e) {
            report($e);

            return $answerOffline();
        }

        return response()->json(['reply' => $reply, 'mode' => 'ai']);
    }

    public static function fallbackText(): string
    {
        $c = config('chatbot.contact');

        return "You have reached today's chat limit. You can reach our team directly:\n"
            . "- Call {$c['phone']}\n"
            . "- WhatsApp {$c['whatsapp']}\n"
            . "- Email {$c['email']}\n"
            . '- Or browse projects: ' . route('property.search');
    }

    /** Site-wide daily cap on Gemini calls, counted per visitor message. */
    private function withinDailyLimit(): bool
    {
        $key = 'chatbot.daily.' . now()->format('Y-m-d');
        Cache::add($key, 0, now()->endOfDay());

        return Cache::increment($key) <= config('chatbot.daily_limit');
    }
}
