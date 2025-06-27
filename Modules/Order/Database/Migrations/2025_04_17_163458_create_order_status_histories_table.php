<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Order\Enums\OrderStatusEnum;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_product_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('changer_type');
            $table->unsignedBigInteger('changer_id');
            // Add index for faster queries
            $table->index(['changer_type', 'changer_id']);
            $table->tinyInteger('status_from')->index();
            $table->tinyInteger('status_to')->index();

            $table->text('comment')->nullable();
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
        Schema::dropIfExists('order_status_histories');
    }
};
