<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignerTacheRequest extends FormRequest
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
            'assigned_to' => 'required|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'assigned_to.required' => 'Veuillez sélectionner un employé.',
            'assigned_to.exists' => 'Cet utilisateur est introuvable.',
        ];
    }

    public function attributes(): array
    {
        return [
            'assigned_to' => 'employé',
        ];
    }
}