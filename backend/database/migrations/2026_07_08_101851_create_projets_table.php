<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projets', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('chef_projet_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('nom');

            $table->text('description')->nullable();

            $table->date('date_debut')->nullable();

            $table->date('date_fin')->nullable();

            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('projets');
    }
};