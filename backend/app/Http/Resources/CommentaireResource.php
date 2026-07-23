<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentaireResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'contenu' => $this->contenu,

            'parent_id' => $this->parent_id,

            'utilisateur' => [

                'id' => $this->utilisateur->id,

                'nom' => $this->utilisateur->nom,

                'prenom' => $this->utilisateur->prenom,

            ],

            'tache' => [

                'id' => $this->tache->id,

                'titre' => $this->tache->titre,

            ],

            'reponses' => CommentaireResource::collection(
                $this->whenLoaded('reponses')
            ),

            'created_at' => $this->created_at,

        ];
    }
}