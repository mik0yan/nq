<?php

// use Illuminate\Database\Seeder;
use Crockett\CsvSeeder\CsvSeeder;

class VendorsTableSeeder extends CsvSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
     public function __construct()
     {
       $this->table = 'vendors';
       $this->csv_delimiter = ',';
       $this->filename = base_path().'/database/seeds/csvs/vendors.csv';
     }

     public function run()
     {
 //      DB::disableQueryLog();
 //
 //      DB::table($this->table)->truncate();

       parent::run();
     }
}
