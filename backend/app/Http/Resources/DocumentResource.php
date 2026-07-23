<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'nom' => $this->nom,

            'type' => $this->type,

            'taille' => $this->taille,

            'chemin' => $this->chemin,

            'projet' => $this->whenLoaded(
                'projet',
                fn() => [
                    'id' => $this->projet->id,
                    'nom' => $this->projet->nom,
                ]
            ),

            'utilisateur' => $this->whenLoaded(
                'utilisateur',
                fn() => new UserResource($this->utilisateur)
            ),

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at,

        ];
    }
}