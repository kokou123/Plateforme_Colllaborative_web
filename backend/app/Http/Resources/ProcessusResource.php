<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProcessusResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'nom' => $this->nom,

            'description' => $this->description,

            'statut' => $this->statut,

            'projet' => new ProjetResource(
                $this->whenLoaded('projet')
            ),

            'etapes' => EtapeProcessusResource::collection(
                $this->whenLoaded('etapes')
            ),

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at,

        ];
    }
}