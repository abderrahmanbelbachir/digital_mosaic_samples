<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBookColumnsToProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('isBook')->nullable()->default(false);
            $table->string('type')->nullable();
            $table->longText('summary')->nullable();
            $table->string('author')->nullable();
            $table->string('language')->nullable();
            $table->string('maisonEdition')->nullable();
            $table->mediumInteger('totalPages')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('isBook');
            $table->dropColumn('type');
            $table->dropColumn('summary');
            $table->dropColumn('author');
            $table->dropColumn('language');
            $table->dropColumn('maisonEdition');
            $table->dropColumn('totalPages');

        });
    }
}
