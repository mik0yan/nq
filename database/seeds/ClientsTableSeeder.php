<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

use App\client;
use App\hospital;


class ClientsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */


    // public function __construct()
    // {
    //   $this->table = 'clients';
    //   $this->csv_delimiter = ',';
    //   $this->filename = base_path().'/database/seeds/csvs/clients.csv';
    //
    // }

    public function run()
    {
      // DB::disableQueryLog();

      DB::table('clients')->truncate();

      $faker = Faker::create('zh_CN');
      
      for($i=1;$i<=1000;$i++)
      {
        $seed = $faker->unique()->Numberbetween(1,2966);
        client::create([
          'name'=>$faker->name(),
          'corp'=> hospital::getname($seed),
          'area_code'=> hospital::getareacode($seed),
          'hosp_code'=> $seed,
          'mobile'=>$faker->phoneNumber(),
          'email'=>$faker->Email()
        ]);
      }

    }


}
