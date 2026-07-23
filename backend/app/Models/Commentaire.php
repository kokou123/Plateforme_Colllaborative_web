<?php

/*namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commentaire extends Model
{
    protected $fillable = [
        'nom',
        'description',
        'contenu',
        'user_id',
        'tache_id'
    ];
    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function tache()
    {
        return $this->belongsTo(Tache::class, 'tache_id');
    }
}*/


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Commentaire extends Model
{
    protected $fillable = ['contenu', 'user_id','tache_id', 'parent_id'];

    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tache()
    {
        return $this->belongsTo(Tache::class);
    }
    public function parent()
    {
        return $this->belongsTo(Commentaire::Class, 'parent_id');
    }
    public function reponses()
    {
        return $this->hasMany(Commentaire::Class, 'parent_id');
    }
    
}

