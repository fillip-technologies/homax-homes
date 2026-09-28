<?php

return [

    // Hides the widget and turns POST /chatbot into the offline reply.
    'enabled' => (bool) env('CHATBOT_ENABLED', true),

    'gemini' => [
        'key' => env('GEMINI_API_KEY'),
        // Tried in order. Free-tier quotas are per model, so a second model keeps
        // the bot answering when the first one hits its limit.
        // gemini-2.5-flash/-lite were retired for new API keys (HTTP 404); the
        // "-latest" aliases track whatever Google currently ships as flash /
        // flash-lite, so this default does not need chasing every release.
        'models' => array_values(array_filter(array_map('trim', explode(',', (string) env('GEMINI_MODELS', 'gemini-flash-latest,gemini-flash-lite-latest'))))),
        // Seconds for a single HTTP call.
        'timeout' => 15,
    ],

    // Wall-clock seconds for answering one visitor message (all model calls and
    // lookups), kept under PHP's 30s max_execution_time.
    'time_budget' => 25,

    // How many rounds of property lookups the model may do before it must answer.
    'max_tool_rounds' => 4,

    // Messages the whole site may send to Gemini per day, to stay inside the free quota.
    'daily_limit' => (int) env('CHATBOT_DAILY_LIMIT', 1000),

    'per_ip_per_minute' => 8,
    'per_ip_per_day' => 150,
    'callbacks_per_ip_per_hour' => 3,

    // Shown by the bot, and in the offline reply when Gemini can't be reached.
    'contact' => [
        'phone' => '+91 99206 85877',
        'whatsapp' => 'https://wa.me/919920685877',
        'email' => 'admin@homaxhomes.com',
    ],
];
