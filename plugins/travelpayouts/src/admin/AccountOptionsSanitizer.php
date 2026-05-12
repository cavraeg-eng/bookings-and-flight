<?php

namespace Travelpayouts\admin;

class AccountOptionsSanitizer
{
    public function sanitize($newValue, $oldValue)
    {
        if (!is_array($newValue)) {
            return $newValue;
        }

        $oldValue = is_array($oldValue) ? $oldValue : [];

        if (array_key_exists('account_api_token', $newValue)) {
            $token = preg_replace('/[^a-zA-Z0-9]/', '', (string)$newValue['account_api_token']);
            if ('' === $token && !empty($oldValue['account_api_token'])) {
                $token = preg_replace('/[^a-zA-Z0-9]/', '', (string)$oldValue['account_api_token']);
            }
            $newValue['account_api_token'] = $token;
        }

        if (array_key_exists('account_api_marker', $newValue)) {
            $newValue['account_api_marker'] = preg_replace('/[^0-9]/', '', (string)$newValue['account_api_marker']);
        }

        foreach (['account_platform', 'account_flights_domain', 'account_hotels_domain'] as $key) {
            if (array_key_exists($key, $newValue)) {
                $newValue[$key] = sanitize_text_field((string)$newValue[$key]);
            }
        }

        return $newValue;
    }
}
