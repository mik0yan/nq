<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('catalog',64)->comment('产品分类');
            $table->boolean('core')->comment('是否为核心物料')->default(1);
            $table->unsignedinteger('vendor_id')->comment('供应商id')->default(1);
            $table->string('name',32)->comment('品名');
            $table->string('sku',32)->comment('物料号')->nullable();
            $table->string('item',32)->comment('物料型号')->nullable();
            $table->string('desc',128)->comment('产品描述')->nullable();
            $table->float('price')->comment('产品单价')->default(9.9);
            $table->boolean('license')->comment('销售许可')->default(1);
            $table->boolean('approved')->nullable()->comment('注册信息批准')->default(1);
            $table->unsignedinteger('approved_id')->nullable()->comment('批准人');
            $table->string('cert_no')->nullable()->comment('产品注册证号');
            $table->string('cert_url')->nullable()->comment('产品认证信息-oss_url');
            $table->date('certified_at')->nullable()->comment('产品认证有效期');
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
        Schema::drop('products');
    }
}
