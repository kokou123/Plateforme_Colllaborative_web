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
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    public function commentaires()
    {
        return $this->hasMany(Commentaire::class);
    }
    public function historiques()
    {
        return $this->hasMany(HistoriqueStatut::class);
    }
}
