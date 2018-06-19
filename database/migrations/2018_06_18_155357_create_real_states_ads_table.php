<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRealStatesAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('real_states_ads', function (Blueprint $table) {
            $table->increments('id');

            $table->decimal('price',11,2);
            $table->double('area',11,2);
            $table->boolean('is_furnished');
            $table->boolean('has_parking');

            $table->integer('property_type_id')->unsigned();
            $table->foreign('property_type_id')->references('id')->on('basic_data');

            $table->integer('rooms_id')->unsigned();
            $table->foreign('rooms_id')->references('id')->on('basic_data');

            $table->integer('bathes_id')->unsigned();
            $table->foreign('bathes_id')->references('id')->on('basic_data');

            $table->integer('ad_id')->unsigned();
            $table->foreign('ad_id')->references('id')->on('ads');


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
        Schema::dropIfExists('real_states_ads');
    }
}
