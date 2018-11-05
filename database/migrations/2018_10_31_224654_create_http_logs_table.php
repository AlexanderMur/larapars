<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHttpLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('http_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->text('url');
            $table->integer('status')->nullable();
            $table->string('message')->nullable();
            $table->integer('donor_id');
            $table->integer('parser_task_id');
            $table->string('channel')->nullable();
            $table->timestamp('sent_at')->nullable();
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
        Schema::dropIfExists('http_logs');
    }
}
