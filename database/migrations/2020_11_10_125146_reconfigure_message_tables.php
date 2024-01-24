<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReconfigureMessageTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn('type');
        });
        Schema::table('messages', function (Blueprint $table) {
            $table->set('type', ['SMS','WHATSAPP','EMAIL', 'PUSH']);
            $table->addColumn('integer', 'views', ['default' => 0]);
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
