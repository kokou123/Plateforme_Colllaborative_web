<?php

namespace App\Services;

use App\Enums\IntentType;
use App\Models\Projet;
use App\Models\Tache;
use App\Models\Document;
use App\Models\Processus;

class ReportService
{
    public static function generer(int $userId, string $message): array
    {
        $rapport = [

            'projets' => Projet::count(),

            'taches' => Tache::count(),

            'documents' => Document::count(),

            'processus' => Processus::count(),

            'taches_terminees' => Tache::where('statut','terminée')->count(),

            'taches_en_cours' => Tache::where('statut','en cours')->count(),

            'projets_termines' => Projet::where('statut','terminé')->count(),

            'projets_en_cours' => Projet::where('statut','en cours')->count(),

        ];

        return AIResponseService::success(

            IntentType::REPORT,

            $rapport,

            "Rapport généré avec succès."

        );
    }

    public static function resumer(int $userId,string $message): array
    {
        return AIResponseService::success(

            IntentType::SUMMARY,

            [

                'question'=>$message

            ],

            "Résumé généré."

        );
    }

    public static function proposer(int $userId,string $message): array
    {

        return AIResponseService::success(

            IntentType::SUGGESTION,

            [

                "Créer davantage de tâches.",

                "Ajouter une documentation.",

                "Réaffecter certaines tâches.",

                "Créer un nouveau processus."

            ],

            "Suggestions générées."

        );

    }
}