<?php

use App\Models\Company;
use App\Models\Donor;
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
        $companies = factory(Company::class, 30)
            ->create()
            ->each(function (Company $company) use (&$reviewsArr, $donors) {

                $attachedDonors = collect();
                foreach ($this->takeRandom($donors,1,10) as $donor) {
                    $attachedDonors[] = $donor;
                    $company->donors()->attach($donor, ['site' => $this->generatePivotSite($donor, $company),'created_at'=>\Carbon\Carbon::now()]);
                }

                $reviews = factory(Review::class, random_int(0, 20))->make();
                $company->reviews()->saveMany($reviews);
                foreach ($reviews as $review) {
                    foreach ($this->takeRandom($attachedDonors, 1, 3) as $donor) {
                        $review->donors()->attach($donor, ['site' => $this->generatePivotSite($donor, $company),'created_at'=>\Carbon\Carbon::now()]);
                    }
                }

                $reviews->count() && $reviews->random()->trash();
                $reviews->count() && $reviews->random()->delete();
            });


    }

    function takeRandom(Collection $collection, $min, $max)
    {
        $count = $collection->count();
        $max = $count < $max ? $count : $max;
        return $collection->random(rand($min,$max));
    }

    function generatePivotSite(Donor $donor, Company $company)
    {
        return $donor->link . '/' . str_slug($company->title);
    }
}
