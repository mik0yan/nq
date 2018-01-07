<?php

use Crockett\CsvSeeder\CsvSeeder;

class LocalsTableSeeder extends CsvSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */


    public function __construct()
    {
      $this->table = 'locals';
      $this->csv_delimiter = ',';
      $this->filename = base_path().'/database/seeds/csvs/locals.csv';

    }

    public function run()
    {
      DB::disableQueryLog();

      DB::table($this->table)->truncate();

      parent::run();
    }


}
