<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom' => 'sometimes|string|max:255',

            'description' => 'nullable|string',

            'date_debut' => 'sometimes|date',

            'date_fin' => 'nullable|date|after_or_equal:date_debut',

            'statut' => 'sometimes|in:à faire,En cours,Suspendu,Terminé',

            'user_id' => 'sometimes|exists:users,id'
        ];
    }
}