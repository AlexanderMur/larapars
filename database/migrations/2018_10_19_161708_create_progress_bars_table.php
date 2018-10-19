<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgressBarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * @see \App\Models\ProgressBar
         */
        Schema::create('progress_bars', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parser_task_id');
            $table->integer('progress')->default(0);
            $table->integer('progress_max');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('progress_bars');
    }
}
