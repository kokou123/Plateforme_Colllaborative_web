<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Services\AuditLogService;
use Illuminate\Support\Str;
use App\Services\MailService;

class UserController
{

    public function index()
    {
         $users = User::with('roles')->get();

        return response()->json([
        'success' => true,
        'message' => 'Liste des utilisateurs',
        'data' => UserResource::collection($users),
    ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function show(User $user)
    {
        return response()->json([
            'success' => true,
            'data' => new UserResource($user->load('roles'))

        ]);
    }
    public function store(StoreUserRequest $request)
    {
        $token = Str::random(48);

        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => Hash::make(Str::random(32)), // placeholder, inutilisable
            'entreprise_id' => auth()->user()->entreprise_id,
            'invitation_token' => $token,
            'invitation_expire_at' => now()->addDays(7),
        ]);

        MailService::envoyerInvitation(
            $user->email,
            auth()->user()->entreprise->nom,
            env('FRONTEND_URL') . "/activation/" . $token
        );

        AuditLogService::enregistrer(
            auth()->id(), 'Création', 'Utilisateur', $user->id,
            "Création de {$user->nom} {$user->prenom}"
        );

        $user->assignRole($request->role);

        return response()->json([
            'message' => 'Invitation envoyée avec succès.',
            'data' => new UserResource($user->load('roles'))
        ], 201);
    }
    /*
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $photo = $user->photo;

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo')->store('users', 'public');
        }

        $user->update([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'photo' => $photo,
        ]);
        AuditLogService::enregistrer(
        auth()->id(),
        'Modification',
        'Utilisateur',
        $user->id,
        "Modification de {$user->nom}"
    );

        $user->syncRoles([$request->role]);

        return response()->json([
            'success' => true,
            'message' => 'Utilisateur modifié avec succès.',
            'data' => new UserResource($user->load('roles'))
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        AuditLogService::enregistrer(
        auth()->id(),
        'Suppression',
        'Utilisateur',
        $user->id,
        "Suppression de {$user->nom}"
    );
        return response()->json([
            'success' => true,
            'message' => 'Utilisateur supprimé avec succès.'
        ]);
    }
}
