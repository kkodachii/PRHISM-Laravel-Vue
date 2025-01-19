<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('delivery_problems', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('delivery_id')->notNullable();
            $table->unsignedInteger('rhu_id')->notNullable();
            $table->unsignedInteger('barangay_id')->notNullable();
            $table->json('broken_equipment')->nullable(); // Stores a JSON array of broken equipment
            $table->json('incorrect_quantity')->nullable(); // Stores a JSON array of incorrect quantities
            $table->boolean('package_issue')->default(false);
            $table->text('wrong_supply_text')->nullable(); // Stores the wrong supply text
            $table->text('other_text')->nullable(); // Stores other issue text
            $table->timestamps();

            $table->foreign('delivery_id')->references('id')->on('deliveries');
            $table->foreign('rhu_id')->references('id')->on('rhus');
            $table->foreign('barangay_id')->references('id')->on('barangays');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_problems');
    }
};
