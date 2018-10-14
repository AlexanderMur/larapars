<?php

use App\CompanyHistory;
use App\Models\Company;
use App\Models\Donor;
use App\Models\ParsedCompany;
use App\Models\Review;
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
        $this->donors = $this->createDonors();

//        $this->createCompanyHistory();
//        /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\Company[] $companies */
//        $companies = factory(Company::class, 6)->create();

//        $companies
//            ->each(function (Company $company) {
//
//                /** @var Collection|Review[] $reviews */
//                $reviews = factory(Review::class, random_int(0, 10))->states('not rated')->make();
//
//                $this->associateReviewsWithCompany($reviews, $company);
//            })
//            ->each(function (Company $company) {
//
//                /** @var Collection|Review[] $reviews */
//
//                $reviews = factory(Review::class, random_int(0, 10))->make();
//                $this->associateReviewsWithCompany($reviews, $company);
//
//                $reviews->count() && $reviews->random()->trash();
//                $reviews->count() && $reviews->random()->delete();
//            });

//        $this->makeReviewsForCompanies(
//            $this->saveMany(
//                $this->makeParsedCompanies('company2', 11)
//            )
//        );
        /**
         * @var Company $company
         */
//        $company = factory(Company::class, 'company1')->create();
//
//        $parsed_companies = $this->makeParsedCompanies('company1', 11);
//
//        $this->saveMany($parsed_companies);
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

    public function createDonors()
    {
        $donors = collect([
            [
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
                'single_city'         => '',
                'archive_pagination'  => '',
            ],
            [
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
                'single_city'         => '',
                'archive_pagination'  => '.menu-item-102 a',
            ],
            [
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
                'single_city'         => '',
                'archive_pagination'  => '.uk-pagination li a',
            ],
            [
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
                'single_city'         => '',
                'archive_pagination'  => 'form .uk-pagination a, .uk-nav.uk-nav-navbar a',
            ],
            [
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
                'single_city'         => '',
                'archive_pagination'  => '.pagination a',
            ],
            [
                'link'                => 'http://mnenieavto.ru',
                'title'               => 'Мнение авто',
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
                'single_site'         => '//div[@class="des_salon"]//span/text()[contains(.,"Вебсайт")]/../..',
                'single_address'      => '//div[@class="des_salon"]//span/text()[contains(.,"Адрес")]/../..',
                'single_tel'          => '//div[@class="des_salon"]//span/text()[contains(.,"Телефон")]/../..',
                'single_title'        => 'h1',
                'single_city'         => '//div[@class="des_salon"]//span/text()[contains(.,"Город")]/../..',
                'archive_pagination'  => '.k2Pagination a',
            ],
        ]);

        foreach ($donors as $donor) {
            $donors[] = Donor::create($donor);
        }
        return $donors;
    }
}
