<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Http\Resources\DocumentResource;
use App\Models\Document;
use App\Models\VersionDocument;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Services\AuditLogService;

class DocumentController extends Controller
{
    private function estChefProjet(Document $document): bool
    {
        return $document->projet->user_id == auth()->id();
    }

    private function estProprietaire(Document $document): bool
    {
        return $document->user_id == auth()->id();
    }

    private function peutLire(Document $document): bool
    {
        if ($this->estChefProjet($document) || $this->estProprietaire($document)) return true;
        return $document->utilisateurPossedePermission(auth()->id(), 'lecture')
            || $document->utilisateurPossedePermission(auth()->id(), 'ecriture');
    }

    private function peutModifier(Document $document): bool
    {
        if ($this->estChefProjet($document) || $this->estProprietaire($document)) return true;
        return $document->utilisateurPossedePermission(auth()->id(), 'ecriture');
    }
    /**
     * Liste des documents.
     */
    // DocumentController::index()
    public function index()
    {
        $documents = Document::with(['projet', 'utilisateur', 'versions'])
            ->whereHas('projet.chefProjet', function ($q) {
                $q->where('entreprise_id', auth()->user()->entreprise_id);
            })
            ->get();

        return response()->json(['success' => true, 'data' => DocumentResource::collection($documents)]);
    }

    /**
     * Déposer un document.
     */
    public function store(StoreDocumentRequest $request)
    {
        $fichier = $request->file('document');

        $chemin = $fichier->store('documents', 'public');

        $document = Document::create([

            'nom' => $request->nom,

            'chemin' => $chemin,

            'type' => $fichier->getClientOriginalExtension(),

            'taille' => $fichier->getSize(),

            'projet_id' => $request->projet_id,

            'user_id' => auth()->id(),

        ]);

        VersionDocument::create([

        'document_id' => $document->id,

        'user_id' => auth()->id(),

        'numero' => 1,

        'chemin' => $chemin,

        'type' => $fichier->getClientOriginalExtension(),

        'taille' => $fichier->getSize(),

        'commentaire' => 'Version initiale',

        ]);

        AuditLogService::enregistrer(
        auth()->id(),
        'Ajout',
        'Document',
        $document->id,
        "Ajout du document {$document->nom}."
        );
        return response()->json([
            'success' => true,
            'message' => 'Document ajouté avec succès.',
            'data' => new DocumentResource(
                $document->load('projet', 'utilisateur')
            )
        ], 201);
    }

    /**
     * Modifier un document.
     */
    public function update(UpdateDocumentRequest $request, Document $document)
    {
        if (!$this->peutModifier($document)) {
            return response()->json(['message' => 'Vous n\'avez pas le droit de modifier ce document.'], 403);
        }
        $document->update($request->validated());

        AuditLogService::enregistrer(
        auth()->id(),
        'Modification',
        'Document',
        $document->id,
        "Modification du document {$document->nom}."
        );

        return response()->json([
            'success' => true,
            'message' => 'Document modifié.',
            'data' => new DocumentResource(
                $document->fresh()->load('projet', 'utilisateur')
            )
        ]);
    }

    /**
     * Supprimer un document.
     */
    public function destroy(Document $document)
    {   
        if (!$this->estChefProjet($document)) {
            return response()->json(['message' => 'Seul le chef de projet peut supprimer un document.'], 403);
        }
        if (Storage::disk('public')->exists($document->chemin)) {

            Storage::disk('public')->delete($document->chemin);
        }
        foreach ($document->versions as $version) {

        if (Storage::disk('public')->exists($version->chemin)) {

            Storage::disk('public')->delete($version->chemin);

        }
        }
        $document->delete();
        AuditLogService::enregistrer(
        auth()->id(),
        'Suppression',
        'Document',
        $document->id,
        "Suppression du document {$document->nom}."
        );

        return response()->json([
            'success' => true,
            'message' => 'Document supprimé avec succès.'
        ]);
    }

    /**
     * Télécharger un document.
     */
    public function download(Document $document)
    {
        if (!$this->peutLire($document)) {
            return response()->json(['message' => 'Accès non autorisé à ce document.'], 403);
        }
        if (!Storage::disk('public')->exists($document->chemin)) {

            return response()->json([
                'success' => false,
                'message' => 'Fichier introuvable.'
            ], 404);
        }

        return Storage::disk('public')->download(
            $document->chemin,
            $document->nom . '.' . $document->type
        );
    }
    public function show(Document $document)
    {
        if (!$this->peutLire($document)) {
            return response()->json(['message' => 'Accès non autorisé à ce document.'], 403);
        }
        return response()->json(['success' => true, 'data' => new DocumentResource($document->load('projet', 'utilisateur', 'versions'))]);
    }
}