<?php
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

use App\client;
use App\hospital;
use App\agent;
use App\order;
use App\order_product;
use App\order_approve;
use App\product;
use App\payment;
use App\charge;

class OrderTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function __construct()
    {
      $this->table = 'orders';
    }

    public static function createorderlist($id)
    {
      $faker = Faker::create('zh_CN');

      $total = 0;
      for($i=1;$i<=rand(1,10);$i++)
      {
        $product_id =rand(1,96);
        $amount = rand(1,5);
        $price = product::getprice($product_id) * (1+ $faker->randomElement([-0.2,0.1,0.05,-0.05,-0.1]));
        order_product::create([
          'product_id' => $product_id,
          'order_id' => $id,
          'amount' => $amount,
          'sub_total' => $amount* $price,
          'bonus' => $amount * product::getbonus($product_id)
        ]);
        $total += $amount * $price;
      }
      return $total;
    }

    public static function newapproved($id,$type)
    {
      $faker = Faker::create('zh_CN');

      order_approve::create([
        'approver_id' => $faker->randomElement([1,3,4,5,8,9,36,37,38,39,40,41]),
        'order_id' => $id,
        'type' => $type,
        'status' => 1,
        'memo' => $faker->text($maxNbChars = 20)
      ]);
    }
    public static function createorderapprove($id,$status)
    {
      $faker = Faker::create('zh_CN');

      if($status > 2)
        OrderTableSeeder::newapproved($id,1);
      if($status > 3)
      {
        OrderTableSeeder::newapproved($id,2);
        OrderTableSeeder::newapproved($id,3);
        OrderTableSeeder::newapproved($id,4);
        OrderTableSeeder::newapproved($id,6);
      }
      if($status > 4)
      {
        OrderTableSeeder::newapproved($id,11);
      }
      if($status > 5)
      {
        OrderTableSeeder::newapproved($id,9);
        OrderTableSeeder::newapproved($id,14);
        OrderTableSeeder::newapproved($id,7);
      }
      if($status > 6)
      {
        OrderTableSeeder::newapproved($id,5);
      }
      if($status > 7)
      {
        OrderTableSeeder::newapproved($id,13);
      }
      if($status > 8)
      {
        OrderTableSeeder::newapproved($id,8);
      }
    }

    public static function createpayment($id,$created_at,$sum,$agent_id)
    {
      $interval = rand(2,30).' days';
      date_add($created_at,date_interval_create_from_date_string($interval));

      $faker = Faker::create('zh_CN');
      payment::create([
        'agent_id' => $agent_id,
        'bank_code' => $faker->creditCardNumber,
        'bank'=>$faker->bank,
        'sum' =>$sum,
        'avalid_sum'=>0,
        'duizhang_sum'=>$sum,
        'ruzhang_time'=>$created_at,
        'status'=>2
      ]);
      charge::create([
        'agent_id' => $agent_id,
        'type' => 1,
        'order_id'=>$id,
        'payment_id'=>$id,
        'sum'=>$sum
      ]);

    }

    public function run()
    {
      DB::table('orders')->truncate();
      DB::table('order_product')->truncate();
      DB::table('order_approves')->truncate();
      DB::table('payments')->truncate();
      DB::table('charges')->truncate();

      $faker = Faker::create('zh_CN');
      for($i=1;$i<=2000;$i++)
      {

        $sum = OrderTableSeeder::createorderlist($i);
        // $sum = 100;
        $validsum = $sum * $faker->randomElement([0.7,0.3,0.7,1,1,1,1]);
        $bonus = 100 * $faker->Numberbetween(0,150);
        $performance = 100 * $faker->Numberbetween(0,300);
        $warranty = $faker->Numberbetween(1,4);
        $addsum = $sum * ($warranty -1) /10;
        $status = $faker->Numberbetween(1,9);
        $agent_id = $faker->Numberbetween(1,150);
        $created_at = $faker->datetimeBetween('-500 days','-1 days');
        order::create([
          'client_id' => $faker->Numberbetween(1,1000) ,
          'agent_id'=>$agent_id,
          'user_id'=>$faker->randomElement([37,42,43,44,45,46,47,48]),
          'ordno'=>$faker->phoneNumber(),
          'sum'=>$sum,
          'validsum'=>$validsum,
          'bonus'=>$bonus,
          'performance'=>$performance,
          'comment'=>$faker->address(),
          'sum'=>$sum,
          'addsum'=>$addsum,
          'warranty'=>$warranty,
          'status'=>$faker->Numberbetween(1,9),
          'created_at'=>$created_at
        ]);

        OrderTableSeeder::createorderapprove($i,$status);
        OrderTableSeeder::createpayment($i,$created_at,$sum+$addsum,$agent_id);

      }
    }
}
