<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVendorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',128)->nullable()->comment('联系人');
            $table->string('mobile')->nullable()->comment('联系方式');
            $table->string('corp',128)->comment('公司名称');
            $table->string('country_code',3)->nullable()->comment('国家代码');
            $table->string('desc')->nullable()->comment('供应商简介');
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
        Schema::drop('vendors');
    }
}
