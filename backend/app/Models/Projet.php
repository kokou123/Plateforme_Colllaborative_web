<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Projet extends Model
{
    protected $fillable = ['nom', 'description', 'date_debut', 'date_fin', 'statut', 'user_id'];

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
    public function chefProjet()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function processus()
    {
        return $this->hasOne(Processus::class);
    }
}
