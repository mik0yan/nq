<?php

use Crockett\CsvSeeder\CsvSeeder;


class ProductsTableSeeder extends CsvSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function __construct()
    {
        $this->table = 'products';
        $this->csv_delimiter = ',';
        $this->filename = base_path().'/database/seeds/csvs/products.csv';
    }

    public function run()
    {
        DB::disableQueryLog();

        DB::table($this->table)->truncate();

        parent::run();
    }


}
