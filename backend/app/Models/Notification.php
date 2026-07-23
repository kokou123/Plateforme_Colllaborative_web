<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [

    'type',

    'contenu',

    'user_id',

    'lu',

    'lien'

    ];
    
    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function marquerCommeLu(): void
    {
        $this->update(['lu' => true]);
    }
}