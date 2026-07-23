<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangerStatutTacheRequest extends FormRequest
{
    /**
     * Determine if the user is authorized.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules.
     */
    public function rules(): array
    {
        return [
            'statut' => 'required|in:À faire,En cours,En révision,Terminée',
        ];
    }

    public function messages(): array
    {
        return [
            'statut.required' => 'Le statut est obligatoire.',
            'statut.in' => 'Le statut sélectionné est invalide.',
        ];
    }

    public function attributes(): array
    {
        return [
            'statut' => 'statut',
        ];
    }
}