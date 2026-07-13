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
    Schema::table('users', function (Blueprint $table) {
        $table->foreignId('equipe_id')
              ->nullable()
              ->after('photo')
              ->constrained('equipes')
              ->nullOnDelete();
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropConstrainedForeignId('equipe_id');
    });
}
    

    /**
     * Reverse the migrations.
     */
   
 
};
