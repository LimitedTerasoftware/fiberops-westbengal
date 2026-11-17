<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHolidaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('date');
            $table->enum('type', ['national', 'regional', 'optional'])->default('national');
            $table->enum('applies_to', ['all', 'state', 'district', 'block'])->default('all');
            $table->foreignId('state_id')->nullable()->constrained('states');
            $table->foreignId('district_id')->nullable()->constrained('districts');
            $table->foreignId('block_id')->nullable()->constrained('blocks');
            $table->boolean('is_recurring')->default(false);
            $table->enum('duration', ['full', 'half'])->default('full');
            $table->date('substitute_date')->nullable();
            $table->text('description')->nullable();
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
        Schema::dropIfExists('holidays');
    }
}
