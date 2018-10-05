<?php

use Illuminate\Database\Seeder;

class ParsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('parsers')->insert([
            'donor_id'            => 1,
        ]);

        DB::table('parsers')->insert([
            'donor_id'            => 2,
        ]);
    }
}





























