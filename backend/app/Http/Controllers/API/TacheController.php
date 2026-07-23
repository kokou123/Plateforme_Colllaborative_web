<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\AssignerTacheRequest;
use App\Http\Requests\ChangerStatutTacheRequest;
use App\Http\Requests\StoreTacheRequest;
use App\Http\Requests\UpdateTacheRequest;
use App\Http\Resources\TacheResource;
use App\Http\Resources\UserResource;
use App\Models\HistoriqueStatut;
use App\Models\Projet;
use App\Models\Tache;
use App\Models\User;
use App\Services\NotificationService;
use App\Constants\NotificationType;
use App\Services\AuditLogService;

class TacheController extends Controller
{
    /**
     * LISTER LES tâches
     */
    public function index()
    {
        $taches = Tache::with([
            'projet',
            'assignee.roles',
            'commentaires',
            'historiques'
        ])->get();

        return response()->json([
            'success' => true,
            'data' => TacheResource::collection($taches),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * CRéer une nouvelle tâche
     */ 
    public function store(StoreTacheRequest $request)
    {
        $projet = Projet::findOrFail($request->projet_id);

        if ($request->filled('assigned_to')) {
            if (!$projet->membres()->where('user_id', $request->assigned_to)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cet utilisateur ne fait pas partie du projet.'
                ], 422);
            }
        }

        $tache = Tache::create($request->validated());
        AuditLogService::enregistrer(
        auth()->id(),
        'Création',
        'Tâche',
        $tache->id,
        "Création de la tâche {$tache->titre}."
        );

        if ($tache->assigned_to) {
            NotificationService::envoyer(
                $tache->assigned_to,
                NotificationType::TACHE,
                "Vous êtes assigné à la tâche « {$tache->titre} ».",
                "/taches/".$tache->id
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Tâche créée avec succès.',
            'data' => new TacheResource(
                $tache->load('projet', 'assignee')
            )
        ], 201);
    }    

    /**
     * Display the specified resource.
     * afficher une tâche
     */
    public function show(Tache $tache)
    {
        $tache->load([
            'projet',
            'assignee.roles',
            'commentaires.user',
            'historiques.utilisateur'
        ]);

        return response()->json([
            'success' => true,
            'data' => new TacheResource($tache),
        ]);
    }

    /**
     * Update the specified resource in storage.
     * modifier une tâche
     */
    public function update(UpdateTacheRequest $request, Tache $tache)
    {
        $ancienAssigne = $tache->assigned_to;
        $ancienStatut = $tache->statut;

        if ($request->filled('assigned_to')) {
            $projet = $tache->projet;

            if (!$projet->membres()->where('user_id', $request->assigned_to)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cet utilisateur ne fait pas partie du projet.'
                ], 422);
            }
        }

        $tache->update($request->validated());
        AuditLogService::enregistrer(
        auth()->id(),
        'Modification',
        'Tâche',
        $tache->id,
        "Modification de la tâche {$tache->titre}."
        );

        if ($ancienAssigne != $tache->assigned_to && $tache->assigned_to) {
            NotificationService::envoyer(
                $tache->assigned_to,
                NotificationType::TACHE,
                "Une nouvelle tâche vous a été assignée : {$tache->titre}.",
                "/taches/".$tache->id
            );
        }

        if ($ancienStatut != $tache->statut) {
            NotificationService::envoyer(
                $tache->projet->user_id, // ← voir point 3
                NotificationType::TACHE,
                auth()->user()->prenom." a changé le statut de la tâche « {$tache->titre} » en {$tache->statut}.",
                "/taches/".$tache->id
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Tâche modifiée avec succès.',
            'data' => new TacheResource(
                $tache->fresh()->load('projet', 'assignee')
            )
        ]);
    }

    /**
     * Remove the specified resource from storage.
     * delete a task
     */
    public function destroy(Tache $tache)
    {
        $tache->delete();
        AuditLogService::enregistrer
        (
        auth()->id(),
        'Suppresion',
        'Tâche',
        $tache->id,
        "Suppression de la tâche {$tache->titre}."
        );

        return response()->json([
            'success' => true,
            'message' => 'Tâche supprimée avec succès.',
        ]);
    }
    public function assigner(AssignerTacheRequest $request,Tache $tache)
    
    {
        $projet = $tache->projet;

        if (!$projet->membres()->where('user_id', $request->assigned_to)->exists()) {

            return response()->json([
                'success' => false,
                'message' => 'Cet utilisateur ne fait pas partie du projet.'
            ], 422);
        }

        $tache->update([
            'assigned_to' => $request->assigned_to,
        ]);

            Notification::create([
        'user_id' => $request->assigned_to,
        'type' => 'Assignation de tâche',
        'contenu' => "Une nouvelle tâche vous a été assignée : {$tache->titre}",
        'lu' => false,
        
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tâche assignée avec succès.',
            'data' => new UserResource(
                $tache->assignee()->with('roles')->first()
            )
        ]);
    }
    public function changerStatut(ChangerStatutTacheRequest $request, Tache $tache)
    {
        $ancien = $tache->statut;

        $tache->update([
            'statut' => $request->statut,
        ]);

        HistoriqueStatut::create([
            'tache_id' => $tache->id,
            'user_id' => auth()->id(),
            'ancien_statut' => $ancien,
            'nouveau_statut' => $request->statut,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Statut modifié avec succès.',
            'data' => new TacheResource($tache->fresh()),
        ]);
    }
}
