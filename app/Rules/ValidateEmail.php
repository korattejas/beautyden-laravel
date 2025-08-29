<?php

namespace App\Rules;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Contracts\Validation\Rule;

class ValidateEmail implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return !$this->isDisposableEmail($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'Email is not allowed or is a disposable email address.';
    }

    protected function isDisposableEmail($email): bool
    {
        try {
            $emailProviderArray = config('custom.email_provider_array');
            $domain = explode('@', $email)[1];
            if (in_array($domain, $emailProviderArray)) {
                return false;
            }
            $client = new Client(['timeout' => 4]);
            $response = $client->get('https://' . $domain);

            return $response->getStatusCode() !== 200;
        } catch (\Throwable $e) {
            return true;
        }

    }
}
