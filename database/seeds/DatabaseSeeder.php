<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      // $this->call(HospitalsTableSeeder::class);
      $this->call(ProductsTableSeeder::class);
      // $this->call(LocalsTableSeeder::class);
      // $this->call(AreasTableSeeder::class);
      // $this->call(PerformTableSeeder::class);
      // $this->call(RebatesTableSeeder::class);

      $this->call(UsersTableSeeder::class);
      // $this->call(RolesTableSeeder::class);
      // $this->call(RoleuserTableSeeder::class);
      $this->call(StocksTableSeeder::class);
      $this->call(TransferTableSeeder::class);
      $this->call(Product_stockTableSeeder::class);
      // $this->call(ProductPackagesTableSeeder::class);
      // $this->call(SerialsTableSeeder::class);
      // $this->call(LotsTableSeeder::class);
      // $this->call(VendorsTableSeeder::class);
      //
      // $this->call(ClientsTableSeeder::class);
      // $this->call(AgentsTableSeeder::class);
      // $this->call(OrderTableSeeder::class);
    }
}
