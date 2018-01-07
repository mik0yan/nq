<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedinteger('agent_id');
            $table->string('bank_code',64)->nullable();
            $table->string('bank',64)->nullable();
            $table->double('sum')->comment('金额');
            $table->double('avalid_sum')->comment('可用总金额');
            $table->double('duizhang_sum')->comment('已对账金额');
            $table->timestamp('ruzhang_time')->comment('入账时间');
            $table->unsignedinteger('status')->comment('S1:未付,S2:收款,S3:已入账');
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
        Schema::drop('payments');
    }
}
