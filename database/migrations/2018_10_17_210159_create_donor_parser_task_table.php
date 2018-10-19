<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDonorParserTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('donor_parser_task', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('donor_id');
            $table->integer('parser_task_id');
            $table->integer('new_parsed_companies_count');
            $table->integer('updated_companies_count');
            $table->integer('new_reviews_count');
            $table->integer('deleted_reviews_count');
            $table->integer('restored_reviews_count');
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
        Schema::dropIfExists('donor_parser_task');
    }
}
