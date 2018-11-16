<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDonorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * @see \App\Models\Donor
         */
        Schema::create('donors', function (Blueprint $table) {
            $table->increments('id');
            $table->string('link');
            $table->string('title');

            $table->string('parser')->nullable();

            $table->boolean('mass_parsing')->default(1);

            //Archive page selector
            $table->string('loop_item')->nullable();
            $table->string('loop_title')->nullable();
            $table->string('loop_address')->nullable();
            $table->string('loop_link')->nullable();

            //Single page selectors
            $table->string('single_site')->nullable();
            $table->string('single_address')->nullable();
            $table->string('s_address_regex')->nullable();
            $table->string('single_tel')->nullable();
            $table->string('single_title')->nullable();
            $table->string('single_city')->nullable();
            $table->string('s_city_regex')->nullable();



            //Replace unclosed tags
            $table->string('replace_search')->nullable();
            $table->string('replace_to')->nullable();



            //Reviews selectors
            $table->string('reviews_all')->nullable();
            $table->string('reviews_title')->nullable();
            $table->string('reviews_text')->nullable();
            $table->string('reviews_rating')->nullable();
            $table->string('reviews_name')->nullable();
            $table->string('reviews_id')->nullable();
            $table->string('reviews_pagination')->nullable();

            //Убрать "Читать полностью..." у отзыва
            $table->string('reviews_ignore_text')->nullable();

            //Пагинация или любые ссылки куда ведут на архив
            $table->string('archive_pagination')->nullable();

            $table->boolean('decode_url')->nullable();

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
        Schema::dropIfExists('donors');
    }
}
