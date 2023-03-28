<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('userId');
            $table->json('products')->nullable();
            $table->unsignedFloat('totalPrice')->nullable();
            $table->unsignedFloat('deliveryPrice')->nullable();
            $table->unsignedFloat('totalWithDelivery')->nullable();
            $table->unsignedBigInteger('magasinId')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('userId')->references('id')->on('users');
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
        Schema::dropIfExists('cards');
    }
}
