<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGeoCacheTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('geo_cache', function (Blueprint $table) {
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->text('address');
            $table->timestamps();

            // Composite primary key to ensure uniqueness for coordinates
            $table->primary(['lat', 'lng']);

            // Adding an index for faster lookups (though PK handles it, explicit index intent is good)
            $table->index(['lat', 'lng']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('geo_cache');
    }
}
