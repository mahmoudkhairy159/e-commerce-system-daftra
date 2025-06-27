<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('default_address_id')->nullable()->after('blocked');


            $table->foreign('default_address_id')
                ->references('id')
                ->on('user_addresses')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove foreign key constraint first
            $table->dropForeign(['default_address_id']);

            // Then drop the column
            $table->dropColumn('default_address_id');
        });
    }
};