<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProcessusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('Chef de projet');
    }

    public function rules(): array
    {
        return [
            'projet_id' => 'required|exists:projets,id',

            'nom' => 'required|string|max:255',

            'description' => 'nullable|string',

            'statut' => 'nullable|in:en_attente,en_cours,termine',
        ];
    }
}