<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('name');
            $table->string('headline');
            $table->string('content');
            $table->enum('type',['SMS','WHATSAPP','EMAIL', 'PUSH']);
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });

        Schema::create('schedule', function (Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('message_id')->nullable();
            $table->dateTime('send_time');
            $table->foreign('message_id')->references('id')->on('messages');
            $table->timestamps();
        });
        Schema::create('delivery', function (Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('message_id')->nullable();
            $table->dateTime('sent_time');
            $table->boolean('delivered');
            $table->boolean('seen')->default(false);
            $table->dateTime('seen_time');
            $table->foreign('message_id')->references('id')->on('messages');
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
        Schema::dropIfExists('delivery');
        Schema::dropIfExists('schedule');
        Schema::dropIfExists('messages');
    }
}
