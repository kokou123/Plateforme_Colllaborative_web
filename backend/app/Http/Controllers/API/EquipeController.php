<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEquipeRequest;
use App\Http\Requests\UpdateEquipeRequest;
use App\Http\Resources\EquipeResource;
use App\Http\Resources\UserResource;
use App\Models\Equipe;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\AjouterMembreEquipeRequest;
use App\Services\AuditLogService;

class EquipeController extends Controller 
{
    public function index()
    {
        $equipes = Equipe::with('utilisateurs.roles')->get();

        return response()->json([
            'success' => true,
            'message' => 'Liste des équipes.',
            'data' => EquipeResource::collection($equipes),
        ]);
    }
    public function store(StoreEquipeRequest $request)
    {
        $equipe = Equipe::create($request->validated());

        AuditLogService::enregistrer(
        auth()->id(),
        'Création',
        'Equipe',
        $equipe->id,
        "Création de l'équipe {$equipe->nom}."
        );

        return response()->json([
            'success' => true,
            'message' => 'Équipe créée avec succès.',
            'data' => new EquipeResource($equipe),
        ], 201);
    }
    public function show(Equipe $equipe)
    {
        $equipe->load('utilisateurs.roles');

        return response()->json([
            'success' => true,
            'data' => new EquipeResource($equipe),
        ]);
    }
    public function update(UpdateEquipeRequest $request, Equipe $equipe)
    {
        $equipe->update($request->validated());
        AuditLogService::enregistrer(
        auth()->id(),
        'Modification',
        'Equipe',
        $equipe->id,
        "Modification de l'équipe {$equipe->nom}."
        );

        return response()->json([
            'success' => true,
            'message' => 'Équipe modifiée avec succès.',
            'data' => new EquipeResource($equipe),
        ]);
    }
    public function destroy(Equipe $equipe)
    {
        User::where('equipe_id', $equipe->id)
            ->update(['equipe_id' => null]);

        $equipe->delete();
        AuditLogService::enregistrer(
        auth()->id(),
        'Suppresion',
        'Equipe',
        $equipe->id,
        "Suppression de l'équipe {$equipe->nom}."
    );

        return response()->json([
            'success' => true,
            'message' => 'Équipe supprimée avec succès.',
        ]);
    }
    public function ajouterMembre(AjouterMembreEquipeRequest $request, Equipe $equipe)
    {
        $user = User::findOrFail($request->user_id);

        if ($user->equipe_id == $equipe->id) {
            return response()->json([
                'success' => false,
                'message' => 'Cet utilisateur appartient déjà à cette équipe.'
            ], 409);
        }

        if (!is_null($user->equipe_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Cet utilisateur appartient déjà à une autre équipe.'
            ], 409);
        }

        $user->update([
            'equipe_id' => $equipe->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Utilisateur ajouté à l’équipe avec succès.',
            'data' => new UserResource($user->fresh()->load('roles', 'equipe')),
        ]);
    }
    
    public function retirerMembre(Equipe $equipe, User $user)
    {
        if ($user->equipe_id != $equipe->id) {
            return response()->json([
                'success' => false,
                'message' => 'Cet utilisateur n’appartient pas à cette équipe.',
            ], 404);
        }

        $user->update([
            'equipe_id' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Utilisateur retiré de l’équipe avec succès.',
        ]);
    }
    public function membres(Equipe $equipe)
    {
        $membres = $equipe->utilisateurs()->with('roles')->get();

        return response()->json([
            'success' => true,
            'data' => UserResource::collection($membres),
        ]);
    }

}

