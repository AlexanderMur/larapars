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
            'text' => 'Тестовый коммент Lorem ipsum dolor sit amet, consectetur adipisicing elit. Blanditiis, dignissimos excepturi expedita iusto labore libero modi molestiae non numquam perferendis praesentium provident quia quidem rem repudiandae sequi totam ut veritatis!!',
            'rating' => null,
            'donor_created_at' => \Carbon\Carbon::now(),
        ]);
    }
}
?>

