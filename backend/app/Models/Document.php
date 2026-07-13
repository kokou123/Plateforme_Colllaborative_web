<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'nom',
        'type',
        'chemin',
        'taille',
        'projet_id',
        'user_id'   
    ];
    public function projet() 
    {
        return $this->belongsTo(Projet::class, 'projet_id');
    }
    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function versionDocument()
    {
        return $this->belongsToMany(VersionDocument::class);
    }
}
