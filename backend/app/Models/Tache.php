<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tache extends Model
{
    protected $fillable = [
        'titre',
        'description',
        'date_debut',
        'date_fin',
        'priorite',
        'statut',
        'projet_id',
        'assigned_to',
    ];
    public function projet() 
    {
        return $this->belongsTo(Projet::class);
    }
    public function utilisateur()
    {
        return $this->belongsTo(User::class);
    }
    public function commentaires()
    {
        return $this->hasMany(Commentaire::class);
    }
}
