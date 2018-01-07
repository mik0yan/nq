<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedtinyinteger('catalog')->comment('T1:采购,T2:发货,T3:移库');
            $table->string('inviceno')->comment('发票编号')->nullable();;
            $table->string('contractno')->comment('合同编号')->nullable();;
            $table->string('track_id')->comment('物流单号')->nullable();
            $table->unsignedinteger('order_id')->nullable()->comment('出货运输相关的订单');
            $table->unsignedinteger('to_stock_id')->comment('入库仓库id')->nullable();
            $table->unsignedinteger('from_stock_id')->comment('出库仓库id')->nullable();
            $table->unsignedinteger('user_id')->comment('操作人员')->nullable();
            $table->string('comment')->comment('备注，用于说明是否发货完')->nullable();
            $table->date('arrival_at')->comment('到货日期')->nullable();
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
        Schema::drop('transfers');
    }
}
