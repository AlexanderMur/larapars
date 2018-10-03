<?php

use Illuminate\Database\Seeder;

class ParsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('parsers')->insert([
            'donor_id'            => 1,
            'loop_address'        => '.entry-summary p:last-child',
            'loop_item'           => 'main article',
            'loop_link'           => '.read-more a',
            'loop_title'          => 'h3',
            'replace_search'      => '</header><!-- .entry-header -->',
            'replace_to'          => '</div></header><!-- .entry-header -->',
            'reviews_all'         => 'main .full-testimonial',
            'reviews_ignore_text' => '... Читать полностью',
            'reviews_rating'      => '.stars',
            'reviews_text'        => '.rr_review_text',
            'reviews_title'       => 'h3',
            'reviews_name'        => '.rr_review_name span',
            'reviews_id'          => '',
            'single_site'         => 'noindex',
            'single_address'      => '#view1 + p',
            'single_tel'          => '',
        ]);

        DB::table('parsers')->insert([
            'donor_id'            => 2,
            'loop_address'        => '.su-post-excerpt',
            'loop_item'           => '.su-post',
            'loop_link'           => 'h2 a',
            'loop_title'          => 'h2',
            'replace_search'      => '',
            'replace_to'          => '',
            'reviews_all'         => '.comment-body',
            'reviews_ignore_text' => '',
            'reviews_rating'      => '.stars',
            'reviews_text'        => '.comment-content',
            'reviews_title'       => '',
            'reviews_name'        => '.fn',
            'reviews_id'          => './*/@id',
            'single_site'         => '.entry-content p > a',
            'single_address'      => '//div[@class="entry-content"]/p/text()[contains(.,"Адрес")]',
            'single_tel'          => '//div[@class="entry-content"]/p/text()[contains(.,"Тел")]',
        ]);
    }
}
