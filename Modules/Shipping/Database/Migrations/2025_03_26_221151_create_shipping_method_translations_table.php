<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::create('shipping_method_translations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('shipping_method_id');
                $table->string('locale');
                $table->string('title');
                $table->text('description')->nullable();
                $table->foreign('shipping_method_id')
                      ->references('id')
                      ->on('shipping_methods')
                      ->onDelete('cascade');

                $table->unique(['shipping_method_id', 'locale']);
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shipping_method_translations');
    }
};
