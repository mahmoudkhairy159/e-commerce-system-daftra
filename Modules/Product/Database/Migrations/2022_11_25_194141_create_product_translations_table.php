<?php

use App\Enums\ProductTypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('locale')->index();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->text('seo_description')->nullable();
            $table->text('seo_keys')->nullable();
            $table->text("short_description")->nullable();
            $table->longText("long_description")->nullable();
            $table->json('additional')->nullable();
            $table->longText("return_policy")->nullable();


            $table->unique(['product_id', 'locale']);
            $table->index('slug');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_translations');
    }
};
