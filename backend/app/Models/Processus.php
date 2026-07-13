<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Processus extends Model
{
    protected $fillable = 
    [
        'nom',
        'description'
        
    ];
    public function etapes()
    {
        return $this->hasMany(EtapeProcessus::class);
    }
}
