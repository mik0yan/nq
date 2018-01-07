<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCredits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('balances', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedtinyinteger('type')->comment('T1:客户,T2:销售');
            $table->unsignedinteger('agent_id')->nullable()->comment('客户id');
            $table->unsignedinteger('user_id')->nullable()->comment('销售id');
            $table->double('value')->comment('余额');
            $table->string('comment')->comment('备忘');
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
        Schema::drop('balances');
    }
}
