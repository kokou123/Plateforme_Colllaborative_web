<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EtapeProcessusResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'nom' => $this->nom,

            'ordre' => $this->ordre,

            'statut' => $this->statut,

            'date_debut' => $this->date_debut,

            'date_fin' => $this->date_fin,

            'commentaire' => $this->commentaire,

            'processus' => new ProcessusResource(
                $this->whenLoaded('processus')
            ),

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at,

        ];
    }
}