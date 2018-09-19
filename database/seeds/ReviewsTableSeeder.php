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
            'donor_id' => '1',
            'company_id' => '1',
            'name' => "unwinter",
            'title' => 'Полное надувательство!',
            'text' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cupiditate error id ipsam perferendis totam! Asperiores cum delectus error libero maxime minus quod recusandae repellat, sint soluta! Fuga molestias quae voluptate.',
            'rating' => null,
            'donor_created_at' => \Carbon\Carbon::now(),
        ]);
        DB::table('reviews')->insert([
            'donor_id' => '1',
            'company_id' => '1',
            'name' => "habbe",
            'title' => null,
            'text' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cupiditate error id ipsam perferendis totam! Asperiores cum delectus error libero maxime minus quod recusandae repellat, sint soluta! Fuga molestias quae voluptate.',
            'rating' => null,
            'donor_created_at' => \Carbon\Carbon::now(),
        ]);
        DB::table('reviews')->insert([
            'donor_id' => '1',
            'company_id' => '2',
            'name' => "global",
            'title' => null,
            'text' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cupiditate error id ipsam perferendis totam! Asperiores cum delectus error libero maxime minus quod recusandae repellat, sint soluta! Fuga molestias quae voluptate.',
            'rating' => null,
            'donor_created_at' => \Carbon\Carbon::now(),
        ]);
        DB::table('reviews')->insert([
            'donor_id' => '2',
            'company_id' => '3',
            'name' => "frowsy",
            'title' => null,
            'text' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cupiditate error id ipsam perferendis totam! Asperiores cum delectus error libero maxime minus quod recusandae repellat, sint soluta! Fuga molestias quae voluptate.',
            'rating' => null,
            'donor_created_at' => \Carbon\Carbon::now(),
        ]);
        DB::table('reviews')->insert([
            'donor_id' => '2',
            'company_id' => '4',
            'name' => "exocrine",
            'title' => null,
            'text' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cupiditate error id ipsam perferendis totam! Asperiores cum delectus error libero maxime minus quod recusandae repellat, sint soluta! Fuga molestias quae voluptate.',
            'rating' => null,
            'donor_created_at' => \Carbon\Carbon::now(),
        ]);
        DB::table('reviews')->insert([
            'donor_id' => '2',
            'company_id' => '5',
            'name' => "oometric",
            'title' => null,
            'text' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cupiditate error id ipsam perferendis totam! Asperiores cum delectus error libero maxime minus quod recusandae repellat, sint soluta! Fuga molestias quae voluptate.',
            'rating' => null,
            'donor_created_at' => \Carbon\Carbon::now(),
        ]);
    }
}
?>

