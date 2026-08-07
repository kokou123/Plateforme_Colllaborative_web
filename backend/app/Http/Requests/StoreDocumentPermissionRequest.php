<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentPermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'document_id' => 'required|exists:documents,id',

            'user_id' => 'required|exists:users,id',

            'lecture' => 'required|boolean',

            'ecriture' => 'required|boolean',

            'suppression' => 'nullable|boolean',

        ];
    }

    public function messages(): array
    {
        return [

            'document_id.required' => 'Le document est obligatoire.',

            'document_id.exists' => 'Document introuvable.',

            'user_id.required' => 'Utilisateur introuvable.',

            'lecture.required' => 'Permission de lecture obligatoire.',

            'ecriture.required' => 'Permission d\'écriture obligatoire.',

            'suppression.required' => 'Permission de suppression obligatoire.',

        ];
    }
}