<?php

use Crockett\CsvSeeder\CsvSeeder;

class AreasTableSeeder extends CsvSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */


    public function __construct()
    {
      $this->table = 'areas';
//      $this->csv_delimiter = ',';
      $this->filename = base_path().'/database/seeds/csvs/areas.csv';

    }

    public function run()
    {
//      DB::disableQueryLog();
//
//      DB::table($this->table)->truncate();

      parent::run();
    }


}
