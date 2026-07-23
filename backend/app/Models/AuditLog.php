<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [

    'user_id',

    'action',

    'module',

    'element_id',

    'description',

    'adresse_ip',

    'user_agent'

    ];
    
    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
