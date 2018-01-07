<?php

use Flynsarmy\CsvSeeder\CsvSeeder;

class RebatesTableSeeder extends CsvSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */


    public function __construct()
    {
      $this->table = 'rebates';
      $this->csv_delimiter = ',';
      $this->filename = base_path().'/database/seeds/csvs/rebates.csv';

    }

    public function run()
    {
      DB::disableQueryLog();

      DB::table($this->table)->truncate();

      parent::run();
    }


}
