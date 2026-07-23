<?php

namespace App\Services;

use App\Enums\IntentType;

class IntentService
{
    public static function detecter(string $message): IntentType
    {
        $message = mb_strtolower($message);

        /*
        |--------------------------------------------------------------------------
        | Projet
        |--------------------------------------------------------------------------
        */

        if (
            str_contains($message, 'projet') ||
            str_contains($message, 'projets')
        ) {
            return IntentType::PROJECT_SEARCH;
        }

        /*
        |--------------------------------------------------------------------------
        | Documents
        |--------------------------------------------------------------------------
        */

        if (
            str_contains($message, 'document') ||
            str_contains($message, 'documents') ||
            str_contains($message, 'fichier')
        ) {
            return IntentType::DOCUMENT_SEARCH;
        }

        /*
        |--------------------------------------------------------------------------
        | Tâches
        |--------------------------------------------------------------------------
        */

        if (
            str_contains($message, 'tâche') ||
            str_contains($message, 'taches') ||
            str_contains($message, 'tâches')
        ) {
            return IntentType::TASK_SEARCH;
        }

        /*
        |--------------------------------------------------------------------------
        | Processus
        |--------------------------------------------------------------------------
        */

        if (
            str_contains($message, 'processus') ||
            str_contains($message, 'workflow') ||
            str_contains($message, 'étape')
        ) {
            return IntentType::PROCESS_SEARCH;
        }

        /*
        |--------------------------------------------------------------------------
        | Rapport
        |--------------------------------------------------------------------------
        */

        if (
            str_contains($message, 'rapport') ||
            str_contains($message, 'statistique') ||
            str_contains($message, 'bilan')
        ) {
            return IntentType::REPORT;
        }

        /*
        |--------------------------------------------------------------------------
        | Résumé
        |--------------------------------------------------------------------------
        */

        if (
            str_contains($message, 'résumé') ||
            str_contains($message, 'résume') ||
            str_contains($message, 'resume')
        ) {
            return IntentType::SUMMARY;
        }

        /*
        |--------------------------------------------------------------------------
        | Suggestions
        |--------------------------------------------------------------------------
        */

        if (
            str_contains($message, 'propose') ||
            str_contains($message, 'suggestion')
        ) {
            return IntentType::SUGGESTION;
        }

        return IntentType::UNKNOWN;
    }
}