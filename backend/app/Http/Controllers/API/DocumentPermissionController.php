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

    /**
     * Liste des permissions d'un document.
     */
    public function index()
    {
        return DocumentPermissionResource::collection(

            DocumentPermission::with('utilisateur','document')->get()

        );
    }

    /**
     * Ajouter une permission.
     */
    public function store(StoreDocumentPermissionRequest $request)
    {
        $permission = DocumentPermission::create(

            $request->validated()

        );

        return response()->json([

            'success'=>true,

            'message'=>'Permission ajoutée.',

            'data'=>new DocumentPermissionResource($permission)

        ],201);
    }

    /**
     * Modifier un rôle.
     */
    public function update(
    UpdateDocumentPermissionRequest $request, 
    DocumentPermission $documentPermission)
    {
        $documentPermission->update(

            $request->validated()

        );

        return response()->json([

            'success'=>true,

            'message'=>'Permission mise à jour.',

            'data'=>new DocumentPermissionResource($documentPermission)

        ]);
    }
    /**
     * Supprimer une permission.
     */
    public function destroy(DocumentPermission $documentPermission)
    {
        $documentPermission->delete();

        return response()->json([

            'success'=>true,

            'message'=>'Permission supprimée.'

        ]);
    }
    public function show(DocumentPermission $documentPermission)
    {
        return new DocumentPermissionResource(

            $documentPermission->load('utilisateur','document')

        );
    }

}