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

        $newValue['account_api_token'] = $this->sanitizePreservedToken(
            $newValue['account_api_token'] ?? '',
            $oldValue['account_api_token'] ?? ''
        );

        $newValue['account_api_marker'] = $this->sanitizePreservedMarker(
            $newValue['account_api_marker'] ?? '',
            $oldValue['account_api_marker'] ?? ''
        );

        $newValue['account_platform'] = $this->sanitizePreservedProject(
            $newValue['account_platform'] ?? '',
            $oldValue['account_platform'] ?? ''
        );

        foreach (['account_flights_domain', 'account_hotels_domain'] as $key) {
            if (array_key_exists($key, $newValue)) {
                $newValue[$key] = $this->sanitizeTextValue((string)$newValue[$key]);
            } elseif (!empty($oldValue[$key])) {
                $newValue[$key] = $this->sanitizeTextValue((string)$oldValue[$key]);
            }
        }

        return $newValue;
    }

    private function sanitizePreservedToken($newToken, $oldToken): string
    {
        $token = preg_replace('/[^a-zA-Z0-9]/', '', (string)$newToken);
        if ('' === $token && !empty($oldToken)) {
            $token = preg_replace('/[^a-zA-Z0-9]/', '', (string)$oldToken);
        }

        return $token;
    }

    private function sanitizePreservedMarker($newMarker, $oldMarker): string
    {
        $marker = preg_replace('/[^0-9]/', '', (string)$newMarker);
        if ('' === $marker && !empty($oldMarker)) {
            $marker = preg_replace('/[^0-9]/', '', (string)$oldMarker);
        }

        return $marker;
    }

    private function sanitizePreservedProject($newProject, $oldProject): string
    {
        $project = preg_replace('/[^0-9]/', '', (string)$newProject);
        if (
            ('' === $project || '0' === $project)
            && !empty($oldProject)
            && '0' !== (string)$oldProject
        ) {
            $project = preg_replace('/[^0-9]/', '', (string)$oldProject);
        }

        return $project;
    }

    private function sanitizeTextValue(string $value): string
    {
        return sanitize_text_field($value);
    }
}
