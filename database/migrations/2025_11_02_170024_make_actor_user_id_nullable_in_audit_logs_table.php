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
        Schema::table('audit_logs', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['actor_user_id']);
            // Make column nullable
            $table->unsignedBigInteger('actor_user_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            // Make column not nullable again
            $table->unsignedBigInteger('actor_user_id')->nullable(false)->change();
            // Re-add foreign key constraint
            $table->foreign('actor_user_id')->references('id')->on('users');
        });
    }
};
