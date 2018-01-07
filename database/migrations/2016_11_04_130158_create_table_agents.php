<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAgents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agents', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',128)->comment('联系人');
            $table->string('corp',128)->nullable()->comment('公司名称');
            $table->unsignedtinyinteger('level')->comment('L1:经销商,L2:总代,L3:直销');
            $table->string('area_code',32)->nullable();
            $table->string('mobile',64)->nullable()->comment('联系手机');
            $table->string('email',128)->nullable()->comment('联系邮箱');
            $table->char('flag')->default(1)->comment('独占标记：1，独占 0 公开');
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
        Schema::drop('agents');
    }
}
