<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableProductStock extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_stock', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedinteger('stock_id');
            $table->unsignedinteger('product_id');
            $table->unsignedinteger('transfer_id');
            $table->double('amount')->comment('数量');
            $table->unsignedtinyinteger('status')->comment('S1:待入库,S2:入库,S3:待出库,S4:出库,S9:销毁');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('product_stock');
    }
}
