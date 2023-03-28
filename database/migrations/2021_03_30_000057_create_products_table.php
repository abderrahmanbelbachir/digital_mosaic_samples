<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('magasinId');
            $table->string('name')->nullable();
            $table->string('category')->nullable();
            $table->longText('description')->nullable();
            $table->json('subCategory')->nullable();
            $table->float('price' , 14 , 2);
            $table->unsignedBigInteger('stockDispo')->nullable();
            $table->unsignedBigInteger('cardQuantity')->nullable();
            $table->unsignedFloat('cardPrice')->nullable();
            $table->unsignedBigInteger('orderQuantity')->nullable();
            $table->unsignedFloat('orderPrice')->nullable();
            $table->string('mark')->nullable();
            $table->json('pictures')->nullable();
            $table->boolean('isPublished')->default(0);
            $table->boolean('quantityOnOrder')->default(0);
            $table->json('reviews')->nullable();
            $table->unsignedFloat('ratingAverage')->nullable();
            $table->json('properties')->nullable();
            $table->json('cardProperties')->nullable();
            $table->json('discounts')->nullable();
            $table->unsignedMediumInteger('homePlace')->nullable();
            $table->unsignedMediumInteger('step')->nullable();
            $table->string('deliveredBy')->nullable();
            $table->dateTime('validatedAt')->nullable();
            $table->string('wilaya')->nullable();
            $table->string('firebaseId')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('magasinId')->references('id')->on('stores');
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
}
