<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoriqueStatut extends Model
{
    protected $fillable = [

        'tache_id',

        'user_id',

        'ancien_statut',

        'nouveau_statut',

    ];

    public function tache()
    {
        return $this->belongsTo(Tache::class);
    }

    public function utilisateur()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}