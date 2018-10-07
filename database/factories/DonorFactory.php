<?php

use Faker\Generator as Faker;

/** @var Illuminate\Database\Eloquent\Factory $factory */

$factory->defineAs(App\Models\Donor::class, 'donor1', function (Faker $faker) {
    return [
        'link'  => 'https://avtosalon-otzyv.ru/',
        'title' => 'Автосалон отзыв',

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
        'single_title'        => 'h2.entry-title',
    ];
});

$factory->defineAs(App\Models\Donor::class, 'donor2', function (Faker $faker) {
    return [
        'link'                => 'https://otziv-avto.ru/msk',
        'title'               => 'Отзыв авто',
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
        'single_address'      => '//div[@id="primary"]//*/text()[contains(.,"Адрес")]',
        'single_tel'          => '//div[@id="primary"]//*/text()[contains(.,"Тел")]',
        'single_title'        => 'h1.entry-title',
    ];
});
$factory->defineAs(App\Models\Donor::class, 'donor3', function (Faker $faker) {
    return [
        'link'                => 'http://rater.club/',
        'title'               => 'Rater Club',
        'loop_address'        => '.su-post-excerpt',
        'loop_item'           => '.su-post',
        'loop_link'           => 'h2 a',
        'loop_title'          => 'h2',
        'replace_search'      => '',
        'replace_to'          => '',
        'reviews_all'         => '.rbox',
        'reviews_ignore_text' => '',
        'reviews_rating'      => '',
        'reviews_text'        => '.comment-body',
        'reviews_title'       => '.comment-title',
        'reviews_name'        => '.comment-author',
        'reviews_id'          => '//*[@class="comment-anchor"]/@id',
        'single_site'         => '//tr//*/text()[contains(.,"Сайт")]/../..',
        'single_address'      => '//tr//*/text()[contains(.,"Адрес")]/../..',
        'single_tel'          => '//tr//*/text()[contains(.,"Телефон")]/../..',
        'single_title'        => 'h1',
    ];
});
