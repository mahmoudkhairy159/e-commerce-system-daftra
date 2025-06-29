<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Product\Enums\ProductTypeEnum;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable()->unique();
            $table->string('image')->nullable();
            $table->text('video_url')->nullable();
            $table->tinyInteger('type')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('admins')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('admins')->onDelete('set null');
            $table->tinyInteger('status')->default(1);
            $table->unsignedInteger('position')->default(1);
            $table->timestamps();
            $table->softDeletes(); // Enable soft deletes


            $table->string("currency", 10)->default('LE'); // Add length limit
            $table->unsignedInteger("stock")->default(1);
            $table->double("price", 10, 2);
            $table->double("offer_price", 10, 2)->default(0);
            $table->tinyInteger('tax_rate')->default(0);
            $table->date('offer_start_date')->nullable();
            $table->date('offer_end_date')->nullable();
            $table->tinyInteger('approval_status')->default(0);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};