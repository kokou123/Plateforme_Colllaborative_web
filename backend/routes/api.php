<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\EquipeController;


    Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::apiResource('equipes', EquipeController::class);


    });
});

Route::middleware(['auth:sanctum', 'role:Administrateur'])->group(function () {
    Route::apiResource('users', UserController::class);

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
});
    // toutes tes autres routes protégées viendront ici

