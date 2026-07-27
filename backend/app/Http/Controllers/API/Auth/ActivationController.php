<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActivateAccountRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ActivationController extends Controller
{
    /**
     * Vérifie que le token est valide, sans activer le compte.
     * Utilisé par le frontend au chargement de la page d'activation.
     */
    public function check(string $token)
    {
        $user = User::where('invitation_token', $token)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Lien d\'invitation invalide.'
            ], 404);
        }

        if (now()->gt($user->invitation_expire_at)) {
            return response()->json([
                'message' => 'Ce lien d\'invitation a expiré.'
            ], 410);
        }

        return response()->json([
            'nom' => $user->nom,
            'prenom' => $user->prenom,
            'email' => $user->email,
            'entreprise' => $user->entreprise->nom,
        ]);
    }

    /**
     * Active le compte : définit le mot de passe choisi par l'employé.
     */
    public function activate(ActivateAccountRequest $request)
    {
        $user = User::where('invitation_token', $request->token)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Lien d\'invitation invalide.'
            ], 404);
        }

        if (now()->gt($user->invitation_expire_at)) {
            return response()->json([
                'message' => 'Ce lien d\'invitation a expiré.'
            ], 410);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'email_verifie' => true,
            'invitation_token' => null,
            'invitation_expire_at' => null,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Compte activé avec succès.',
            'token' => $token,
            'user' => $user,
            'roles' => $user->getRoleNames(),
        ]);
    }
}