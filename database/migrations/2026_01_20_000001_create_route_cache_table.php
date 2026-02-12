<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRouteCacheTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('route_cache', function (Blueprint $table) {
            $table->increments('id');
            // source_key and dest_key can be 'lat,lng' strings
            $table->string('source_key', 100);
            $table->string('dest_key', 100);

            $table->decimal('distance_km', 8, 2)->nullable();
            $table->integer('duration_min')->nullable(); // in minutes
            $table->text('polyline')->nullable(); // encoded polyline
            $table->text('response_json')->nullable(); // Store full JSON if needed for other fields

            $table->timestamps();

            // Unique index to prevent duplicate routes
            $table->unique(['source_key', 'dest_key']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('route_cache');
    }
}
