<?php
// 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('invitation_token')->nullable()->unique()->after('otp_expire_at');
            $table->timestamp('invitation_expire_at')->nullable()->after('invitation_token');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['invitation_token', 'invitation_expire_at']);
        });
    }
};