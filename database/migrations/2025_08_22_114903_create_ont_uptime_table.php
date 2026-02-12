<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOntUptimeTable extends Migration
{


  

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::create('ont_uptime', function (Blueprint $table) {
            $table->id();
            $table->string('lgd_code')->index();
            $table->date('date');                
            $table->decimal('uptime_percent', 5, 2)->nullable(); 
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
         Schema::dropIfExists('ont_uptime');
    }
}
