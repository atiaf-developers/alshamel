<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropertyTypesTranslationsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('property_types_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->char('locale', 2);
            $table->string('title', 255);
            $table->integer('property_type')->unsigned();
            $table->foreign('property_type')->references('id')->on('property_types');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('property_types_translations');
    }

}
