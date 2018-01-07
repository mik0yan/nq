<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSerials extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('serials', function (Blueprint $table) {
          $table->increments('id');
          $table->unsignedinteger('product_id')->comment('所属产品id');
          $table->unsignedinteger('purchase_id')->comment('采购的运单id')->nullable();
          $table->unsignedinteger('stock_id')->comment('最后的批次号id')->nullable();
          $table->unsignedinteger('ship_id')->comment('发货的运单id')->nullable();
          $table->string('serial_no',64)->comment('序列号');
          $table->string('comment',64)->comment('备注信息')->nullable();
          $table->date('product_at')->comment('生产日期')->nullable();
          // $table->date('storage_at')->comment('入库日期')->nullable();
          $table->date('expire_at')->comment('到期日期')->nullable();
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
      Schema::drop('serials');
    }
}
