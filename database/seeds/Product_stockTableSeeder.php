<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Crockett\CsvSeeder\CsvSeeder;

class Product_stockTableSeeder extends CsvSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
     public function __construct()
     {
       $this->table = 'product_stock';
       $this->csv_delimiter = ',';
       $this->filename = base_path().'/database/seeds/csvs/product_stock.csv';

     }

     public function run()
     {
       DB::disableQueryLog();

       DB::table($this->table)->truncate();

       parent::run();
     }


}
