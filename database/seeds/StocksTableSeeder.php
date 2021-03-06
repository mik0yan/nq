<?php

use Crockett\CsvSeeder\CsvSeeder;

class StocksTableSeeder extends CsvSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
     public function __construct()
     {
       $this->table = 'stocks';
       $this->csv_delimiter = ',';
       $this->filename = base_path().'/database/seeds/csvs/stocks.csv';

     }

     public function run()
     {
       DB::disableQueryLog();

       DB::table($this->table)->truncate();

       parent::run();
     }

}
