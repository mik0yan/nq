<?php

use Flynsarmy\CsvSeeder\CsvSeeder;

class PerformTableSeeder extends CsvSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */


    public function __construct()
    {
      $this->table = 'prerforms';
      $this->csv_delimiter = ',';
      $this->filename = base_path().'/database/seeds/csvs/prerforms.csv';

    }

    public function run()
    {
      DB::disableQueryLog();

      DB::table($this->table)->truncate();

      parent::run();
    }


}
