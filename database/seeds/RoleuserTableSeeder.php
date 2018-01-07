<?php

use Flynsarmy\CsvSeeder\CsvSeeder;

class RoleuserTableSeeder extends CsvSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */


    public function __construct()
    {
      $this->table = 'role_user';
      $this->csv_delimiter = ',';
      $this->filename = base_path().'/database/seeds/csvs/role_user.csv';

    }

    public function run()
    {
      DB::disableQueryLog();

      DB::table($this->table)->truncate();

      parent::run();
    }


}
