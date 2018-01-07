<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

use App\product;
use App\Lot;
use App\Stock;
use Carbon\Carbon;

class LotsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('product_packages')->truncate();
        $faker = Faker::create('zh_CN');
        $products = product::where('core',2)->get();
        foreach ($products as $product) {
            $a = $product->id;
            for ($i=0; $i <10 ; $i++) {
                Lot::create([
                    'lot_no'=>$faker->isbn13,
                    'product_id'=>$product->id,
                    'quantity'=>rand(20,100),
                    'transfer_id'=>rand(1,10),
                    'status_id'=>$faker->randomElement($array = array (1,1,1,2))
                ]);
            }
            for ($i=0; $i <30 ; $i++) {
                Lot::create([
                    'lot_no'=>$faker->isbn13,
                    'product_id'=>$product->id,
                    'quantity'=>rand(2,10),
                    'transfer_id'=>rand(1,50),
                    'status_id'=>$faker->randomElement($array = array (3,3,4))
                ]);
            }

        }
    }
}
