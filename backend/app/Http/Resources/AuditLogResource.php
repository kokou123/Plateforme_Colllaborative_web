<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuditLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
    return
    [
            
        'id'=>$this->id,

        'action'=>$this->action,

        'module'=>$this->module,

        'element_id'=>$this->element_id,

        'description'=>$this->description,

        'utilisateur'=>new UserResource($this->whenLoaded('utilisateur')),

        'adresse_ip'=>$this->adresse_ip,

        'user_agent'=>$this->user_agent,

        'created_at'=>$this->created_at,
    ];
    }
}
