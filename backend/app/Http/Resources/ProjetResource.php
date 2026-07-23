<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'nom' => $this->nom,

            'description' => $this->description,

            'statut' => $this->statut,

            'date_debut' => $this->date_debut,

            'date_fin' => $this->date_fin,

            'chef_projet' => $this->whenLoaded(
                'chefProjet',
                fn() => new UserResource($this->chefProjet)
            ),

            'nombre_membres' => $this->whenLoaded(
                'membres',
                fn() => $this->membres->count()
            ),

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at,
        ];
    }
}