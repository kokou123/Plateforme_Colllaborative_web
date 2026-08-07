<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Document extends Model
{
    protected $fillable = [

        'nom',

        'description',

        'chemin',

        'type',

        'taille',

        'projet_id',

        'user_id',

    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    // Le document appartient à un projet
    public function projet(): BelongsTo
    {
        return $this->belongsTo(Projet::class);
    }

    // Utilisateur ayant déposé le document
    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Historique des versions
    public function versions(): HasMany
    {
        return $this->hasMany(VersionDocument::class);
    }

    // Commentaires
    public function commentaires(): HasMany
    {
        return $this->hasMany(Commentaire::class);
    }

    // Permissions d'accès
    public function permissions(): HasMany
    {
        return $this->hasMany(DocumentPermission::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Méthodes
    |--------------------------------------------------------------------------
    */

    /**
     * Vérifie si un utilisateur possède une permission.
     */
    public function utilisateurPossedePermission(
        int $userId,
        string $permission
    ): bool {

        // Le chef de projet possède tous les droits
        if ($this->projet->user_id == $userId) {
            return true;
        }

        $permissionDocument = $this->permissions()
            ->where('user_id', $userId)
            ->first();

        if (!$permissionDocument) {
            return false;
        }

        return match ($permission) {

            'lecture' => $permissionDocument->lecture,

            'ecriture' => $permissionDocument->ecriture,

            'suppression' => $permissionDocument->suppression,

            default => false,

        };
    }

    /**
     * Vérifie si un utilisateur appartient au projet.
     */
    public function utilisateurEstMembre(int $userId): bool
    {
        return $this->projet
            ->membres()
            ->where('users.id', $userId)
            ->exists();
    }

    /**
     * Retourne la dernière version.
     */
    public function derniereVersion()
    {
        return $this->versions()
            ->orderByDesc('numero')
            ->first();
    }
}