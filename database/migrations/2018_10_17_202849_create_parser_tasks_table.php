<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParserTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parser_tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('state')->nullable();
            $table->string('progress_now')->nullable();
            $table->string('type')->nullable();
            $table->string('progress_max')->nullable();
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
        Schema::dropIfExists('parser_tasks');
    }
}
