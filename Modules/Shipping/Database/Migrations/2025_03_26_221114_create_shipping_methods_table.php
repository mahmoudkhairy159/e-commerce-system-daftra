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
        Schema::create('shipping_methods', function (Blueprint $table) {
                $table->id();
                $table->string('type')->nullable();
                $table->decimal('flat_rate', 10, 2)->default(0);
                $table->decimal('per_km_rate', 10, 2)->default(0);
                $table->decimal('max_distance', 10, 2)->default(50);
                $table->tinyInteger('status')->default(1);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->foreign('created_by')->references('id')->on('admins')->onDelete('set null');
                $table->foreign('updated_by')->references('id')->on('admins')->onDelete('set null');
                $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shipping_methods');
    }
};
