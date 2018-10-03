<?php

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
//        $this->donors = factory(Donor::class, 10)->create();
        $this->donors = collect();
        $this->donors[] = factory(Donor::class, 'donor1')->create();
        $this->donors[] = factory(Donor::class, 'donor2')->create();
        /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\Company[] $companies */
        $companies = factory(Company::class, 6)->create();

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
        $company = factory(Company::class, 'company1')->create();

        $parsed_companies = $this->makeParsedCompanies('company1', 11);

        $this->saveMany($parsed_companies);
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
                return [];
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
}
