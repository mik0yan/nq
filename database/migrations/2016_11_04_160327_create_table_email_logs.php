<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableEmailLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_logs', function (Blueprint $table) {
          $table->increments('id');
          $table->unsignedinteger('user_id')->nullable();
          $table->string('email',64);
          $table->string('content');
          $table->ipAddress('visitor');
          $table->macAddress('device');
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
        Schema::drop('email_logs');
    }
}
