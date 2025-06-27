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
        Schema::create('linked_social_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('provider_id');
            $table->string('provider_name', 50); // Limit provider_name length to 50 characters
            $table->unsignedBigInteger('user_id')->index('idx_linked_social_accounts_user_id'); // Descriptive index name
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); // Foreign key constraint
            $table->timestamps();

            // Ensure unique combination of user_id and provider_name
            $table->unique(['user_id', 'provider_name'], 'idx_user_provider_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('linked_social_accounts');
    }
};
