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
        'archive_pagination'  => '',
    ];
});

$factory->defineAs(App\Models\Donor::class, 'donor2', function (Faker $faker) {
    return [
        'link'                => 'https://otziv-avto.ru/',
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
        'single_site'         => '//div[@id="primary"]//*/text()[contains(.,"Сайт")]',
        'single_address'      => '//div[@id="primary"]//*/text()[contains(.,"Адрес")]',
        'single_tel'          => '//div[@id="primary"]//*/text()[contains(.,"Тел")]',
        'single_title'        => 'h1.entry-title',
        'archive_pagination'  => '.menu-item-102 a',
    ];
});
$factory->defineAs(App\Models\Donor::class, 'donor3', function (Faker $faker) {
    return [
        'link'                => 'http://rater.club/',
        'title'               => 'Rater Club',
        'loop_address'        => '',
        'loop_item'           => '.uk-article',
        'loop_link'           => 'h1 a',
        'loop_title'          => 'h1',
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
        'archive_pagination'  => '.uk-pagination li a',
    ];
});
$factory->defineAs(App\Models\Donor::class, 'donor4', function (Faker $faker) {
    return [
        'link'                => 'http://mail-auto.ru/',
        'title'               => 'Mail Auto',
        'loop_address'        => '',
        'loop_item'           => 'tbody tr',
        'loop_link'           => 'a',
        'loop_title'          => 'a',
        'replace_search'      => '',
        'replace_to'          => '',
        'reviews_all'         => '.rbox',
        'reviews_ignore_text' => '',
        'reviews_rating'      => '',
        'reviews_text'        => '.comment-body',
        'reviews_title'       => '.comment-title',
        'reviews_name'        => '.comment-author',
        'reviews_id'          => '//*[@class="comment-anchor"]/@id',
        'single_site'         => '//article[@class="uk-article"]//p/text()[contains(.,"Сайт")]/..',
        'single_address'      => '//article[@class="uk-article"]//p/text()[contains(.,"Адрес")]',
        'single_tel'          => '//article[@class="uk-article"]//p/text()[contains(.,"Телефон")]',
        'single_title'        => 'h1.uk-article-title',
        'archive_pagination'  => 'form .uk-pagination a, .uk-nav.uk-nav-navbar a',
    ];
});
$factory->defineAs(App\Models\Donor::class, 'donor5', function (Faker $faker) {
    return [
        'link'                => 'http://xn----7sbgzkqfjydk.xn--p1ai/avtosalony',
        'title'               => 'авто-путник',
        'loop_address'        => '',
        'loop_item'           => '.listing-summary',
        'loop_link'           => 'h3 a',
        'loop_title'          => 'h3',
        'replace_search'      => '',
        'replace_to'          => '',
        'reviews_all'         => '.review',
        'reviews_ignore_text' => '',
        'reviews_rating'      => '',
        'reviews_text'        => '.review-text',
        'reviews_title'       => '.review-title',
        'reviews_name'        => '.review-owner',
        'reviews_id'          => '//div[@class="review-title"]/a/@id',
        'single_site'         => '',
        'single_address'      => '//div[@id="listing"]//div/text()[contains(.,"Адрес")]/..',
        'single_tel'          => '//div[@id="listing"]//div/text()[contains(.,"Телефон")]/..',
        'single_title'        => 'h2',
        'archive_pagination'  => '.pagination a',
    ];
});
