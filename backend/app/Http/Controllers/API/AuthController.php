<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Services\AuditLogService;

class AuthController extends Controller
{

    /**
     * Connexion
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'=>'required|email',
            'password'=>'required'
        ]);

        $user = User::where('email',$request->email)->first();

        if(!$user->email_verifie){

        return response()->json([

            'message'=>'Veuillez vérifier votre adresse email.'

        ],403);

        }

        if(!$user || !Hash::check($request->password,$user->password))
        {
            return response()->json([
                'message'=>'Email ou mot de passe incorrect.'
            ],401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message'=>'Connexion réussie.',
            'token'=>$token,
            'user'=>$user,
            'roles'=>$user->getRoleNames()
        ],200);

        LoginHistory::create([

        'user_id'=>$user->id,

        'ip'=>$request->ip(),

        'user_agent'=>$request->userAgent()

        ]);

        AuditLogService::enregistrer(
        auth()->id(),
        'Connexion',
        'Authentification',
        null,
        'Connexion réussie.'
    );

    }

    /**
     * Profil connecté
     */
    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message'=>'Déconnexion réussie.'
        ]);

        AuditLogService::enregistrer(
        auth()->id(),
        'Déconnexion',
        'Authentification',
        null,
        'Déconnexion.'
    );
    }
    

}