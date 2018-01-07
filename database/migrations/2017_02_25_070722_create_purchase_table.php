<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('purchase', function (Blueprint $table) {
          $table->increments('id');
          $table->unsignedInteger('transfer_id')->comment('关联转运单号');
          // $table->unsignedinteger('user_id')->comment('采购员id');
          $table->string('inviceno')->comment('发票编号');
          $table->string('contractno')->comment('合同编号');
          // $table->unsignedinteger('stock_id')->comment('入库仓库id');
          // $table->unsignedinteger('transfer_id')->comment('运单信息');
          $table->string('comment')->comment('备忘');
          $table->unsignedtinyinteger('status')->comment('S1:待发货,S2:发货,S3:待收货,S4:完成、S9:异常');
          $table->dateTime('shipdate')->comment('发货日期');
          $table->dateTime('pickupdate')->comment('到货日期');
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
        Schema::drop('purchase');
    }
}
