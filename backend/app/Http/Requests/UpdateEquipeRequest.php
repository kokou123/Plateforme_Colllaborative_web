<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEquipeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
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
        $equipe = $this->route('equipe');

        return [
            'nom' => [
                'required',
                'string',
                'max:100',
                Rule::unique('equipes')->ignore($equipe),
            ],

            'description' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom de l\'équipe est obligatoire.',
            'nom.unique' => 'Une équipe portant ce nom existe déjà.',
        ];
    }
}