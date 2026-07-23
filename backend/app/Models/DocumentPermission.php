<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentPermission extends Model
{
    protected $fillable = [

        'document_id',

        'user_id',

        'lecture',

        'ecriture',

        'suppression',

    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function peutLire(): bool
    {
        return $this->lecture;
    }

    public function peutEcrire(): bool
    {
        return $this->ecriture;
    }

    public function peutSupprimer(): bool
    {
        return $this->suppression;
    }
}