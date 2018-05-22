<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->increments('id');
            $table->text('image');
            $table->boolean('active');
            $table->integer('this_order');
            $table->integer('parent_id');
            $table->string('parents_ids');
            $table->integer('level');

            $table->integer('currency_id')->unsigned();
            $table->foreign('currency_id')->references('id')->on('currency');

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
        Schema::dropIfExists('locations');
    }
}
