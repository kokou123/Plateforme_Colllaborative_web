<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCommentaireRequest;
use App\Http\Requests\UpdateCommentaireRequest;
use App\Http\Resources\CommentaireResource;
use App\Models\Commentaire;
use App\Models\Tache;
use App\Services\AuditLogService;

class CommentaireController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $commentaires = Commentaire::with([
            'utilisateur',
            'tache',
            'reponses.utilisateur'
        ])
        ->whereNull('parent_id')
        ->latest()
        ->get();

        return response()->json([

            'success' => true,

            'data' => CommentaireResource::collection($commentaires)

        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommentaireRequest $request)
    {
        $tache = Tache::findOrFail($request->tache_id);

        if (!$tache->projet->membres()->where('user_id', auth()->id())->exists()) {

            return response()->json([
                'success' => false,
                'message' => 'Vous ne faites pas partie de ce projet.'
            ], 403);

        }
        if ($request->filled('parent_id')) {

            $parent = Commentaire::findOrFail($request->parent_id);

            if ($parent->tache_id != $tache->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le commentaire parent n’appartient pas à cette tâche.'
                ], 422);
            }
        }
        $commentaire = Commentaire::create([
            'contenu' => $request->contenu,
            'user_id' => auth()->id(),
            'tache_id' => $request->tache_id,
            'parent_id' => $request->parent_id,
        ]);
        AuditLogService::enregistrer(
        auth()->id(),
        'Création',
        'Commentaire',
        $commentaire->id,
        "Ajout d'un commentaire."
        );

        return response()->json([
            'success' => true,
            'message' => 'Commentaire ajouté.',
            'data' => new CommentaireResource(
                $commentaire->load('utilisateur', 'tache')
            )
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Commentaire $commentaire)
    {
        return response()->json([

            'success' => true,

            'data' => new CommentaireResource(

                $commentaire->load([
                    'utilisateur',
                    'tache',
                    'reponses.utilisateur'
                ])

            )

        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommentaireRequest $request, Commentaire $commentaire)
    {
        if ($commentaire->user_id != auth()->id()) {

            return response()->json([

                'success' => false,

                'message' => 'Vous ne pouvez modifier que vos propres commentaires.'

            ],403);

        }

        $commentaire->update($request->validated());
        AuditLogService::enregistrer(
        auth()->id(),
        'Modification',
        'Commentaire',
        $commentaire->id,
        "Modification d'un commentaire."
        );

        return response()->json([

            'success' => true,

            'message' => 'Commentaire modifié.',

            'data' => new CommentaireResource(

                $commentaire->fresh()->load('utilisateur','tache')

            )

        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Commentaire $commentaire)
    {
        if (
            $commentaire->user_id != auth()->id()
            && !auth()->user()->hasRole('Chef de projet')
        ) {

            return response()->json([

                'success' => false,

                'message' => 'Suppression refusée.'

            ],403);

        }

        $commentaire->delete();

        AuditLogService::enregistrer(
        auth()->id(),
        'Suppression',
        'Commentaire',
        $commentaire->id,
        "Suppression d'un commentaire."
        );

        return response()->json([

            'success' => true,

            'message' => 'Commentaire supprimé.'

        ]);
    }
    public function commentairesParTache(Tache $tache)
    {
        $commentaires = $tache->commentaires()
            ->whereNull('parent_id')
            ->with([
                'utilisateur',
                'reponses.utilisateur'
            ])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => CommentaireResource::collection($commentaires)
        ]);
    }
}
