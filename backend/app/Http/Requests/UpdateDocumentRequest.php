<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'nom' => 'sometimes|string|max:255',

            'projet_id' => 'sometimes|exists:projets,id',

        ];
    }
}