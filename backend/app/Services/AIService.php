<?php

namespace App\Services;

use App\Enums\IntentType;
use Illuminate\Support\Facades\Auth;
use App\Services\AIResponseService;

class AIService
{
    /**
     * Traite la question de l'utilisateur.
     */
    public static function traiter(string $message): array
    {

        $intent = IntentService::detecter($message);

        $resultat = match($intent){

            IntentType::PROJECT_SEARCH=>

                ProjectSearchService::rechercher(

                    auth()->id(),

                    $message

                ),

            IntentType::DOCUMENT_SEARCH=>

                DocumentSearchService::rechercher(

                    auth()->id(),

                    $message

                ),

            IntentType::TASK_SEARCH=>

                TaskSearchService::rechercher(

                    auth()->id(),

                    $message

                ),

            IntentType::PROCESS_SEARCH=>

                ProcessSearchService::rechercher(

                    auth()->id(),

                    $message

                ),

            IntentType::REPORT=>

                ReportService::generer(

                    auth()->id(),

                    $message

                ),

            IntentType::SUMMARY=>

                ReportService::resumer(

                    auth()->id(),

                    $message

                ),

            IntentType::SUGGESTION=>

                ReportService::proposer(

                    auth()->id(),

                    $message

                ),

            default=>

                AIResponseService::error(

                    "Je n'ai pas compris."

                )

        };

        if(!$resultat['success']){

            return $resultat;

        }

        $prompt = PromptService::construire(

            $message,

            $resultat

        );

        $reponse = GeminiService::envoyer(

            $prompt

        );

        $resultat['ai']=$reponse;

        return $resultat;

    }
}