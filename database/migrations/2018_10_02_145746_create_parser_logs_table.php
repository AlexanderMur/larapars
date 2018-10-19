<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParserLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parser_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->text('url')->nullable();
            $table->text('message');
            $table->string('status')->nullable();
            $table->text('details')->nullable();
            $table->integer('donor_id')->nullable();
            $table->integer('parsed_company_id')->nullable();
            $table->string('type')->nullable();
            $table->integer('parser_task_id')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parser_logs');
    }
}
