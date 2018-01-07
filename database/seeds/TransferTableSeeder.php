<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Crockett\CsvSeeder\CsvSeeder;

use App\product;
use App\serials;
use App\Stock;
use Carbon\Carbon;

class TransferTableSeeder extends CsvSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function __construct()
    {
    $this->table = 'transfers';
    $this->csv_delimiter = ',';
    $this->filename = base_path().'/database/seeds/csvs/transfers.csv';

    }

    public function run()
    {
        DB::disableQueryLog();

        DB::table($this->table)->truncate();

        parent::run();

    }
}
