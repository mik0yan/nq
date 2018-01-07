<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_packages', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedinteger('product_id')->comment('产品id')->nullable();
            $table->unsignedinteger('catalog')->comment('类型:1.商务包、2.培训包、3.维保包');
            $table->float('add_price',8)->comment('附加价格')->default(0);
            $table->string('name',32)->comment('附加包名称');
            $table->string('code',3)->comment('附加包代码');
            $table->string('desc',128)->comment('附加包说明');
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
        Schema::dropIfExists('product_packages');
    }
}
