<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOltsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('olts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('state_id  ');
            $table->integer('district_id ');
            $table->integer('block_id ');
            $table->string('olt_location');
            $table->string('olt_location_code')->unique();
            $table->string('lgd_code')->unique();
            $table->string('olt_ip')->unique();
            $table->integer('no_of_gps');
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
        Schema::dropIfExists('olts');
    }
}
