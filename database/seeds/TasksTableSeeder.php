<?php

use Illuminate\Database\Seeder;
use \Illuminate\Support\Facades\DB;

class TasksTableSeeder extends Seeder
{
    /**
     * @throws Exception
     */
    public function run()
    {
        $faker = Faker\Factory::create();
        $months = ['08', '09', '10'];
        $days = ['03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '18', '20', '22', '24', '25'];
        for ($i = 0; $i < 50; $i++){
            $start_date = '2018-'.$months[random_int(0, 2)].'-'.$days[random_int(0,17)];
            $due_date = new \Carbon\Carbon($start_date);
            $pipeline_id = $faker->randomElement([1, 2, 3, 4, 5]);
            $count =DB::select("select count(*) as count from tasks where pipeline_id = ".$pipeline_id);
            DB::table('tasks')->insert([
                'name' => $faker->catchPhrase,
                'description' => $faker->text,
                'pipeline_id' => $pipeline_id,
                'pipeline_position' => $count[0]->count,
                'start_date' => $start_date,
                'due_date' => $due_date->addDays(random_int(5, 15)),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
