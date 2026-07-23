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
    /**
     * Liste des documents.
     */
    public function index()
    {
        $documents = Document::with([
            'projet',
            'utilisateur',
            'versions'
        ])->get();

        return response()->json([
            'success' => true,
            'data' => DocumentResource::collection($documents)
        ]);
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
}