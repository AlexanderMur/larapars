<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyDonorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * @see \App\Models\CompanyDonor
         */
        Schema::create('company_donor', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id');
            $table->integer('donor_id');
            $table->string('site')->nullable();
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
        Schema::dropIfExists('company_donor');
    }
}
