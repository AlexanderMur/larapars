<?php

use App\Models\Company;
use App\Models\Donor;
use App\Models\Review;
use Illuminate\Database\Seeder;

class CompaniesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $donors = factory(Donor::class,4)->create();
        factory(Company::class, 10)
            ->create()
            ->each(function (Company $company) use ($donors) {
                $donors->each(function($donor) use ($company) {
                    if(random_int(0,100) <= 70){
                        $company->donors()->attach($donor,['site'=>$this->generatePivotSite($donor,$company)]);
                    }
                });

                $company->reviews()->saveMany(factory(Review::class, random_int(0, 4))->make());
                if ($first = $company->reviews()->first()) {
                    $first->delete();
                };
            });
    }

    function generatePivotSite(Donor $donor, Company $company){
        return $donor->link . '/' . str_slug($company->title);
    }
}
