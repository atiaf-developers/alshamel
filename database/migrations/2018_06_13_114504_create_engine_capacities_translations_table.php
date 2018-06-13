<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEngineCapacitiesTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('engine_capacities_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->char('locale', 2);
            $table->string('title', 255);
            $table->integer('engine_capacity_id')->unsigned();
            $table->foreign('engine_capacity_id')->references('id')->on('engine_capacities');
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
        Schema::dropIfExists('engine_capacities_translations');
    }
}
