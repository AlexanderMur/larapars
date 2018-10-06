<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParsedCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * @see \App\Models\ParsedCompany
         */
        Schema::create('parsed_companies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('phone')->nullable();
            $table->string('site')->nullable();
            $table->string('title')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->integer('company_id')->nullable();
            $table->text('donor_page')->nullable();
            $table->integer('donor_id')->nullable();
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
        Schema::dropIfExists('parsed_companies');
    }
}
