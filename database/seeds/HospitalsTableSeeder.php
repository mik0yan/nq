<?php

use Crockett\CsvSeeder\CsvSeeder;

class HospitalsTableSeeder extends CsvSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */


    public function __construct()
    {
      $this->table = 'hospitals';
//      $this->csv_delimiter = ',';
      $this->filename = base_path().'/database/seeds/csvs/hospitals.csv';
    }

    public function run()
    {
//      DB::disableQueryLog();
//
//      DB::table($this->table)->truncate();

      parent::run();
    }


}
