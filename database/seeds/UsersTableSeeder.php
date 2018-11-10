<?php

use Illuminate\Database\Seeder;
use \Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
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
            DB::table('users')->insert([
                'name' => $faker->name(),
                'email' => $faker->safeEmail,
                'profile_picture' => 'https://randomuser.me/api/portraits/med/men/'.random_int(10,90).'.jpg',
                'password' => bcrypt('pa33.word'),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
