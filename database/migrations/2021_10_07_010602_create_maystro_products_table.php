<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaystroProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maystro_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('magasinId');
            $table->string('name' , 600);
            $table->unsignedBigInteger('placetta_id');
            $table->unsignedBigInteger('maystro_id')->nullable();
            $table->unique(['name' , 'magasinId']);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('magasinId')->references('id')->on('stores');
            $table->foreign('placetta_id')->references('id')->on('products');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('maystro_products');
    }
}
