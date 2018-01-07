<?php
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

use App\client;
use App\hospital;
use App\agent;
use App\area;

class AgentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */


    public function __construct()
    {
      $this->table = 'agents';

    }

    public function run()
    {
      // DB::disableQueryLog();

      DB::table('agents')->truncate();

      $faker = Faker::create('zh_CN');

      for($i=1;$i<=150;$i++)
      {
        $seed = $faker->unique()->Numberbetween(1,2966);
        agent::create([
          'name' => $faker->name() ,
          'corp'=>$faker->company(),
          'level'=>$faker->Numberbetween(1,2),
          'area_code'=> area::getareacode(),
          'mobile'=>$faker->phoneNumber(),
          'email'=>$faker->Email()
        ]);
      }

    }


}
