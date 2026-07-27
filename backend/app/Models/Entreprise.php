<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entreprise extends Model
{
    protected $fillable=[

        'nom',
        'secteur',
        'taille',
        'email',
        'telephone',
        'adresse',
        'logo',
        'active'

    ];

    public function utilisateurs()
    {
        return $this->hasMany(User::class);
    }
}