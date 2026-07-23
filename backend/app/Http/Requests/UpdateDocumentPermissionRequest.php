<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDocumentPermissionRequest extends FormRequest
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

            'lecture' => 'sometimes|boolean',

            'ecriture' => 'sometimes|boolean',

            'suppression' => 'sometimes|boolean',

        ];
    }

    public function messages(): array
    {
        return [

            'role.required' => 'Le rôle est obligatoire.',

            'role.in' => 'Rôle invalide.',

        ];
    }
}