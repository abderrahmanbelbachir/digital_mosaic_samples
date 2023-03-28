<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('magasinId');
            $table->unsignedBigInteger('userId');
            $table->string('userName')->nullable();
            $table->unsignedFloat('totalPrice')->nullable();
            $table->boolean('delivred')->default(0);
            $table->dateTime('delivredAt')->nullable();
            $table->string('status')->nullable();
            $table->json('products');
            $table->dateTime('approvedAt')->nullable();
            $table->dateTime('rejectedAt')->nullable();
            $table->boolean('received')->default(0);
            $table->dateTime('receivedAt')->nullable();
            $table->boolean('canceled')->default(0);
            $table->dateTime('canceledAt')->nullable();
            $table->string('deliveryAddress')->nullable();
            $table->string('customerPhone')->nullable();
            $table->string('wilaya')->nullable();
            $table->string('commune')->nullable();
            $table->unsignedFloat('deliveryPrice')->nullable();
            $table->unsignedBigInteger('codeCommune')->nullable();
            $table->unsignedFloat('totalWithDeliveryPrice')->nullable();
            $table->string('delivredBy')->nullable();
            $table->string('maystroId')->nullable();
            $table->boolean('deliveryAborted')->default(0);
            $table->boolean('deliveryPostPoned')->default(0);
            $table->dateTime('deliveryAbortedAt')->nullable();
            $table->dateTime('deliveryPostponedAt')->nullable();
            $table->string('firebaseId')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('magasinId')->references('id')->on('stores');
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
        Schema::dropIfExists('orders');
    }
}
