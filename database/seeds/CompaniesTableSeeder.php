<?php

use App\Models\Company;
use App\Models\Donor;
use App\Models\Group;
use App\Models\ParsedCompany;
use App\Models\Review;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class CompaniesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /** @var Collection $donors */
        $donors = factory(Donor::class, 10)->create();

        /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\Company[] $companies */
        $companies = factory(Company::class, 3)
            ->create()
            ->each(function (Company $company) use ($donors) {

                /** @var Collection|Review[] $reviews */
                $reviews = factory(Review::class, random_int(0, 10))
                    ->states('not rated')
                    ->make();

                $company->reviews()->saveMany($reviews);
                foreach ($reviews as $review) {

                    $donor = $donors->random();
                    $review->donor_link = $this->generatePivotSite($donor, $company);
                    $review->donor()->associate($donor);
                    $review->save();
                }
            })
            ->each(function (Company $company) use (&$reviewsArr, $donors) {

                $attachedDonors = collect();
                foreach ($this->takeRandom($donors, 1, 10) as $donor) {
                    $attachedDonors[] = $donor;
                    $company->donors()->attach($donor, ['site' => $this->generatePivotSite($donor, $company), 'created_at' => \Carbon\Carbon::now()]);
                }

                /** @var Collection|Review[] $reviews */
                $reviews = factory(Review::class, random_int(0, 10))->make();
                $company->reviews()->saveMany($reviews);
                foreach ($reviews as $review) {

                    $donor = $donors->random();
                    $review->donor_link = $this->generatePivotSite($donor, $company);
                    $review->donor()->associate($donor);
                    $review->save();
                }
//                if ($reviews->count() >= 2) {
//                    $group = new Group();
//                    $group->save();
//                    $group->reviews()->saveMany($this->takeRandom($reviews, 2, 3));
//
//                    $group = new Group();
//                    $group->save();
//                    $group->reviews()->saveMany($this->takeRandom($reviews, 2, 3));
//                }

                $reviews->count() && $reviews->random()->trash();
                $reviews->count() && $reviews->random()->delete();
            });

        /** @var Collection|ParsedCompany[] $parsed_companies */
        foreach (['company1', 'company2'] as $item) {
            $parsed_companies = factory(ParsedCompany::class,$item,11)->create();
            $parsed_companies->each(function(ParsedCompany $parsedCompany) use($donors){
                $donor = $donors->random();
                $parsedCompany->donor_page = $this->generatePivotSite($donor,$parsedCompany);
                $parsedCompany->donor()->associate($donor)->save();
            });
        }
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
            if($count < $min){
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
}
