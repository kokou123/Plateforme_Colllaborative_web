<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VersionDocumentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'id'=>$this->id,

            'numero'=>$this->numero,

            'type'=>$this->type,

            'taille'=>$this->taille,

            'commentaire'=>$this->commentaire,

            'document'=>[
                'id'=>$this->document->id,
                'nom'=>$this->document->nom,
            ],

            'utilisateur'=>[
                'id'=>$this->utilisateur->id,
                'nom'=>$this->utilisateur->nom,
                'prenom'=>$this->utilisateur->prenom,
            ],

            'created_at'=>$this->created_at,
        ];
    }
}