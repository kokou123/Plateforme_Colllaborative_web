<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTacheRequest extends FormRequest
{
    /**
     * Determine if the user is authorized.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation.
     */
    public function rules(): array
    {
        return [

            'titre' => 'required|string|max:255',

            'description' => 'nullable|string',

            'date_debut' => 'required|date',

            'date_fin' => 'nullable|date|after_or_equal:date_debut',

            'priorite' => 'required|in:Faible,Moyenne,Haute,Urgente',

            'statut' => 'nullable|in:À faire,En cours,En révision,Terminée',

            'projet_id' => 'required|exists:projets,id',

            'assigned_to' => 'nullable|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [

            'titre.required' => 'Le titre est obligatoire.',

            'projet_id.required' => 'Le projet est obligatoire.',

            'projet_id.exists' => 'Projet introuvable.',

            'assigned_to.exists' => 'Utilisateur introuvable.',

            'date_fin.after_or_equal' => 'La date de fin doit être postérieure à la date de début.',

        ];
    }
}