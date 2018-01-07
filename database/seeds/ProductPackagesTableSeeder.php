<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

use App\product;
use App\product_package;

class ProductPackagesTableSeeder extends Seeder
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
          $a =  $product->id;
          for ($i=0; $i < 9; $i++) {
            switch ($i%3+1) {
              case 1:
                $b = 'A';
                break;
              case 2:
                $b = 'B';
                break;
              case 3:
                $b = 'C';
                break;
              default:
                # code...
                break;
            }
            switch (floor($i/3)+1) {
              case 1:
                $c = '商务包';
                break;
              case 2:
                $c = '安装包';
                break;
              case 3:
                $c = '质保包';
                break;
              default:
                # code...
                break;
            }

            product_package::create([
              'product_id'=>$a,
              'catalog'=>floor($i/3)+1,
              'add_price'=>$faker->Numberbetween(50,500),
              'name'=> $c.$b,
              'code'=> $b,
              'desc'=> $faker->company
            ]);
          }
        }
    }
}
