<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreVersionDocumentRequest;
use App\Http\Requests\UpdateVersionDocumentRequest;
use App\Http\Resources\VersionDocumentResource;
use App\Models\Document;
use App\Models\VersionDocument;
use Illuminate\Support\Facades\Storage;

class VersionDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     * lister les versions d'un doc
     */
    public function index()
    {
        $versions = VersionDocument::with([
            'document',
            'utilisateur'
        ])->get();

        return response()->json([
            'success'=>true,
            'data'=>VersionDocumentResource::collection($versions)
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVersionDocumentRequest $request)
    {
        $document = Document::findOrFail($request->document_id);

        // Calcul du prochain numéro de version
        $numero = ($document->versions()->max('numero') ?? 0) + 1;

        // Récupération du fichier
        $fichier = $request->file('document');

        // Stockage du fichier
        $chemin = $fichier->store('documents/versions', 'public');

        // Création de la nouvelle version
        $version = VersionDocument::create([

            'document_id' => $document->id,

            'user_id' => auth()->id(),

            'numero' => $numero,

            'chemin' => $chemin,

            'type' => $fichier->getClientOriginalExtension(),

            'taille' => $fichier->getSize(),

            'commentaire' => $request->commentaire,

        ]);
        

        // Mise à jour du document principal
        $document->update([

            'chemin' => $chemin,

            'type' => $fichier->getClientOriginalExtension(),

            'taille' => $fichier->getSize(),

        ]);

        AuditLogService::enregistrer(
        auth()->id(),
        'Nouvelle version',
        'VersionDocument',
        $version->id,
        "Création de la version {$version->numero}."
        );

        return response()->json([

            'success' => true,

            'message' => 'Nouvelle version créée avec succès.',

            'data' => new VersionDocumentResource(
                $version->load('document', 'utilisateur')
            ),

        ], 201);
    }
    /**
     * Display the specified resource.
     */
    public function show(VersionDocument $versionDocument)
    {
        return response()->json([
            'success'=>true,
            'data'=>new VersionDocumentResource(
                $versionDocument->load('document','utilisateur')
            )
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVersionDocumentRequest $request, VersionDocument $versionDocument)
    {
        $versionDocument->update($request->validated());

        return response()->json([
            'success'=>true,
            'message'=>'Version mise à jour.',
            'data'=>new VersionDocumentResource(
                $versionDocument->fresh()->load('document','utilisateur')
            )
        ]);
    }
    /**
     * Remove the specified resource from storage.
     */
    
    public function destroy(VersionDocument $versionDocument)
    {
        if(Storage::disk('public')->exists($versionDocument->chemin))
        {
            Storage::disk('public')->delete($versionDocument->chemin);
        }

        $versionDocument->delete();

        AuditLogService::enregistrer(
        auth()->id(),
        'Suppression Version',
        'VersionDocument',
        $version->id,
        "Suppression la version {$version->numero}."
        );

        return response()->json([
            'success'=>true,
            'message'=>'Version supprimée.'
        ]);
    }
    public function download(VersionDocument $versionDocument)
    {
        if(!Storage::disk('public')->exists($versionDocument->chemin))
        {
            return response()->json([
                'success'=>false,
                'message'=>'Fichier introuvable.'
            ],404);
        }

        return Storage::disk('public')->download(
            $versionDocument->chemin,
            'Version_'.$versionDocument->numero.'.'.$versionDocument->type
        );
    }
    public function restore(VersionDocument $versionDocument)
    {
        $document = $versionDocument->document;

        // Déterminer le prochain numéro de version
        $nouveauNumero = ($document->versions()->max('numero') ?? 0) + 1;

        // Vérifier que le fichier existe
        if (!Storage::disk('public')->exists($versionDocument->chemin)) {

            return response()->json([
                'success' => false,
                'message' => 'Le fichier de cette version est introuvable.'
            ], 404);
        }

        // Copier le fichier
        $extension = pathinfo($versionDocument->chemin, PATHINFO_EXTENSION);

        $nouveauChemin = 'documents/versions/' .
            uniqid('version_') . '.' . $extension;

        Storage::disk('public')->copy(
            $versionDocument->chemin,
            $nouveauChemin
        );

        // Créer une nouvelle version
        $nouvelleVersion = VersionDocument::create([

            'document_id' => $document->id,

            'user_id' => auth()->id(),

            'numero' => $nouveauNumero,

            'chemin' => $nouveauChemin,

            'type' => $versionDocument->type,

            'taille' => $versionDocument->taille,

            'commentaire' => 'Restauration de la version '.$versionDocument->numero,

        ]);

        return response()->json([
            'success' => true,
            'message' => 'Version restaurée avec succès.',
            'data' => new VersionDocumentResource(
                $nouvelleVersion->load('document', 'utilisateur')
            )
        ]);
    }
}
