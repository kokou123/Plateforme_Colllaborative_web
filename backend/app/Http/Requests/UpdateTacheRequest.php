<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTacheRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return True;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
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
}
