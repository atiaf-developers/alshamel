<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRatingUsersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('rating_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('rating_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->foreign('rating_id')->references('id')->on('rating');
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('rating_users');
    }

}
