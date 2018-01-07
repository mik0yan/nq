<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

use App\product;
use App\serials;
use App\Stock;
use Carbon\Carbon;

class SerialsTableSeeder extends Seeder
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
        $products = product::where('core',1)->get();
        foreach ($products as $product) {
            $a = $product->id;
            for ($i=0; $i <100 ; $i++) {
                serials::create([
                    'serial_no'=>$faker->isbn13,
                    'product_id'=>$product->id,
                    'purchase_id'=>rand(1,30),
                    'stock_id'=>rand(1,10),
                    'product_at'=>Carbon::now()->addWeek(rand(-12,-5))->toDateString(),
                    'storage_at'=>Carbon::now()->addWeek(rand(-5,-1))->toDateString(),
                    'expire_at'=>Carbon::now()->addWeek(rand(24,36))->toDateString()
                ]);
            }
        }
    }
}
