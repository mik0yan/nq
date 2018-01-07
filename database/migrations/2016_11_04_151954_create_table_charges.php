<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCharges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('charges', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedinteger('agent_id');
            $table->unsignedtinyinteger('type')->comment('T1:支付订单,T2:余额,T3:减记');
            $table->unsignedinteger('order_id')->nullable();
            $table->unsignedinteger('payment_id')->nullable();
            $table->double('sum');
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
        Schema::drop('charges');
    }
}
