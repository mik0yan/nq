<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePerformsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prerforms', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedtinyinteger('price_id')->comment('价格等级');
            $table->unsignedtinyinteger('agentlevel_id')->comment('代理等级');
            $table->unsignedtinyinteger('precentage')->comment('超额绩效比例');
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
        Schema::drop('prerforms');
    }
}
