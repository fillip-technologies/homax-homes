<?php

namespace App\Services\Chatbot;

use RuntimeException;

/** Gemini could not produce an answer (quota, outage, bad key, timeout). */
class GeminiUnavailable extends RuntimeException
{
}
