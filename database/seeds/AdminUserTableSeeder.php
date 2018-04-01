<?php

use Illuminate\Database\Seeder;
use Crockett\CsvSeeder\CsvSeeder;

class AdminUserTableSeeder extends CsvSeeder
{
    public function __construct()
    {
        $this->filename = base_path().'/database/seeds/csvs/users.csv';
        $this->table = 'admin_users';
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        parent::run();
    }
}
