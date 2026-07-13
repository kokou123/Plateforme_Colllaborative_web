<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Projet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DocumentController extends Controller
{
    // Liste des documents d'un projet
    public function index(Projet $projet)
    {
        $documents = $projet->documents()->with('utilisateur:id,nom,prenom')->latest()->get();

        return response()->json([
            'documents' => $documents,
        ]);
        
    }

    // Upload d'un nouveau document
    public function store(Request $request, Projet $projet)
    {
        $validator = Validator::make($request->all(), [
            'fichier' => ['required', 'file', 'max:10240'], // 10 Mo max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $fichier = $request->file('fichier');
        $chemin  = $fichier->store('documents/' . $projet->id, 'local');

        $document = Document::create([
            'nom'       => $fichier->getClientOriginalName(),
            'type'      => $fichier->getClientMimeType(),
            'taille'    => $fichier->getSize(),
            'chemin'    => $chemin,
            'projet_id' => $projet->id,
            'user_id'   => $request->user()->id,
        ]);

        return response()->json([
            'message'  => 'Document ajouté avec succès',
            'document' => $document,
        ], 201);
    }

    // Détails d'un document
    public function show(Document $document)
    {
        return response()->json([
            'document' => $document->load('utilisateur:id,nom,prenom', 'projet:id,nom'),
        ]);
    }

    // Téléchargement du fichier
    public function download(Document $document)
    {
        if (! Storage::disk('local')->exists($document->chemin)) {
            return response()->json([
                'message' => 'Fichier introuvable',
            ], 404);
        }

        return Storage::disk('local')->download($document->chemin, $document->nom);
    }

    // Suppression d'un document
    public function destroy(Document $document)
    {
        if (Storage::disk('local')->exists($document->chemin)) {
            Storage::disk('local')->delete($document->chemin);
        }

        $document->delete();

        return response()->json([
            'message' => 'Document supprimé avec succès',
        ]);
    }
}