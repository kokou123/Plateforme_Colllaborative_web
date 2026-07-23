<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'type' => $this->type,

            'contenu' => $this->contenu,

            'lien' => $this->lien,

            'lu' => $this->lu,

            'utilisateur' => [

                'id' => $this->utilisateur->id,

                'nom' => $this->utilisateur->nom,

                'prenom' => $this->utilisateur->prenom,

            ],

            'created_at' => $this->created_at,

        ];
    }
}