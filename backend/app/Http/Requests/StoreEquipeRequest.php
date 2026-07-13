<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEquipeRequest extends FormRequest
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
        return [
            'nom' => 'required|string|max:100|unique:equipes,nom',
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom de l\'équipe est obligatoire.',
            'nom.unique' => 'Une équipe portant ce nom existe déjà.',
            'nom.max' => 'Le nom ne doit pas dépasser 100 caractères.',
            'description.max' => 'La description ne doit pas dépasser 1000 caractères.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nom' => 'nom de l\'équipe',
            'description' => 'description',
        ];
    }
}