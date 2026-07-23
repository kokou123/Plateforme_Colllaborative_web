<?php

namespace App\Services;

use App\Models\AuditLog;

class AuditLogService
{
    public static function enregistrer(

        ?int $userId,

        string $action,

        string $module,

        ?int $elementId,

        string $description

    ): AuditLog {

        return AuditLog::create([

            'user_id' => $userId,

            'action' => $action,

            'module' => $module,

            'element_id' => $elementId,

            'description' => $description,

            'adresse_ip' => request()->ip(),

            'user_agent' => request()->userAgent()

        ]);

    }
}