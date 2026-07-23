<?php

namespace App\Services;

use App\Models\Processus;
use App\Services\AIResponseService;
use App\Enums\IntentType;

class ProcessSearchService
{
    public static function rechercher(int $userId, string $message): array
    {

        $query = Processus::with([
            'etapes'
        ]);

        if (!empty(trim($message))) {

            $query->where('nom', 'LIKE', "%{$message}%");

        }

        $processus = $query->get();

        return AIResponseService::success(

        IntentType::PROCESS_SEARCH,

        $processus,

        $processus->count().' processus trouvé(s).'

        );

    }
}