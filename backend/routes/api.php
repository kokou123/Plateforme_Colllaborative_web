<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\EquipeController;
use App\Http\Controllers\API\ProjetController;
use App\Http\Controllers\API\DocumentController;
use App\Http\Controllers\API\DocumentPermissionController;
use App\Http\Controllers\API\VersionDocumentController;
use App\Http\Controllers\API\CommentaireController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\TacheController;
use App\Http\Controllers\API\ProcessusController;
use App\Http\Controllers\API\EtapeProcessusController;
use App\Http\Controllers\API\AuditLogController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\AI\AssistantController;
use App\Http\Controllers\API\EntrepriseController;
use App\Http\Controllers\API\Auth\VerifyEmailController;
use App\Http\Controllers\API\Auth\ForgotPasswordController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\SettingController;
use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\API\Auth\ActivationController;

Route::prefix('auth')->group(function () {

    Route::post('/register', [RegisterController::class,'register']);

    Route::post('/login', [AuthController::class,'login']);

    Route::post('/verify-email', [VerifyEmailController::class,'verify']);

    Route::post('/resend-otp', [VerifyEmailController::class,'resend']);

    Route::post('/forgot-password', [ForgotPasswordController::class,'envoyerOtp']);

    Route::post('/reset-password', [ForgotPasswordController::class,'reset']);


});

Route::get('/auth/activation/{token}', [ActivationController::class, 'check']);
Route::post('/auth/activation', [ActivationController::class, 'activate']);

Route::post('/entreprises',[EntrepriseController::class,'store']);

Route::middleware([
    'auth:sanctum',
    'verified.email',
    'entreprise.active'
])->group(function () {

    Route::get('/me',[AuthController::class,'me']);

    Route::post('/logout',[AuthController::class,'logout']);

    Route::get('/dashboard',[DashboardController::class,'dashboard']);

    Route::post('/assistant',[AssistantController::class,'chat']);

    Route::get('/profile',[ProfileController::class,'me']);

    Route::post('/profile',[ProfileController::class,'update']);

    Route::get('/settings',[SettingController::class,'index']);

    Route::put('/settings',[SettingController::class,'update']);

    Route::get('notifications',[NotificationController::class,'index']);

    Route::get('notifications/{notification}',[NotificationController::class,'show']);

    Route::patch('notifications/{notification}/lue',[NotificationController::class,'marquerCommeLue']);

    Route::patch('notifications/lire-toutes',[NotificationController::class,'marquerToutesCommeLues']);

    Route::delete('notifications/{notification}',[NotificationController::class,'destroy']);

    Route::apiResource('commentaires',CommentaireController::class);

    Route::get(
        'taches/{tache}/commentaires',
        [CommentaireController::class,'commentairesParTache']
    );

    Route::apiResource('documents',DocumentController::class);

    Route::get(
        'documents/{document}/download',
        [DocumentController::class,'download']
    );

    Route::apiResource('versions-documents',VersionDocumentController::class);

    Route::get(
        'versions-documents/{versionDocument}/download',
        [VersionDocumentController::class,'download']
    );

    Route::post(
        'versions-documents/{versionDocument}/restore',
        [VersionDocumentController::class,'restore']
    );
});

Route::middleware([
    'auth:sanctum',
    'verified.email',
    'entreprise.active',
    'role:Admin'
])->group(function () {

    Route::get('/dashboard/admin',[DashboardController::class,'admin']);

    Route::apiResource('users',UserController::class);

    Route::apiResource('equipes',EquipeController::class);

    Route::post(
        'equipes/{equipe}/membres',
        [EquipeController::class,'ajouterMembre']
    );

    Route::delete(
        'equipes/{equipe}/membres/{user}',
        [EquipeController::class,'retirerMembre']
    );

    Route::get(
        'equipes/{equipe}/membres',
        [EquipeController::class,'membres']
    );

    Route::get('audit-logs',[AuditLogController::class,'index']);

    Route::get('audit-logs/{auditLog}',[AuditLogController::class,'show']);

    Route::delete('audit-logs/{auditLog}',[AuditLogController::class,'destroy']);

    Route::get('/entreprise', [EntrepriseController::class, 'show']);
    
    Route::put('/entreprise', [EntrepriseController::class, 'update']);

});

Route::middleware([
    'auth:sanctum',
    'verified.email',
    'entreprise.active',
    'role:Chef de projet'
])->group(function () {

    Route::get('/dashboard/chef-projet',[DashboardController::class,'chefProjet']);

    Route::apiResource('projets',ProjetController::class);

    Route::post(
        'projets/{projet}/membres',
        [ProjetController::class,'ajouterMembre']
    );

    Route::delete(
        'projets/{projet}/membres/{user}',
        [ProjetController::class,'retirerMembre']
    );

    Route::get(
        'projets/{projet}/membres',
        [ProjetController::class,'membres']
    );

    Route::apiResource('taches',TacheController::class)
        ->parameters(['taches'=>'tache']);

    Route::patch(
        'taches/{tache}/assigner',
        [TacheController::class,'assigner']
    );

    Route::patch(
        'taches/{tache}/statut',
        [TacheController::class,'changerStatut']
    );

    Route::apiResource('document-permissions',DocumentPermissionController::class)
        ->only(['index','store','update','destroy']);

    Route::apiResource('processus',ProcessusController::class);

    Route::apiResource('etape-processus',EtapeProcessusController::class);

    Route::patch(
        'etape-processus/{etapeProcessus}/terminer',
        [EtapeProcessusController::class,'terminer']
    );

    Route::get('/employes', [UserController::class, 'employes']);

});

Route::middleware([
    'auth:sanctum',
    'verified.email',
    'entreprise.active',
    'role:Employé'
])->group(function () {

    Route::get(
        '/dashboard/employe',
        [DashboardController::class,'employe']
    );

});