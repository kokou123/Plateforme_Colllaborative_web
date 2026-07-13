<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'role' => 'required|exists:roles,name',
            'photo' => 'nullable|image|max:2048',
        ];
    }

    public function messages(): array
    {
        return [

            'nom.required' => 'Le nom est obligatoire.',

            'prenom.required' => 'Le prénom est obligatoire.',

            'email.required' => 'L\'adresse email est obligatoire.',

            'email.email' => 'L\'adresse email est invalide.',

            'email.unique' => 'Cette adresse email existe déjà.',

            'password.required' => 'Le mot de passe est obligatoire.',

            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',

            'role.required' => 'Veuillez choisir un rôle.',

            'role.exists' => 'Le rôle sélectionné est invalide.',

            'photo.image' => 'Le fichier doit être une image.',

            'photo.mimes' => 'Formats autorisés : jpg, jpeg, png.',

            'photo.max' => 'La taille maximale est de 2 Mo.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nom' => 'nom',
            'prenom' => 'prénom',
            'email' => 'adresse email',
            'photo' => 'photo',
            'role' => 'rôle',
        ];
    }
}
