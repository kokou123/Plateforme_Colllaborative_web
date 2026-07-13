<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('taches', function (Blueprint $table) {

    $table->id();

    $table->foreignId('projet_id')
          ->constrained()
          ->cascadeOnDelete();

    $table->foreignId('assigned_to')
          ->constrained('users')
          ->cascadeOnDelete();

    $table->string('titre');

    $table->text('description')->nullable();

    $table->enum('priorite', [
        'faible',
        'moyenne',
        'haute'
    ])->default('moyenne');

    $table->enum('statut', [
        'a_faire',
        'en_cours',
        'terminee'
    ])->default('a_faire');

    $table->date('date_debut')->nullable();
    $table->date('date_fin')->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taches');
    }
};
