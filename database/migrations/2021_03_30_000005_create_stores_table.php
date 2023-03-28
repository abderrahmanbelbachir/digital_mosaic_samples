<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('userId');
            $table->string('title')->nullable();
            $table->string('picture')->nullable();
            $table->string('category')->nullable();
            $table->json('categories')->nullable();
            $table->string('wilaya')->nullable();
            $table->string('commune')->nullable();
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
            $table->string('mobile')->nullable();
            $table->string('address')->nullable();
            $table->string('registreCommerce')->nullable();
            $table->unsignedFloat('rate')->nullable();
            $table->string('country')->nullable();
            $table->boolean('delivery')->default(0);
            $table->string('deliveryType')->nullable();
            $table->string('storeType')->nullable();
            $table->boolean('isPublished')->default(0);
            $table->json('payments')->nullable();
            $table->json('reviews')->nullable();
            $table->unsignedFloat('ratingAverage')->nullable();
            $table->json('paymentAccounts')->nullable();
            $table->string('planType')->nullable();
            $table->unsignedFloat('planPrice')->nullable();
            $table->unsignedMediumInteger('homePlace')->nullable();
            $table->boolean('clickAndCollect')->default(0);
            $table->unsignedBigInteger('validatedAtTime')->nullable();
            $table->dateTime('validatedAt')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('userId')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stores');
    }
}
