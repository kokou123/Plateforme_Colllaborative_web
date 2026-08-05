<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'nom_complet' => $this->nom . ' ' . $this->prenom,
            'email' => $this->email,
            'photo' => $this->photo
                ? asset('storage/' . $this->photo)
                : null,
            'equipe_id' => $this->equipe_id,
            'email_verifie' => (bool) $this->email_verifie,
            'roles' => $this->getRoleNames(),
            'created_at' => $this->created_at?->format('d/m/Y H:i'),
            'updated_at' => $this->updated_at?->format('d/m/Y H:i'),
        ];
    }
}