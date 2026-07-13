<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = 
    [
        'action',
        'description',
        'user_id'
    ];
    
    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
