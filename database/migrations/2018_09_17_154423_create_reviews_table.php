<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * @see \App\Models\Review
         */
        Schema::create('reviews', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id');
            $table->string('title')->nullable();
            $table->string('name')->nullable();
            $table->text('text')->nullable();
            $table->boolean('good')->nullable();
            $table->integer('group_id')->nullable();
            $table->string('donor_link')->nullable();
            $table->integer('donor_id')->nullable();
            $table->date('donor_created_at')->nullable();
            $table->date('trashed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reviews');
    }
}
