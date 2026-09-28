<?php

namespace App\Providers;

use App\Http\Controllers\ChatbotController;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Validator::extend(
            'recaptcha',
            'App\Rules\Recaptcha@passes',
            'The reCAPTCHA verification failed. Please try again.'
        );

        // Per-visitor limits for the chat widget; the reply keeps the widget's JSON shape.
        RateLimiter::for('chatbot', function (Request $request) {
            $tooMany = fn () => response()->json([
                'reply' => 'You are sending messages too quickly. Please wait a minute and try again.',
                'mode' => 'limited',
            ], 429);

            return [
                Limit::perMinute(config('chatbot.per_ip_per_minute'))->by('min:' . $request->ip())->response($tooMany),
                Limit::perDay(config('chatbot.per_ip_per_day'))->by('day:' . $request->ip())->response(
                    fn () => response()->json(['reply' => ChatbotController::fallbackText(), 'mode' => 'limited'], 429)
                ),
            ];
        });
    }
}
