<?php

use Illuminate\Database\Seeder;
use \Illuminate\Support\Facades\DB;

class PipelinesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();
        for ($i = 0; $i < 5; $i++){
            DB::table('pipelines')->insert([
                'name' => $faker->catchPhrase,
                'description' => $faker->text,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
