<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBathesTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bathes_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->char('locale', 2);
            $table->string('title', 255);
            $table->integer('bath_id')->unsigned();
            $table->foreign('bath_id')->references('id')->on('bathes');
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
        Schema::dropIfExists('bathes_translations');
    }
}
