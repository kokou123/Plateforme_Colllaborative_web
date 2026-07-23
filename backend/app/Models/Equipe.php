<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipe extends Model
{
    protected $fillable = [
        'nom',
        'description',
        'equipe_id'
    ];
    public function utilisateurs()
    {
        return $this->hasMany(User::class, 'equipe_id');
    }
}
