<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEtapeProcessusRequest;
use App\Http\Requests\UpdateEtapeProcessusRequest;
use App\Http\Resources\EtapeProcessusResource;
use App\Models\EtapeProcessus;
use Illuminate\Http\JsonResponse;
use App\Services\AuditLogService;
use App\Services\NotificationService;
use App\Models\Processus;

class EtapeProcessusController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => EtapeProcessusResource::collection(
                EtapeProcessus::with('processus')->orderBy('ordre')->paginate(15)
            )
        ]);
    }

    public function store(StoreEtapeProcessusRequest $request): JsonResponse
    {
        $etape = EtapeProcessus::create($request->validated());

        AuditLogService::enregistrer(
        auth()->id(),
        'Création',
        'ÉtapeProcessus',
        $etape->id,
        "Création de l'étape {$etape->nom}."
        );

        return response()->json([
            'success' => true,
            'message' => 'Étape créée avec succès.',
            'data' => new EtapeProcessusResource(
                $etape->load('processus')
            )
        ],201);
    }

    public function show(EtapeProcessus $etapeProcessus): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new EtapeProcessusResource(
                $etapeProcessus->load('processus')
            )
        ]);
    }

    public function update(UpdateEtapeProcessusRequest $request, EtapeProcessus $etapeProcessus): JsonResponse
    {
        $etapeProcessus->update($request->validated());

        AuditLogService::enregistrer(
        auth()->id(),
        'Modification',
        'ÉtapeProcessus',
        $etape->id,
        "Modification de l'étape {$etape->nom}."
        );

        return response()->json([
            'success' => true,
            'message' => 'Étape modifiée avec succès.',
            'data' => new EtapeProcessusResource(
                $etapeProcessus->load('processus')
            )
        ]);
    }

    public function destroy(EtapeProcessus $etapeProcessus): JsonResponse
    {
        $etapeProcessus->delete();

        AuditLogService::enregistrer(
        auth()->id(),
        'Suppression',
        'ÉtapeProcessus',
        $etape->id,
        "Suppression de l'étape {$etape->nom}."
        );

        return response()->json([
            'success' => true,
            'message' => 'Étape supprimée avec succès.'
        ]);
    }
    public function terminer(EtapeProcessus $etapeProcessus)
    {
        // Terminer l'étape actuelle
        $etapeProcessus->update([
            'statut' => 'terminee',
            'date_fin' => now(),
        ]);

        AuditLogService::enregistrer(
        auth()->id(),
        'Validation étape',
        'ÉtapeProcessus',
        $etapeProcessus->id,
        "L'étape {$etapeProcessus->nom} a été terminée."
        );

        // Étape suivante
        $suivante = EtapeProcessus::where('processus_id', $etapeProcessus->processus_id)
            ->where('ordre', $etapeProcessus->ordre + 1)
            ->first();

        if ($suivante) {

            $suivante->update([
                'statut' => 'en_cours',
                'date_debut' => now(),
            ]);

            NotificationService::envoyer(
                auth()->id(),
                'processus',
                "Le processus est passé à l'étape {$suivante->nom}.",
                "/processus/".$etapeProcessus->processus_id
            );

        } else {

            $processus = $etapeProcessus->processus;

            $processus->update([
                'statut' => 'termine'
            ]);

        }

        AuditLogService::enregistrer(
            auth()->id(),
            'Validation étape',
            'Processus',
            $etapeProcessus->id,
            "Étape {$etapeProcessus->nom} terminée."
        );

        return response()->json([
            'success' => true,
            'message' => 'Étape terminée avec succès.'
        ]);
    }
    
}