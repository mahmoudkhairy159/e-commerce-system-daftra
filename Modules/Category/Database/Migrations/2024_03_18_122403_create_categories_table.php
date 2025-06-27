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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('image')->nullable();
            $table->string('code')->unique()->nullable();
            $table->foreignId('updated_by')->nullable();
            $table->foreignId('created_by')->nullable();
            $table->tinyInteger('status')->default(1)->index();
            $table->bigInteger('position')->default(1)->unsigned()->comment('Used for ordering categories');
            $table->unsignedBigInteger('parent_id')->nullable(); // Self-referencing column
            $table->foreign('parent_id')->references('id')->on('categories')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
            $table->index('parent_id');
            $table->index('position');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropForeign(['parent_id']);
            $table->dropIndex(['parent_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['position']);
            $table->dropSoftDeletes();
        });
        Schema::dropIfExists('categories');
    }
};
