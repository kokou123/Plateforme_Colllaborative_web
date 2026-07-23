<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentaireRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'contenu' => 'required|string|max:1000',

            'tache_id' => 'required|exists:taches,id',

            'parent_id' => 'nullable|exists:commentaires,id',

        ];
    }

    public function messages(): array
    {
        return [

            'contenu.required' => 'Le commentaire est obligatoire.',

            'tache_id.required' => 'La tâche est obligatoire.',

            'tache_id.exists' => 'Cette tâche n\'existe pas.',

            'parent_id.exists' => 'Le commentaire parent est introuvable.',

        ];
    }
}