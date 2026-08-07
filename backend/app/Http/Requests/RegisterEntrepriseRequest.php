<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterEntrepriseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return True;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'nom' => 'required',
            'secteur' => 'nullable',
            'taille' => 'required|integer',
            'email_entreprise' => 'required|email|unique:entreprises,email',
            'telephone' => 'nullable',
            'adresse' => 'nullable',
            'nom_admin' => ['required', 'regex:/^[\pL\s\'\-\/]+$/u'],
            'prenom_admin' => ['required', 'regex:/^[\pL\s\'\-\/]+$/u'],
            'email_admin' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ];
    }

    public function messages()
    {
        return [
            'nom_admin.regex' => 'Le nom ne doit contenir que des lettres.',
            'prenom_admin.regex' => 'Le prénom ne doit contenir que des lettres.',
        ];
    }
}
