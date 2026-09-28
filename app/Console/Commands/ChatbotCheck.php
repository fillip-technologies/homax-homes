<?php

namespace App\Console\Commands;

use App\Services\Chatbot\ChatAssistant;
use App\Services\Chatbot\GeminiUnavailable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

/** Sends one real question through the chatbot, to check the key and models after a deploy. */
class ChatbotCheck extends Command
{
    protected $signature = 'chatbot:check {question=Which projects do you have?} {--reset : Clear model cooldowns first}';

    protected $description = 'Ask the website chatbot a question using the configured Gemini key';

    public function handle(ChatAssistant $assistant): int
    {
        if ($this->option('reset')) {
            Cache::forget('chatbot.gemini.key_rejected');
            foreach (config('chatbot.gemini.models') as $model) {
                Cache::forget('chatbot.gemini.cooldown.' . $model);
            }
        }

        if (!$assistant->available()) {
            $this->error('Chatbot is off: set GEMINI_API_KEY (and CHATBOT_ENABLED=true), then run php artisan config:clear.');

            return self::FAILURE;
        }

        $this->line('Models: ' . implode(', ', config('chatbot.gemini.models')));

        try {
            $this->info($assistant->reply([['role' => 'user', 'text' => $this->argument('question')]], null, '127.0.0.1'));
        } catch (GeminiUnavailable $e) {
            $this->error('Gemini unavailable: ' . $e->getMessage() . ' (details in storage/logs/laravel.log)');

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
