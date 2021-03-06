<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->integer('parsed_company_id')->nullable();
            $table->text('title')->nullable();
            $table->string('name')->nullable();
            $table->text('text')->nullable();
            $table->boolean('good')->nullable();
            $table->integer('group_id')->nullable();
            $table->text('donor_link')->nullable();
            $table->integer('donor_id')->nullable();
            $table->string('donor_comment_id')->nullable();
            $table->date('donor_created_at')->nullable();
            $table->date('trashed_at')->nullable();
            $table->timestamp('rated_at')->nullable();
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
