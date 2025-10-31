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
        // Create divisions table first (custom schema)
        Schema::create('divisions', function (Blueprint $table) {
            $table->id('division_id');
            $table->string('division_name', 100)->unique();
        });

        // Create roles table
        Schema::create('roles', function (Blueprint $table) {
            $table->id('role_id');
            $table->string('role_name', 100)->unique();
            $table->text('role_description')->nullable();
            $table->datetime('created_at');
        });

        // Create permissions table (custom schema)
        Schema::create('permissions', function (Blueprint $table) {
            $table->id('permission_id');
            $table->string('permission_name', 150)->unique();
            $table->string('permission_code', 100)->unique();
            $table->string('category', 100)->nullable();
            // created_at only (no updated_at as per spec)
            $table->dateTime('created_at');
        });

        // Modify users table
        Schema::table('users', function (Blueprint $table) {
            // Drop unnecessary columns
            $table->dropColumn([
                'email_verified_at',
                'remember_token'
            ]);

            // Rename and modify columns
            $table->renameColumn('id', 'user_id');
            
            $table->renameColumn('name', 'username');
            $table->string('username', 100)->change();
            
            $table->renameColumn('password', 'password_hash');
            $table->string('password_hash', 255)->change();
            
            $table->string('email', 150)->change();

            // Add new columns
            $table->string('full_name', 200)->after('email');
            $table->foreignId('division_id')->after('full_name')->constrained('divisions', 'division_id');
            $table->foreignId('role_id')->after('division_id')->constrained('roles', 'role_id');
            $table->enum('status', ['active', 'inactive', 'pending'])->default('pending')->after('role_id');
            $table->dateTime('last_login')->nullable()->after('status');
        });

        // Create pivot table for roles <-> permissions (only FKs)
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained('roles', 'role_id')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('permissions', 'permission_id')->onDelete('cascade');
            // composite primary key to ensure uniqueness
            $table->primary(['role_id', 'permission_id']);
        });

        // Create activity log table (custom schema)
        Schema::create('user_activity_log', function (Blueprint $table) {
            $table->id('log_id');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->string('activity', 255);
            $table->string('ip_address', 50)->nullable();
            $table->text('user_agent')->nullable();
            $table->dateTime('timestamp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_activity_log');
        Schema::dropIfExists('role_permissions');
        
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['division_id']);
            $table->dropForeign(['role_id']);
            
            // Drop added columns
            $table->dropColumn([
                'division_id',
                'role_id',
                'status',
                'last_login',
                'full_name'
            ]);
            
            // Restore original columns
            $table->renameColumn('user_id', 'id');
            $table->renameColumn('password_hash', 'password');
            $table->renameColumn('username', 'name');
            
            // Add back removed columns
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();

            // Restore column lengths
            $table->string('name')->change();
            $table->string('email')->change();
            $table->string('password')->change();
        });

        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('divisions');
    }
};