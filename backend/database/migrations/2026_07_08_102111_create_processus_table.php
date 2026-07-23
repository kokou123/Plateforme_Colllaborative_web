<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('processus', function (Blueprint $table) {

            $table->id();

            $table->foreignId('projet_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('nom');

            $table->text('description')->nullable();

            $table->enum('statut', [
                'en_attente',
                'en_cours',
                'termine'
            ])->default('en_attente');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('processus');
    }
};