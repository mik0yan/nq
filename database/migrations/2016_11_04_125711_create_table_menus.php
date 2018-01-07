<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableMenus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->increments('id');
            $table->string('path',128)->comment('菜单路径');
            $table->string('name',32)->comment('菜单名称');
            $table->boolean('security')->comment('安全等级');
            $table->boolean('module')->comment('说明哪个模块下的id');
            $table->unsignedinteger('pId')->comment('父节点id');
            $table->unsignedinteger('sortMenuId')->comment('叶子节点排序');
            $table->unsignedinteger('sortPid')->comment('父节点排序');
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
        Schema::drop('menus');
    }
}
