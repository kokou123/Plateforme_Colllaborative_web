<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EtapeProcessus extends Model
{
    protected $fillable = 
    [
        'nom',
        'user_id',
        'ordre',
        'description',
        'processus_id'
    ];
    public function processus()
    {
        return $this->belongsTo(Processus::class);
    }
    public function validateur()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
