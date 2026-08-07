<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
        'photo',
        'equipe_id',
        'entreprise_id',
        'email_verifie',
        'otp',
        'otp_expire_at',
        'invitation_token',        
        'invitation_expire_at',
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

        public function equipe()
        {
            return $this->belongsTo(Equipe::class);
        }
        public function projets()
        {
            return $this->belongsToMany(Projet::class);
        }
        public function projetsCrees()
        {
            return $this->hasMany(Projet::class, 'user_id'); 
        }
        public function tachesAssignees()
        {
            return $this->hasMany(Tache::class, 'assigned_to');
        }

        public function documents()
        {
            return $this->hasMany(Document::class);
        }

        public function commentaires()
        {
            return $this->hasMany(Commentaire::class);
        }

        public function notifications()
        {
            return $this->hasMany(Notification::class);
        }

        public function auditLogs()
        {
            return $this->hasMany(AuditLog::class);
        }
        public function historiques()
        {
            return $this->hasMany(HistoriqueStatut::class);
        }
        public function permissions() 
        {
            return $this->hasMany(DocumentPermission::class);
        }
        public function entreprise()
        {
            return $this->belongsTo(Entreprise::class);
        }
        
}