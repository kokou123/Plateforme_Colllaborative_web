<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeminiService
{

    public static function envoyer(

        string $prompt

    ): string
    {

        $response = Http::post(

            "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key="

            .config('services.gemini.key'),

            [

                "contents"=>[

                    [

                        "parts"=>[

                            [

                                "text"=>$prompt

                            ]

                        ]

                    ]

                ]

            ]

        );

        if(!$response->successful()){

            return "Impossible de contacter Gemini.";

        }

        return

$response['candidates'][0]['content']['parts'][0]['text']

??

"Aucune réponse.";

    }

}