<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjetRequest;
use App\Http\Requests\UpdateProjetRequest;
use App\Http\Resources\ProjetResource;
use App\Http\Resources\UserResource;
use App\Models\Projet;
use App\Models\User;
use App\Services\NotificationService;
use App\Constants\NotificationType;
use Illuminate\Http\Request;
use App\Services\AuditLogService;

class ProjetController extends Controller
{
    /**
     * Liste des projets.
     */
    public function index()
    {
        $projets = Projet::with([
            'chefProjet.roles',
            'membres.roles'
        ])->get();

        return response()->json([
            'success' => true,
            'message' => 'Liste des projets.',
            'data' => ProjetResource::collection($projets)
        ]);
    }

    /**
     * Création d'un projet.
     */
    public function store(StoreProjetRequest $request)
    {
        // Création du projet
        $projet = Projet::create([
            ...$request->validated(),
            'user_id' => auth()->id(),
        ]);
        AuditLogService::enregistrer(
        auth()->id(),
        'Création',
        'Projet',
        $projet->id,
        "Création du projet {$projet->nom}."
    );

        // Le chef de projet devient automatiquement membre du projet
        $projet->membres()->syncWithoutDetaching([auth()->id()]);

        // Notification
        NotificationService::envoyer(

            auth()->id(),

            NotificationType::PROJET,

            "Le projet « {$projet->nom} » a été créé avec succès.",

            "/projets/".$projet->id

        );

        return response()->json([
            'success' => true,
            'message' => 'Projet créé avec succès.',
            'data' => new ProjetResource(
                $projet->load('chefProjet.roles', 'membres')
            )
        ], 201);
    }

    /**
     * Affichage d'un projet.
     */
    public function show(Projet $projet)
    {
        if ($projet->chefProjet->entreprise_id !== auth()->user()->entreprise_id) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        $projet->load([
            'chefProjet.roles',
            'membres.roles',
            'taches.assignee',
            'documents'
        ]);

        return response()->json(['success' => true, 'data' => new ProjetResource($projet)]);
    }

    /**
     * Modification d'un projet.
     */
    public function update(UpdateProjetRequest $request, Projet $projet)
    {
        

    $projet->update($request->validated());
    AuditLogService::enregistrer(
    auth()->id(),
    'Modification',
    'Projet',
    $projet->id,
    "Modification du projet {$projet->nom}."
    );

    if($ancienChef != $projet->chef_projet_id){

        NotificationService::envoyer(

            $projet->chef_projet_id,

            NotificationType::PROJET,

            "Vous êtes désormais chef du projet « {$projet->nom} ».",

            "/projets/".$projet->id

        );

        }
        return response()->json([
            'success' => true,
            'message' => 'Projet modifié avec succès.',
            'data' => new ProjetResource(
                $projet->fresh()->load('chefProjet.roles')
            )
        ]);
    }

    /**
     * Suppression d'un projet.
     */
    public function destroy(Projet $projet)
    {
        // Suppression des membres du projet
        $projet->membres()->detach();

        $projet->delete();
        AuditLogService::enregistrer(
            auth()->id(),
            'Suppression',
            'Projet',
            $projet->id,
            "Suppression du projet {$projet->nom}."
        );
         
        

        return response()->json([
            'success' => true,
            'message' => 'Projet supprimé avec succès.'
        ]);
    }

    /**
     * Ajouter un membre.
     */
    public function ajouterMembre(Request $request, Projet $projet)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $user = User::findOrFail($request->user_id);

        if ($projet->membres()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cet utilisateur appartient déjà à ce projet.'
            ], 409);
        }

        if ($user->id == $projet->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Le chef de projet est déjà responsable du projet.'
            ], 409);
        }

        $projet->membres()->attach($user->id);
        AuditLogService::enregistrer(
        auth()->id(),
        'Ajout membre',
        'Projet',
        $projet->id,
        "Ajout d'un membre au projet {$projet->nom}."
        );

        NotificationService::envoyer(
            $request->user_id,
            NotificationType::PROJET,
            "Vous avez été ajouté au projet « {$projet->nom} ».",
            "/projets/".$projet->id
        );

        return response()->json([
            'success' => true,
            'message' => 'Membre ajouté avec succès.',
            'data' => new UserResource(
                $user->load('roles', 'equipe')
            )
        ]);
    }

    /**
     * Retirer un membre.
     */
    public function retirerMembre(Projet $projet, User $user)
    {
        if (!$projet->membres()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cet utilisateur ne fait pas partie du projet.'
            ], 404);
        }

        $projet->membres()->detach($user->id);
        AuditLogService::enregistrer(
        auth()->id(),
        'Retrait membre',
        'Projet',
        $projet->id,
        "Retrait d'un membre du projet {$projet->nom}."
        );

        NotificationService::envoyer(
            $user->id,
            NotificationType::PROJET,
            "Vous avez été retiré du projet « {$projet->nom} ».",
            "/projets/".$projet->id
        );

        return response()->json([
            'success' => true,
            'message' => 'Membre retiré avec succès.'
        ]);
    }

    /**
     * Liste des membres.
     */
    public function membres(Projet $projet)
    {
        $membres = $projet->membres()
            ->with('roles', 'equipe')
            ->get();

        return response()->json([
            'success' => true,
            'data' => UserResource::collection($membres)
        ]);
    }
}