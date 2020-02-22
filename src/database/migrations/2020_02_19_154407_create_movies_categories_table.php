<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMoviesCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movies_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('movie_id');
            $table->foreign('category_id')->references('id')->on('categories')
            ->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('movie_id')->references('id')->on('movies')
            ->onDelete('cascade')->onUpdate('cascade');
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
        Schema::disableForeignKeyConstraints();
        Schema::table('movies_categories', function (Blueprint $table)
        {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['movie_id']);


        });
        Schema::dropIfExists('movies_categories');
        Schema::enableForeignKeyConstraints();
    }
}
