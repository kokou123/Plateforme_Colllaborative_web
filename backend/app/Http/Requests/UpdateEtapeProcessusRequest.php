<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEtapeProcessusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('Chef de projet');
    }

    public function rules(): array
    {
        return [

            'nom' => 'sometimes|string|max:255',

            'ordre' => 'sometimes|integer|min:1',

            'statut' => 'sometimes|in:en_attente,en_cours,terminee',

            'date_debut' => 'nullable|date',

            'date_fin' => 'nullable|date|after_or_equal:date_debut',

            'commentaire' => 'nullable|string'

        ];
    }
}