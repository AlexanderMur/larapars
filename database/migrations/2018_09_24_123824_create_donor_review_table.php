<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDonorReviewTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * @see \App\Models\DonorReview
         */
        Schema::create('donor_review', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('review_id');
            $table->integer('donor_id');
            $table->string('site');
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
        Schema::dropIfExists('donor_review');
    }
}
