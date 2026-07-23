<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TacheResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'titre' => $this->titre,

            'description' => $this->description,

            'priorite' => $this->priorite,

            'statut' => $this->statut,

            'date_debut' => $this->date_debut,

            'date_fin' => $this->date_fin,

            'projet' => $this->whenLoaded(
                'projet',
                fn() => [
                    'id' => $this->projet->id,
                    'nom' => $this->projet->nom,
                ]
            ),

            'assignee' => $this->whenLoaded(
                'assignee',
                fn() => new UserResource($this->assignee)
            ),

            'nombre_commentaires' => $this->whenLoaded(
                'commentaires',
                fn() => $this->commentaires->count()
            ),

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at,

        ];
    }
}