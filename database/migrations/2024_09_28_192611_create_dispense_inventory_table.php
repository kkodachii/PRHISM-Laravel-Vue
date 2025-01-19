<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('dispense_inventory', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('dispense_id');
            $table->unsignedInteger('midwife_inventory_id')->nullable(); 
            $table->string('type')->notNullable(); 
            $table->string('name')->notNullable();
            $table->unsignedInteger('quantity')->notNullable();
            $table->timestamps();

            // Foreign key to connect to midwife_inventory
            $table->foreign('midwife_inventory_id')
                ->references('id')->on('midwife_inventory')
                ->onDelete('set null'); 
                
            // Foreign key to connect to dispensations
            $table->foreign('dispense_id')
                ->references('id')->on('dispensations')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('dispense_inventory');
    }
};
