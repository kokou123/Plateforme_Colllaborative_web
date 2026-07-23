<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'nom' => 'required|string|max:255',

            'document' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png|max:10240',

            'projet_id' => 'required|exists:projets,id',

            'user_id' => 'required|exists:users,id',

        ];
    }

    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom du document est obligatoire.',
            'document.required' => 'Veuillez sélectionner un fichier.',
            'document.file' => 'Le fichier est invalide.',
            'document.mimes' => 'Format de fichier non autorisé.',
            'document.max' => 'La taille maximale est de 10 Mo.',
        ];
    }
}