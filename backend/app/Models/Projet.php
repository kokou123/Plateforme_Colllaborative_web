<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Projet extends Model
{
    protected $fillable = ['nom', 'description', 'date_debut', 'date_fin', 'chef_projet_id', 'statut'];

    public function chef_de_projet()
    {
        return $this->belongsTo(User::class, 'chef_projet_id');
    }

    public function taches()
    {
        return $this->hasMany(Tache::class);
    }
    public function membres()
    {
        return $this->belongsToMany(User::class);
    }
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    
}
