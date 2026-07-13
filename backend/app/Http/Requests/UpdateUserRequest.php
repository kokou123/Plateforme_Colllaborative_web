<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
        $user = $this->route('user');

        return [
            'nom' => 'required|string|max:100',

            'prenom' => 'required|string|max:100',

            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user)
            ],

            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

            'role' => 'required|exists:roles,name',
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