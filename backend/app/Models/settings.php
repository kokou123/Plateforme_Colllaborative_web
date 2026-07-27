<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class settings extends Model
{
    protected $fillable=[
    'entreprise_id',
    'langue',
    'theme',
    'assistant'
    ];
}
