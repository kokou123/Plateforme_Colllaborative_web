<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Processus extends Model
{
    protected $table = 'processus';

    protected $fillable = [

        'projet_id',

        'nom',

        'description',

        'statut'

    ];

    public function projet(): BelongsTo
    {
        return $this->belongsTo(Projet::class);
    }

    public function etapes(): HasMany
    {
        return $this->hasMany(EtapeProcessus::class)
                    ->orderBy('ordre');
    }
}