<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Constants\NotificationType;
use App\Services\NotificationService;
use App\Services\AuditLogService;

class NotificationController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notifications = Notification::with('utilisateur')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return response()->json([

            'success' => true,

            'data' => NotificationResource::collection($notifications),

        ]);
    }

   
    public function show(Notification $notification)
    {
        if ($notification->user_id != auth()->id()) {

            return response()->json([
                'success' => false,
                'message' => 'Accès refusé.'
            ],403);

        }

        return response()->json([

            'success' => true,

            'data' => new NotificationResource(
                $notification->load('utilisateur')
            )

        ]);
    }

   /* fonction marquer comme lue*/
   public function marquerCommeLue(Notification $notification)
    {
        if ($notification->user_id != auth()->id()) {

            return response()->json([
                'success' => false,
                'message' => 'Accès refusé.'
            ],403);

        }

        $notification->update([

            'lu' => true,

        ]);
        AuditLogService::enregistrer
        (
        auth()->id(),
        'Lecture',
        'Notification',
        $notification->id,
        "Notification marquée comme lue."
        );

        return response()->json([

            'success' => true,

            'message' => 'Notification marquée comme lue.',

        ]);
    }

    public function marquerToutesCommeLues()
    {
        Notification::where('user_id', auth()->id())

            ->where('lu', false)

            ->update([

                'lu' => true,

            ]);
        AuditLogService::enregistrer
        (
            auth()->id(),
            'Lecture',
            'Notification',
            null,
            "Toutes les notifications ont été marquées comme lues."
        );
        

        return response()->json([

            'success' => true,

            'message' => 'Toutes les notifications ont été marquées comme lues.',

        ]);
    }

    /**
     * Remove the specified resource from storage.
     * supprimer une notification
     */
    public function destroy(Notification $notification)
    {
        if ($notification->user_id != auth()->id()) {

            return response()->json([
                'success' => false,
                'message' => 'Accès refusé.'
            ],403);

        }

        $notification->delete();

        return response()->json([

            'success' => true,

            'message' => 'Notification supprimée.',

        ]);
    }
}
