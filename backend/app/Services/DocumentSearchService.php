<?php

namespace App\Services;

use App\Models\Document;
use App\Services\AIResponseService;
use App\Enums\IntentType;

class DocumentSearchService
{
    public static function rechercher(int $userId, string $message): array
    {

        $query = Document::with([
            'projet',
            'utilisateur',
            'versions'
        ]);

        $query->where(function ($q) use ($userId) {

            $q->where('user_id', $userId)

              ->orWhereHas('permissions', function ($permission) use ($userId) {

                    $permission

                        ->where('user_id', $userId)

                        ->where('lecture', true);

              });

        });

        if (!empty(trim($message))) {

            $query->where('nom', 'LIKE', "%{$message}%");

        }

        $documents = $query->get();

        return AIResponseService::success(

        IntentType::DOCUMENT_SEARCH,

        $documents,

        $documents->count().' document(s) trouvé(s).'

        );

    }
}