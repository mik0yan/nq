<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',128)->comment('用户姓名');
            $table->string('work_id',64)->nullable()->comment('工号');
            $table->string('mobile',64)->comment('工作手机')->nullable();
            $table->string('email',128)->comment('工作邮箱')->nullable();
            $table->string('password',64)->comment('hashed密码')->nullable();
            $table->string('remember_token',128)->comment('登录token')->nullable();
            $table->string('wx_unionid')->comment('微信id')->nullable();
            $table->unsignedinteger('rate')->nullable()->default('600')->comment('返点率,万分之几');
            $table->boolean('is_activate')->default('1');
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
        Schema::drop('users');
    }
}
