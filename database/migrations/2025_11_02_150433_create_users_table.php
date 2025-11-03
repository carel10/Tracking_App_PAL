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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('full_name', 150);
            $table->string('email', 150)->unique();
            $table->text('password_hash')->nullable();
            $table->unsignedBigInteger('division_id');
            $table->string('status', 20);
            $table->string('sso_subject', 255)->nullable();
            $table->string('sso_issuer', 255)->nullable();
            $table->timestamps();

            $table->foreign('division_id')->references('id')->on('divisions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
