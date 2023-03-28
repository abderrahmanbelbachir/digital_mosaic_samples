<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('fullName')->nullable();
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('address')->nullable();
            $table->string('mobile')->nullable();
            $table->string('country')->nullable();
            $table->string('accountType')->nullable();
            $table->string('wilaya')->nullable();
            $table->unsignedMediumInteger('wilayaCode')->nullable();
            $table->string('commune')->nullable();
            $table->unsignedMediumInteger('communeId')->nullable();
            $table->string('picture')->nullable();
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
            $table->boolean('isValidated')->default(0);
            $table->json('addressList')->nullable();
            $table->json('reviews')->nullable();
            $table->unsignedFloat('ratingAverage')->nullable();
            $table->unsignedFloat('deliveryPrice')->nullable();
            $table->unsignedMediumInteger('codeCommune')->nullable();
            $table->boolean('hasFreeDelivery')->default(0);
            $table->string('firebaseId')->nullable();

            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
