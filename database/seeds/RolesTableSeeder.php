<?php

use Flynsarmy\CsvSeeder\CsvSeeder;

class RolesTableSeeder extends CsvSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */


    public function __construct()
    {
      $this->table = 'roles';
      $this->csv_delimiter = ',';
      $this->filename = base_path().'/database/seeds/csvs/roles.csv';

    }

    public function run()
    {
      DB::disableQueryLog();

      DB::table($this->table)->truncate();

      parent::run();
    }


}
