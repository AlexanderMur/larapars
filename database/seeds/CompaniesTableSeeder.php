<?php

use App\Models\Company;
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

        factory(Company::class,10)
            ->create()
            ->each(function(Company $company){
                $company->reviews()->saveMany(factory(Review::class,random_int(0,4))->make());
                if($first = $company->reviews()->first()){
                    $first->delete();
                };
            });
    }
}
