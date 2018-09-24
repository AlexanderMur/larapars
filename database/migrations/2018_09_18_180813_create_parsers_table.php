<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * @see \App\Parser
         */
        Schema::create('parsers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('donor_id')->nullable();
            $table->string('loop_item')->nullable();
            $table->string('loop_title')->nullable();
            $table->string('loop_address')->nullable();
            $table->string('loop_link')->nullable();
            $table->string('single_site')->nullable();
            $table->string('single_address')->nullable();
            $table->string('single_tel')->nullable();
            $table->string('replace_search')->nullable();
            $table->string('replace_to')->nullable();

            $table->string('reviews_ignore_text')->nullable();
            $table->string('reviews_all')->nullable();
            $table->string('reviews_title')->nullable();
            $table->string('reviews_text')->nullable();
            $table->string('reviews_rating')->nullable();
            $table->string('reviews_name')->nullable();
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
        Schema::dropIfExists('parsers');
    }
}
