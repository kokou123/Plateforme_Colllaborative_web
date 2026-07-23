<?php

namespace App\Services;

use App\Models\Notification;

class NotificationService
{
    /**
     * Envoyer une notification à un utilisateur.
     */
    public static function envoyer(
        int $userId,
        string $type,
        string $contenu,
        ?string $lien = null
    ): Notification {

        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'contenu' => $contenu,
            'lien' => $lien,
            'lu' => false,
        ]);
    }

    /**
     * Envoyer une notification à plusieurs utilisateurs.
     */
    public static function envoyerAuxUtilisateurs(
        array $userIds,
        string $type,
        string $contenu,
        ?string $lien = null
    ): void {

        foreach ($userIds as $userId) {

            self::envoyer(
                $userId,
                $type,
                $contenu,
                $lien
            );

        }

    }

    /**
     * Marquer une notification comme lue.
     */
    public static function marquerCommeLue(Notification $notification): void
    {
        $notification->update([
            'lu' => true
        ]);
    }

    /**
     * Marquer toutes les notifications d'un utilisateur comme lues.
     */
    public static function marquerToutesCommeLues(int $userId): void
    {
        Notification::where('user_id', $userId)
            ->where('lu', false)
            ->update([
                'lu' => true
            ]);
    }
}