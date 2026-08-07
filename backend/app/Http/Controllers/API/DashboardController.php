<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Equipe;
use App\Models\Projet;
use App\Models\Tache;
use App\Models\Document;
use App\Models\VersionDocument;
use App\Models\Commentaire;
use App\Models\Notification;
use App\Models\Processus;
use App\Models\EtapeProcessus;
use App\Models\AuditLog;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function dashboard(): JsonResponse
    {
        $user = auth()->user();

        if ($user->hasRole('Admin')) {
            return $this->admin();
        }

        if ($user->hasRole('Chef de projet')) {
            return $this->chefProjet();
        }

        if ($user->hasRole('Employé')) {
            return $this->employe();
        }

        return response()->json([
            'success' => false,
            'message' => 'Aucun tableau de bord disponible pour ce rôle.'
        ], 403);
    }
    public function admin(): JsonResponse
    {
        return response()->json([

            'utilisateur' => [
                'id' => auth()->id(),
                'nom' => auth()->user()->nom,
                'prenom' => auth()->user()->prenom,
                'roles' => auth()->user()->getRoleNames(),
            ],
            'statistiques'=>[

                'utilisateurs'=>User::count(),

                'chefs_projet'=>User::role('Chef de projet')->count(),

                'employes'=>User::role('Employé')->count(),

                'equipes'=>Equipe::count(),

                'projets'=>Projet::count(),

                'taches'=>Tache::count(),

                'documents'=>Document::count(),

                'versions'=>VersionDocument::count(),

                'commentaires'=>Commentaire::count(),

                'notifications'=>Notification::count(),

                'processus'=>Processus::count(),

                'etapes'=>EtapeProcessus::count()

            ],

            'projets'=>[
                'en_attente'=>Projet::where('statut','À faire')->count(),
                'en_cours'=>Projet::where('statut','En cours')->count(),
                'termines'=>Projet::where('statut','Terminé')->count()
            ],
            'taches'=>[
                'en_attente'=>Tache::where('statut','À faire')->count(),
                'en_cours'=>Tache::where('statut','En cours')->count(),
                'terminees'=>Tache::where('statut','Terminée')->count()
            ],
            'notifications'=>[

                'lues'=>Notification::where('lu',true)->count(),

                'non_lues'=>Notification::where('lu',false)->count()

            ],

            'activites'=>AuditLog::with('utilisateur')

                ->latest()

                ->take(10)

                ->get()

        ]);
    }


    public function chefProjet(): JsonResponse
    {
        $chef = auth()->user();

        $projets = Projet::where('user_id', $chef->id)->pluck('id');    
        return response()->json([

            'utilisateur' => [
                'id' => auth()->id(),
                'nom' => auth()->user()->nom,
                'prenom' => auth()->user()->prenom,
                'roles' => auth()->user()->getRoleNames(),
            ],

            'mes_projets'=>Projet::whereIn('id',$projets)->count(),

            'mes_taches'=>Tache::whereIn('projet_id',$projets)->count(),

            'taches_en_attente'=>Tache::whereIn('projet_id',$projets)->where('statut','À faire')->count(),
            'taches_en_cours'=>Tache::whereIn('projet_id',$projets)->where('statut','En cours')->count(),
            'taches_terminees'=>Tache::whereIn('projet_id',$projets)->where('statut','Terminée')->count(),

            'documents'=>Document::whereIn('projet_id',$projets)->count(),

            'processus'=>Processus::whereIn('projet_id',$projets)->count(),

            'notifications'=>Notification::where('user_id',$chef->id)

                ->where('lu',false)

                ->count()

        ]);
    }

    public function employe(): JsonResponse
    {
        $user = auth()->user();

        return response()->json([

            'utilisateur' => [
                'id' => auth()->id(),
                'nom' => auth()->user()->nom,
                'prenom' => auth()->user()->prenom,
                'roles' => auth()->user()->getRoleNames(),
            ],

            'mes_taches'=>Tache::where('assigned_to',$user->id)->count(),

            'en_attente'=>Tache::where('assigned_to',$user->id)->where('statut','À faire')->count(),
            'en_cours'=>Tache::where('assigned_to',$user->id)->where('statut','En cours')->count(),
            'terminees'=>Tache::where('assigned_to',$user->id)->where('statut','Terminée')->count(),

            'notifications'=>Notification::where('user_id',$user->id)

                ->where('lu',false)

                ->count()

        ]);
    }
}