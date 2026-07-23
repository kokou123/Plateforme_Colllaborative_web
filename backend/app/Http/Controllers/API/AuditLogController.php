<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuditLogResource;
use App\Models\AuditLog;
use Illuminate\Http\JsonResponse;

class AuditLogController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => AuditLogResource::collection(
                AuditLog::with('utilisateur')
                    ->latest()
                    ->paginate(20)
            )
        ]);
    }

    public function show(AuditLog $auditLog): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new AuditLogResource(
                $auditLog->load('utilisateur')
            )
        ]);
    }

    public function destroy(AuditLog $auditLog): JsonResponse
    {
        $auditLog->delete();

        return response()->json([
            'success' => true,
            'message' => 'Audit supprimé avec succès.'
        ]);
    }
}