<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableClients extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',128)->comment('联系人');
            $table->string('corp',128)->nullable()->comment('医院名称');
            $table->text('desc')->nullable()->comment('医院介绍');
            $table->string('postal_code',128)->nullable()->comment('医院邮编');
            $table->string('hosp_code',128)->nullable()->comment('医院代码');
            $table->string('area_code',32)->nullable();
            $table->string('mobile',64)->nullable()->comment('联系手机');
            $table->string('email',128)->nullable()->comment('联系邮箱');
            $table->char('flag')->default(0)->comment('是否是特殊医院1、是  0 普通');
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
        Schema::drop('clients');
    }
}
