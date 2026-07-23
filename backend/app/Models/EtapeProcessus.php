<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EtapeProcessus extends Model
{
    protected $table = 'etape_processus';

    protected $fillable = [

        'processus_id',

        'nom',

        'ordre',

        'statut',

        'date_debut',

        'date_fin',

        'commentaire'

    ];

    public function processus(): BelongsTo
    {
        return $this->belongsTo(Processus::class);
    }
}