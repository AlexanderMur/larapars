<?php

use Illuminate\Database\Seeder;

class ReviewsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('reviews')->insert([
            'company_id' => '1',
            'title' => 'Полное надувательство!',
            'text' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cupiditate error id ipsam perferendis totam! Asperiores cum delectus error libero maxime minus quod recusandae repellat, sint soluta! Fuga molestias quae voluptate.',
            'rating' => null,
            'donor_created_at' => \Carbon\Carbon::now(),
        ]);
        DB::table('reviews')->insert([
            'company_id' => '1',
            'title' => null,
            'text' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cupiditate error id ipsam perferendis totam! Asperiores cum delectus error libero maxime minus quod recusandae repellat, sint soluta! Fuga molestias quae voluptate.',
            'rating' => null,
            'donor_created_at' => \Carbon\Carbon::now(),
        ]);
        DB::table('reviews')->insert([
            'company_id' => '2',
            'title' => null,
            'text' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cupiditate error id ipsam perferendis totam! Asperiores cum delectus error libero maxime minus quod recusandae repellat, sint soluta! Fuga molestias quae voluptate.',
            'rating' => null,
            'donor_created_at' => \Carbon\Carbon::now(),
        ]);
        DB::table('reviews')->insert([
            'company_id' => '3',
            'title' => null,
            'text' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cupiditate error id ipsam perferendis totam! Asperiores cum delectus error libero maxime minus quod recusandae repellat, sint soluta! Fuga molestias quae voluptate.',
            'rating' => null,
            'donor_created_at' => \Carbon\Carbon::now(),
        ]);
        DB::table('reviews')->insert([
            'company_id' => '4',
            'title' => null,
            'text' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cupiditate error id ipsam perferendis totam! Asperiores cum delectus error libero maxime minus quod recusandae repellat, sint soluta! Fuga molestias quae voluptate.',
            'rating' => null,
            'donor_created_at' => \Carbon\Carbon::now(),
        ]);
        DB::table('reviews')->insert([
            'company_id' => '5',
            'title' => null,
            'text' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cupiditate error id ipsam perferendis totam! Asperiores cum delectus error libero maxime minus quod recusandae repellat, sint soluta! Fuga molestias quae voluptate.',
            'rating' => null,
            'donor_created_at' => \Carbon\Carbon::now(),
        ]);
    }
}
?>

