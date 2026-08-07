<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocumentPermissionRequest;
use App\Http\Requests\UpdateDocumentPermissionRequest;
use App\Http\Resources\DocumentPermissionResource;
use App\Models\Document;
use App\Models\DocumentPermission;
use App\Models\User;

class DocumentPermissionController extends Controller
{
    public function store(StoreDocumentPermissionRequest $request)
    {
        $document = Document::with('projet')->findOrFail($request->document_id);
        $user = auth()->user();

        $estChef = $document->projet->user_id == $user->id;
        $estProprietaire = $document->user_id == $user->id;

        if (!$estChef && !$estProprietaire) {
            return response()->json([
                'message' => 'Seul le déposant du document ou le chef de projet peut accorder des permissions.'
            ], 403);
        }

        if ($request->user_id == $document->projet->user_id) {
            return response()->json([
                'message' => 'Le chef de projet possède déjà tous les droits sur ce document.'
            ], 422);
        }

        $permission = DocumentPermission::create([
            'document_id' => $document->id,
            'user_id' => $request->user_id,
            'lecture' => $request->boolean('lecture'),
            'ecriture' => $request->boolean('ecriture'),
            'suppression' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permission ajoutée.',
            'data' => new DocumentPermissionResource($permission->load('utilisateur', 'document'))
        ], 201);
    }

    public function update(UpdateDocumentPermissionRequest $request, DocumentPermission $documentPermission)
    {
        $document = $documentPermission->document->load('projet');
        $user = auth()->user();
        $estChef = $document->projet->user_id == $user->id;
        $estProprietaire = $document->user_id == $user->id;

        if (!$estChef && !$estProprietaire) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        $documentPermission->update([
            'lecture' => $request->boolean('lecture'),
            'ecriture' => $request->boolean('ecriture'),
            'suppression' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permission mise à jour.',
            'data' => new DocumentPermissionResource($documentPermission->fresh())
        ]);
    }

    public function destroy(DocumentPermission $documentPermission)
    {
        $document = $documentPermission->document->load('projet');
        $user = auth()->user();
        $estChef = $document->projet->user_id == $user->id;
        $estProprietaire = $document->user_id == $user->id;

        if (!$estChef && !$estProprietaire) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        $documentPermission->delete();
        return response()->json(['success' => true, 'message' => 'Permission supprimée.']);
    }
}