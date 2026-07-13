<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VersionDocument extends Model
{
    protected $fillable = 
    [
        'numero',
        'fichier',
        'document_id'
    ];
    public function document() 
    {
        return $this->belongsTo(Document::class, 'document_id');
    }
}
