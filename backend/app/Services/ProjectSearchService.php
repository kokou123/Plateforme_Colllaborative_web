<?php

namespace App\Services;

use App\Models\Projet;
use App\Services\AIResponseService;
use App\Enums\IntentType;

class ProjectSearchService
{
    public static function rechercher(int $userId, string $message): array
    {
        $query = Projet::with([
            'chefProjet',
            'membres',
            'taches'
        ]);

        // L'utilisateur doit être membre ou chef du projet
        $query->where(function ($q) use ($userId) {

            $q->where('chef_projet_id', $userId)

              ->orWhereHas('membres', function ($m) use ($userId) {

                    $m->where('users.id', $userId);

              });

        });

        // Recherche par nom si un texte est fourni
        if (!empty(trim($message))) {

            $query->where('nom', 'LIKE', "%{$message}%");

        }

        $projets = $query->get();

        return AIResponseService::success(

        IntentType::PROJECT_SEARCH,

        $projets,

        $projets->count().' projet(s) trouvé(s).'

        );
    }
}