<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentPermissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'id'=>$this->id,

            'utilisateur'=>$this->utilisateur,

            'lecture'=>$this->lecture,

            'ecriture'=>$this->ecriture,

            'suppression'=>$this->suppression,

        ];
    }
}