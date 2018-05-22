<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ads', function (Blueprint $table) {
            $table->increments('id');

            $table->string('title', 255);
            $table->longText('details');
            $table->longText('images');
            $table->boolean('active');
            $table->double('lat', 11,8);
            $table->double('lng', 11,8);
            $table->string('email');
            $table->string('mobile');
            $table->boolean('special');
            $table->integer('rate');

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');

            $table->integer('category_one_id')->unsigned();
            $table->foreign('category_one_id')->references('id')->on('categories');

            $table->integer('category_two_id')->unsigned();
            $table->foreign('category_two_id')->references('id')->on('categories');

            $table->integer('category_three_id')->unsigned()->nullable();
            $table->foreign('category_three_id')->references('id')->on('categories');

            $table->integer('country_id')->unsigned();
            $table->foreign('country_id')->references('id')->on('locations');

            $table->integer('city_id')->unsigned();
            $table->foreign('city_id')->references('id')->on('locations');

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
        Schema::dropIfExists('ads');
    }
}
