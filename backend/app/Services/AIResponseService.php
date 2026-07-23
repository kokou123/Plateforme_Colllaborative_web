<?php

namespace App\Services;

use App\Enums\IntentType;

class AIResponseService
{
    public static function success(
        IntentType $intent,
        mixed $data,
        string $message = 'Succès'
    ): array {

        return [

            'success' => true,

            'intent' => $intent->value,

            'message' => $message,

            'count' => is_countable($data) ? count($data) : 1,

            'data' => $data

        ];
    }

    public static function error(
        string $message
    ): array {

        return [

            'success' => false,

            'message' => $message,

            'data' => []

        ];
    }
}