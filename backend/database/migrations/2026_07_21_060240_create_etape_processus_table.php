<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('etape_processus', function (Blueprint $table) {

        $table->id();

        $table->foreignId('processus_id')
            ->constrained('processus')   // <-- nom de table précisé
            ->cascadeOnDelete();

        $table->string('nom');

        $table->integer('ordre');

        $table->enum('statut', [
            'en_attente',
            'en_cours',
            'terminee'
        ])->default('en_attente');

        $table->date('date_debut')->nullable();

        $table->date('date_fin')->nullable();

        $table->text('commentaire')->nullable();

        $table->timestamps();

    });

       
    }

    public function down(): void
    {
        Schema::dropIfExists('etape_processus');
    }
};