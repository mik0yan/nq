<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableOrderProduct extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_product', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedinteger('product_id');
            $table->unsignedinteger('order_id');
            $table->float('amount')->comment('数量');
            $table->double('sub_total')->comment('小计');
            $table->string('package_code',128)->comment('可选包代码');
            $table->double('package_price')->comment('附加价格');
            $table->double('bonus')->comment('利润加成');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('order_product');
    }
}
