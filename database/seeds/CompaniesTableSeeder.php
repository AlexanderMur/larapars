<?php

use App\CompanyHistory;
use App\Models\Company;
use App\Models\Donor;
use App\Models\ParsedCompany;
use App\Models\Review;
use App\Services\SettingService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class CompaniesTableSeeder extends Seeder
{
    /**
     * @var Collection|Donor[]
     */
    public $donors = [];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createDefaultSettings();

        $this->donors = $this->createDonors();

    }

    /**
     * @param Company[]|ParsedCompany[] $companies
     *
     */
    public function makeReviewsForCompanies($companies)
    {

        foreach ($companies as $company) {
            $reviews = factory(Review::class, rand(0, 10))->make();
            $this->associateReviewsWithCompany($reviews, $company);
        }
    }

    /**
     * @param string $name
     * @param null $num
     * @return Collection|ParsedCompany[]
     */
    public function makeParsedCompanies($name = 'default', $num = null)
    {
        return factory(ParsedCompany::class, $name, $num)->make()
            ->each(function (ParsedCompany $parsedCompany) {
                $donor                     = $this->donors->random();
                $parsedCompany->donor_page = $this->generatePivotSite($donor, $parsedCompany);
                $parsedCompany->donor_id   = $donor->id;
            });
    }

    /**
     * @param Collection $collection
     * @param $min
     * @param $max
     * @return array|mixed|static
     */
    function takeRandom(Collection $collection, $min, $max)
    {
        $count = $collection->count();
        if ($max > $count) {
            $max = $count;
            if ($count < $min) {
                [];
            }
        }
        return $collection->random(rand($min, $max));
    }

    /**
     * @param Donor $donor
     * @param ParsedCompany|Company $company
     * @return string
     */
    function generatePivotSite(Donor $donor, $company)
    {
        return $donor->link . '/' . str_slug($company->title);
    }

    /**
     * @param Collection|Review[] $reviews
     * @param ParsedCompany|Company $company
     */
    function associateReviewsWithCompany($reviews, $company)
    {
        $donor = $this->donors->random();
        $company->reviews()->saveMany($reviews);
        foreach ($reviews as $review) {

            $review->donor_link = $this->generatePivotSite($donor, $company);
            $review->donor()->associate($donor);
            $review->save();
        }
    }

    /**
     * @param $models
     * @return array
     */
    public function saveMany($models)
    {
        foreach ($models as $model) {
            $model->save();
        }
        return $models;
    }

    public function createCompanyHistory()
    {
        $history = [
            [
                'field'             => 'title',
                'old_value'         => 'test',
                'new_value'         => 'test2',
                'parsed_company_id' => 1,
            ],
        ];
        foreach ($history as $item) {
            CompanyHistory::create($item);
        }
    }
    public function createDefaultSettings(){
        SettingService::set('time',4);
    }
    public function createDonors()
    {
        $donors = collect([
            [
                'link'         => 'https://avtosalon-otzyv.ru/',
                'title'        => 'Автосалон отзыв',
                'mass_parsing' => true,

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
                'reviews_pagination'  => '',
                'single_site'         => 'noindex',
                'single_address'      => '#view1 + p',
                'single_tel'          => '',
                'single_title'        => 'h2.entry-title',
                'single_city'         => '',
                'archive_pagination'  => '',
            ],
            [
                'link'                => 'https://otziv-avto.ru/',
                'title'               => 'Отзыв авто',
                'mass_parsing'        => true,
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
                'single_site'         => '//div[@id="primary"]//*/text()[contains(.,"Сайт")]/..',
                'single_address'      => '//div[@id="primary"]//*/text()[contains(.,"Адрес")]',
                'single_tel'          => '//div[@id="primary"]//*/text()[contains(.,"Тел")]',
                'single_title'        => 'h1.entry-title',
                'single_city'         => '',
                'archive_pagination'  => '.menu-item-102 a',
            ],
            [
                'link'                => 'http://rater.club/',
                'title'               => 'Rater Club',
                'mass_parsing'        => true,
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
                'single_city'         => '',
                'archive_pagination'  => '.uk-pagination li a',
            ],
            [
                'link'                => 'http://mail-auto.ru/',
                'title'               => 'Mail Auto',
                'mass_parsing'        => true,
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
                'single_city'         => '',
                'archive_pagination'  => 'form .uk-pagination a, .uk-nav.uk-nav-navbar a',
            ],
            [
                'link'                => 'http://xn----7sbgzkqfjydk.xn--p1ai/avtosalony',
                'title'               => 'авто-путник',
                'mass_parsing'        => true,
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
                'single_city'         => '',
                'archive_pagination'  => '.pagination a',
            ],
            [
                'link'                => 'http://mnenieavto.ru',
                'title'               => 'Мнение авто',
                'mass_parsing'        => true,
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
                'single_city'         => '',
                'archive_pagination'  => '.pagination a',
            ],
            [
                'link'                => 'https://auto-review.info/avtosalon',
                'title'               => 'Auto review',
                'mass_parsing'        => true,
                'loop_address'        => '',
                'loop_item'           => '.itemContainer',
                'loop_link'           => '.catItemHeader a',
                'loop_title'          => '',
                'replace_search'      => '',
                'replace_to'          => '',
                'reviews_all'         => '.li_otziv_',
                'reviews_ignore_text' => '',
                'reviews_rating'      => '',
                'reviews_text'        => 'p',
                'reviews_title'       => '.head_com',
                'reviews_name'        => '.otziv_l_name',
                'reviews_id'          => '',
                'reviews_pagination'  => '',
                'single_site'         => '//div[@class="des_salon"]//span/text()[contains(.,"Вебсайт")]/../..',
                'single_address'      => '//div[@class="des_salon"]//span/text()[contains(.,"Адрес")]/../..',
                'single_tel'          => '//div[@class="des_salon"]//span/text()[contains(.,"Телефон")]/../..',
                'single_title'        => 'h1',
                'single_city'         => '//div[@class="des_salon"]//span/text()[contains(.,"Город")]/../..',
                'archive_pagination'  => '.k2Pagination a',
            ],
            [
                'link'                => 'https://avtosalon-review.ru/',
                'title'               => 'avtosalon-review',
                'mass_parsing'        => true,
                'loop_address'        => '',
                'loop_item'           => '.post',
                'loop_link'           => 'h1 a',
                'loop_title'          => 'h1',
                'replace_search'      => '</header><!-- .entry-header -->',
                'replace_to'          => '</div></header><!-- .entry-header -->',
                'reviews_all'         => '.full-testimonial',
                'reviews_ignore_text' => '... Читать полностью',
                'reviews_rating'      => '',
                'reviews_text'        => '.rr_review_text',
                'reviews_title'       => '.rr_title',
                'reviews_name'        => '.rr_review_name span',
                'reviews_id'          => '',
                'reviews_pagination'  => '',
                'single_site'         => '//h3/text()[contains(.,"Вебсайт")]/../following-sibling::noindex/a',
                'single_address'      => '//h3/text()[contains(.,"Адрес")]/../following-sibling::p',
                'single_tel'          => '',
                'single_title'        => 'h1.entry-title',
                'single_city'         => '',
                'archive_pagination'  => '.k2Pagination a',
            ],
            [
                'link'                => 'https://kritika.su/otzyvy/auto/avtosalony.html',
                'title'               => 'kritika.su',
                'mass_parsing'        => true,
                'loop_address'        => '',
                'loop_item'           => '.listing-summary',
                'loop_link'           => 'h3 a',
                'loop_title'          => 'h3 a',
                'replace_search'      => '',
                'replace_to'          => '',
                'reviews_all'         => '.review',
                'reviews_ignore_text' => '... Читать полностью',
                'reviews_rating'      => '',
                'reviews_text'        => '.review-text',
                'reviews_title'       => '.review-title',
                'reviews_name'        => '.review-owner',
                'reviews_id'          => '',
                'reviews_pagination'  => '.pagination a',
                'single_site'         => '//div[@id="listing"]//div[@class="caption"]/text()[contains(.,"Сайт")]/../..//a',
                'single_address'      => '//div[@id="listing"]//div[@class="caption"]/text()[contains(.,"Адрес")]/../..',
                'single_tel'          => '//div[@id="listing"]//div[@class="caption"]/text()[contains(.,"Телефон")]/../..',
                'single_title'        => 'h1',
                'single_city'         => '',
                'archive_pagination'  => '.pagination a',
            ],
            [
                'link'                => 'http://rtdm-auto.ru/',
                'title'               => 'rtdm-auto',
                'mass_parsing'        => true,
                'loop_address'        => '',
                'loop_item'           => '.post',
                'loop_link'           => 'h2 a',
                'loop_title'          => 'h2 a',
                'replace_search'      => '',
                'replace_to'          => '',
                'reviews_all'         => '.comment',
                'reviews_ignore_text' => '',
                'reviews_rating'      => '',
                'reviews_text'        => 'p',
                'reviews_title'       => '.review-title',
                'reviews_name'        => 'cite',
                'reviews_id'          => '',
                'reviews_pagination'  => '',
                'single_site'         => '//div[@class="entry-content"]//ul/li//text()[contains(.,"Сайт")]/../..',
                'single_address'      => '//div[@class="entry-content"]//ul/li//text()[contains(.,"Адрес")]/..',
                'single_tel'          => '//div[@class="entry-content"]//ul/li//text()[contains(.,"Телефон")]/..',
                'single_title'        => 'h1',
                'single_city'         => '',
                'archive_pagination'  => '#main-navigation li:nth-child(-n+4):not(:first-child) a, #post-navigator a',
            ],
            [
                'link'                => 'https://trueslns.ru/?limitstart=0',
                'title'               => 'trueslns',
                'mass_parsing'        => true,
                'loop_address'        => '',
                'loop_item'           => '.listing-summary',
                'loop_link'           => 'h3 a',
                'loop_title'          => 'h3 a',
                'replace_search'      => '',
                'replace_to'          => '',
                'reviews_all'         => '.review',
                'reviews_ignore_text' => '',
                'reviews_rating'      => '',
                'reviews_text'        => '.review-text',
                'reviews_title'       => '',
                'reviews_name'        => '.review-owner',
                'reviews_id'          => '',
                'reviews_pagination'  => '',
                'single_site'         => '//div[@class="fields"]//div[@class="caption"]/text()[contains(.,"Сайт")]/../../..',
                'single_address'      => '//div[@class="fields"]//div[@class="caption"]/text()[contains(.,"Адрес")]/../..',
                'single_tel'          => '//div[@class="fields"]//div[@class="caption"]/text()[contains(.,"Телефон")]/../..',
                'single_title'        => 'h2',
                'single_city'         => '',
                'archive_pagination'  => '.pages-links a',
            ],
            [
                'link'                => 'https://acauto.su',
                'title'               => 'acauto.su',
                'mass_parsing'        => true,
                'loop_address'        => '',
                'loop_item'           => '.post',
                'loop_link'           => 'h3 a',
                'loop_title'          => 'h3 a',
                'replace_search'      => '',
                'replace_to'          => '',
                'reviews_all'         => '.comment',
                'reviews_ignore_text' => '',
                'reviews_rating'      => '',
                'reviews_text'        => 'p',
                'reviews_title'       => '',
                'reviews_name'        => '//div[@class="vcard meta"]/text()',
                'reviews_id'          => '',
                'reviews_pagination'  => '',
                'single_site'         => '//div[@class="entry-content clearfix"]//ul//text()[contains(.,"Сайт")]/../..',
                'single_address'      => '//div[@class="entry-content clearfix"]//ul//text()[contains(.,"Адрес")]/..',
                'single_tel'          => '//div[@class="entry-content clearfix"]//ul//text()[contains(.,"Телефон")]/..',
                'single_title'        => 'h1',
                'single_city'         => '',
                'archive_pagination'  => '.pagination a,#menu-menu-1 li:nth-child(-n+2) a',
            ],
            [
                'link'                => 'https://xn----dtbas7abcdze4h.xn--p1ai/katalog-avtosalonov/',
                'title'               => 'топ-отзывов',
                'mass_parsing'        => true,
                'loop_address'        => '',
                'loop_item'           => '.su-post',
                'loop_link'           => 'h2 a',
                'loop_title'          => 'h2 a',
                'replace_search'      => '',
                'replace_to'          => '',
                'reviews_all'         => '.comment',
                'reviews_ignore_text' => '',
                'reviews_rating'      => '',
                'reviews_text'        => 'p',
                'reviews_title'       => '',
                'reviews_name'        => 'cite',
                'reviews_id'          => '',
                'reviews_pagination'  => '',
                'single_site'         => '//div[@class="blog-block single-page"]/p/text()[contains(.,"Сайт")]',
                'single_address'      => '//div[@class="blog-block single-page"]/p/text()[contains(.,"Адрес")]',
                'single_tel'          => '//div[@class="blog-block single-page"]/p/text()[contains(.,"Телефон")]',
                'single_title'        => 'h1',
                'single_city'         => '',
                'archive_pagination'  => '',
            ],
            [
                'link'                => 'http://rusavtodiler.ru',
                'title'               => 'rusavtodiler',
                'mass_parsing'        => true,
                'loop_address'        => '',
                'loop_item'           => '.post',
                'loop_link'           => 'h1 a',
                'loop_title'          => 'h1 a',
                'replace_search'      => '',
                'replace_to'          => '',
                'reviews_all'         => '.content-annina .full-testimonial',
                'reviews_ignore_text' => '... Читать полностью',
                'reviews_rating'      => '',
                'reviews_text'        => '.rr_review_text',
                'reviews_title'       => '.rr_title',
                'reviews_name'        => '.rr_review_name',
                'reviews_id'          => '',
                'reviews_pagination'  => '',
                'single_site'         => 'article a',
                'single_address'      => '',
                'single_tel'          => 'article',
                'single_title'        => 'h1.entry-title',
                'single_city'         => '',
                'archive_pagination'  => '',
            ],
            [
                'link'                => 'http://bp-auto.ru/news/',
                'title'               => 'bp-auto',
                'mass_parsing'        => true,
                'loop_address'        => '',
                'loop_item'           => '.post',
                'loop_link'           => 'h5 a',
                'loop_title'          => 'h5 a',
                'replace_search'      => '',
                'replace_to'          => '',
                'reviews_all'         => 'li.comment',
                'reviews_ignore_text' => '... Читать полностью',
                'reviews_rating'      => '',
                'reviews_text'        => 'p',
                'reviews_title'       => '',
                'reviews_name'        => '.author-card',
                'reviews_id'          => '',
                'reviews_pagination'  => '',
                'single_site'         => '.post pre',
                'single_address'      => '',
                'single_tel'          => '.post pre',
                'single_title'        => '.tag_line_title',
                'single_city'         => '',
                'archive_pagination'  => '.pagination a',
            ],
            [
                'link'                => 'http://dealer-auto.ru/avtosalony/',
                'title'               => 'dealer-auto',
                'mass_parsing'        => true,
                'loop_address'        => '',
                'loop_item'           => '.post',
                'loop_link'           => 'h2 a',
                'loop_title'          => 'h2 a',
                'replace_search'      => '</br>',
                'replace_to'          => '<br/>',
                'reviews_all'         => 'li.comment',
                'reviews_ignore_text' => '... Читать полностью',
                'reviews_rating'      => '',
                'reviews_text'        => 'p',
                'reviews_title'       => '.lets-review-ur-headline',
                'reviews_name'        => 'cite',
                'reviews_id'          => '',
                'reviews_pagination'  => '',
                'single_site'         => '//section[@class="cb-entry-content clearfix"]/text()[contains(.,\'Сайт\') or contains(.,"Auto.ru")]/following-sibling::a',
                'single_address'      => '//section[@class="cb-entry-content clearfix"]/text()[contains(.,\'Адрес\')]/following-sibling::b',
                'single_tel'          => '//section[@class="cb-entry-content clearfix"]/text()[contains(.,\'✆\')]',
                'single_title'        => 'h1',
                'single_city'         => '',
                'archive_pagination'  => '.page-numbers a',
            ],
            [
                'link'                => 'http://bookrates.ru/',
                'title'               => 'Book rates',
                'mass_parsing'        => false,
                'loop_address'        => '',
                'loop_item'           => '.post',
                'loop_link'           => 'h2 a',
                'loop_title'          => 'h2 a',
                'replace_search'      => '</br>',
                'replace_to'          => '<br/>',
                'reviews_all'         => '.comment',
                'reviews_ignore_text' => '... Читать полностью',
                'reviews_rating'      => '',
                'reviews_text'        => '.comment-text',
                'reviews_title'       => '.lets-review-ur-headline',
                'reviews_name'        => '.author',
                'reviews_id'          => '',
                'reviews_pagination'  => '',
                'single_site'         => '//div[@class="center"]//text()[contains(.,"сайт")]/../..',
                'single_address'      => '//div[@class="center"]//text()[. = "Адрес"]/../..',
                'single_tel'          => '//div[@class="center"]//text()[contains(.,"Телефон")]/../..',
                'single_title'        => 'h1',
                'single_city'         => '//div[@class="center"]//text()[contains(.,"Город")]/../..',
                'archive_pagination'  => '',
            ],
        ]);

        foreach ($donors as $donor) {
            $donors[] = Donor::create($donor);
        }
        return $donors;
    }
}
