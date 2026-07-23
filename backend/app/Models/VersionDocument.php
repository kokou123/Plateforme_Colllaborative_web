<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VersionDocument extends Model
{
    protected $fillable = [

    'document_id',

    'user_id',

    'numero',

    'chemin',

    'taille',

    'type',

    'commentaire',

    ];
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function utilisateur()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
