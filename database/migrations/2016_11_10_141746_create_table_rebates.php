<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableRebates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rebates', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedtinyinteger('price_id')->comment('价格等级');
            $table->unsignedtinyinteger('agentlevel_id')->comment('代理等级');
            $table->unsignedtinyinteger('precentage')->comment('折扣百分比');
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
        Schema::drop('rebates');
    }
}
