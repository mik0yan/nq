<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableRefunds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refunds', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedinteger('user_id');
            $table->unsignedtinyinteger('stage')->comment('1人力、2出纳、3财务总监');
            $table->unsignedtinyinteger('catalog')->comment('1.工资、2奖金、3.绩效、4.差旅、5.平衡记账');
            $table->unsignedinteger('author_id')->comment('出账人');
            $table->unsignedtinyinteger('status')->comment('S1:pending,S2:paid');
            $table->double('sum')->comment('金额');
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
        Schema::drop('refunds');
    }
}
