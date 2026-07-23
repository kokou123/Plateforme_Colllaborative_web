<?php

namespace App\Services;

class PromptService
    {

        public static function construire(

            string $question,

            array $contexte

        ): string
        {

            return

    "Tu es un assistant IA professionnel de gestion d'entreprise.

    Réponds uniquement avec les informations suivantes.

    Question :

    {$question}

    Contexte :

    ".json_encode(

    $contexte,

    JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE

    );

        }

    }