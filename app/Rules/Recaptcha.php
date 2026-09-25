<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Http;

class Recaptcha implements Rule
{
    public function passes($attribute, $value)
    {
        try {
            $response = Http::asForm()->timeout(5)->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => config('services.recaptcha.secret_key'),
                'response' => $value,
                'remoteip' => request()->ip(),
            ]);
        } catch (\Throwable $e) {
            // Google unreachable: fail the check instead of returning a 500.
            report($e);

            return false;
        }

        return (bool) $response->json('success', false);
    }

    public function message()
    {
        return 'The reCAPTCHA verification failed. Please try again.';
    }
}
