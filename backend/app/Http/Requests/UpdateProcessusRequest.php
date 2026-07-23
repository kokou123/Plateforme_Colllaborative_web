<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProcessusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('Chef de projet');
    }

    public function rules(): array
    {
        return [
            'nom' => 'sometimes|string|max:255',

            'description' => 'nullable|string',

            'statut' => 'sometimes|in:en_attente,en_cours,termine',
        ];
    }
}