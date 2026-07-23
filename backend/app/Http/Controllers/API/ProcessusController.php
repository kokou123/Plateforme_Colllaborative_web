<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProcessusRequest;
use App\Http\Requests\UpdateProcessusRequest;
use App\Http\Resources\ProcessusResource;
use App\Models\Processus;
use Illuminate\Http\JsonResponse;
use App\Services\AuditLogService;

class ProcessusController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => ProcessusResource::collection(
                Processus::with(['projet','etapes'])->paginate(10)
            )
        ]);
    }

    public function store(StoreProcessusRequest $request): JsonResponse
    {
        $processus = Processus::create($request->validated());
        AuditLogService::enregistrer(
        auth()->id(),
        'Création',
        'Processus',
        $processus->id,
        "Création du processus {$processus->nom}."
        );

        return response()->json([
            'success' => true,
            'message' => 'Processus créé avec succès.',
            'data' => new ProcessusResource(
                $processus->load(['projet','etapes'])
            )
        ],201);
    }

    public function show(Processus $processus): JsonResponse
    {
        if (Processus::where('projet_id', $request->projet_id)->exists()) {
        return response()->json([
            'success' => false,
            'message' => 'Ce projet possède déjà un processus.'
        ], 422);
        }
        return response()->json([
            'success' => true,
            'data' => new ProcessusResource(
                $processus->load(['projet','etapes'])
            )
        ]);
    }

    public function update(UpdateProcessusRequest $request, Processus $processus): JsonResponse
    {
        $processus->update($request->validated());
        AuditLogService::enregistrer(
        auth()->id(),
        'Modification',
        'Processus',
        $processus->id,
        "Modification du processus {$processus->nom}."
        );

        return response()->json([
            'success' => true,
            'message' => 'Processus modifié avec succès.',
            'data' => new ProcessusResource(
                $processus->load(['projet','etapes'])
            )
        ]);
    }

    public function destroy(Processus $processus): JsonResponse
    {
        $processus->delete();

        AuditLogService::enregistrer(
        auth()->id(),
        'Suppression',
        'Processus',
        $processus->id,
        "Suppression du processus {$processus->nom}."
    );

        return response()->json([
            'success' => true,
            'message' => 'Processus supprimé avec succès.'
        ]);
    }
}