<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableOrderApproves extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_approves', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedinteger('approver_id')->comment('批准人id');
            $table->unsignedinteger('order_id');
            $table->unsignedtinyinteger('type')->comment('T1:价格审计,T2:售后条款,T3:备货审批,T4:合规审批,T5:发货审批,T6:合同审批,T7:到账审批,T8:关闭审批,T9:赊销,T10:授权审批,T11合同签章,T12发货审批,T13到货审批,T14备货指令');
            $table->unsignedtinyinteger('status')->comment('S1:pending,S2:refuse,S3:approver');
            $table->string('memo')->comment('备忘');
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
        Schema::drop('order_approves');
    }
}
