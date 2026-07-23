<?php

namespace App\Services;

use App\Models\Tache;
use App\Services\AIResponseService;
use App\Enums\IntentType;

class TaskSearchService
{
    public static function rechercher(int $userId, string $message): array
    {

        $query = Tache::with([
            'projet',
            'utilisateur'
        ]);

        $query->where(function ($q) use ($userId) {

            $q->where('assigned_to', $userId)

              ->orWhereHas('projet.membres', function ($m) use ($userId) {

                    $m->where('users.id', $userId);

              });

        });

        if (!empty(trim($message))) {

            $query->where('titre', 'LIKE', "%{$message}%");

        }

        $taches = $query->get();

        return AIResponseService::success(

        IntentType::TASK_SEARCH,

        $taches,

        $taches->count().' tâche(s) trouvée(s).'

        );

    }
}