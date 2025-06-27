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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->text('bio')->nullable();
            $table->enum('mode', ['dark', 'light', 'device_mode'])->nullable()->default('light');
            $table->enum('sound_effects', ['on', 'off'])->nullable()->default('on');
            $table->string('language')->nullable()->default('en');
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['Male', 'Female', 'Non-binary', 'Prefer not to say'])->nullable();
            $table->foreignId('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
