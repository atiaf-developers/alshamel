<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVehiclesAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicles_ads', function (Blueprint $table) {
            $table->increments('id');

            $table->decimal('price',11,2);
            $table->boolean('status');
            $table->integer('manufacturing_year');
            
            $table->integer('motion_vector_id')->unsigned();
            $table->foreign('motion_vector_id')->references('id')->on('basic_data');

            $table->integer('engine_capacity_id')->unsigned();
            $table->foreign('engine_capacity_id')->references('id')->on('basic_data');


            $table->integer('propulsion_system_id')->unsigned();
            $table->foreign('propulsion_system_id')->references('id')->on('basic_data');


            $table->integer('fuel_type_id')->unsigned();
            $table->foreign('fuel_type_id')->references('id')->on('basic_data');

            $table->integer('mileage_id')->unsigned();
            $table->foreign('mileage_id')->references('id')->on('basic_data');
            
            $table->integer('mileage_unit');
             

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
        Schema::dropIfExists('vehicles_ads');
    }
}
