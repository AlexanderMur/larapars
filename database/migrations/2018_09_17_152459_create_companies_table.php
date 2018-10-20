<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * @see \App\Models\Company
         */
        Schema::create('companies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('phone')->nullable();
            $table->string('site')->nullable();
            $table->string('title')->nullable();
            $table->string('city')->nullable();
            $table->string('address')->nullable();
            $table->boolean('favourite')->default(false);
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
        Schema::dropIfExists('companies');
    }
}
