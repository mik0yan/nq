<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSerialTransferPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('serial_transfer', function (Blueprint $table) {
            $table->integer('serial_id')->unsigned()->index();
            $table->foreign('serial_id')->references('id')->on('serials')->onDelete('cascade');
            $table->integer('transfer_id')->unsigned()->index();
            $table->foreign('transfer_id')->references('id')->on('transfers')->onDelete('cascade');
            $table->primary(['serial_id', 'transfer_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('serial_transfer');
    }
}
