<?php

namespace App\Http\Controllers\API\AI;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssistantRequest;
use App\Services\AIService;

class AssistantController extends Controller
{

    public function chat(AssistantRequest $request)
    {

        return response()->json(

            AIService::traiter(

                $request->message

            )

        );

    }

}