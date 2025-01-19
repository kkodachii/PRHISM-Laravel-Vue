<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('request_id')->notNullable();
            $table->unsignedInteger('user_id')->notNullable();
            $table->unsignedInteger('rhu_id')->notNullable(); // Origin RHU
            $table->unsignedInteger('destination_id')->notNullable(); // Destination Barangay
            $table->boolean('for_pickup')->default(false);
            $table->date('pickup_date')->nullable();
            $table->string('delivery_name')->nullable();
            $table->string('delivery_number')->nullable();
            $table->text('delivery_address')->nullable();
            $table->timestamp('date_delivery')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('date_delivered')->nullable();
            $table->timestamps();

            $table->foreign('request_id')->references('id')->on('requests');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('rhu_id')->references('id')->on('rhus');
            $table->foreign('destination_id')->references('id')->on('barangays');
        });

        Schema::create('delivery_medicines', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('delivery_id')->nullable(); // Allow null to prevent cascade deletion
            $table->unsignedInteger('medicine_id')->nullable(); // Allow null to prevent cascade deletion
            $table->string('generic_name')->notNullable();
            $table->unsignedInteger('quantity')->notNullable();
            $table->string('status')->nullable();
            $table->timestamps();

            $table->foreign('delivery_id')->references('id')->on('deliveries')->onDelete('set null');
            $table->foreign('medicine_id')->references('id')->on('medicines')->onDelete('set null');
        });

        Schema::create('delivery_equipments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('delivery_id')->nullable(); // Allow null to prevent cascade deletion
            $table->unsignedInteger('equipment_id')->nullable(); // Allow null to prevent cascade deletion
            $table->string('equipment_name')->notNullable();
            $table->unsignedInteger('quantity')->notNullable();
            $table->string('status')->nullable();
            $table->timestamps();

            $table->foreign('delivery_id')->references('id')->on('deliveries')->onDelete('set null');
            $table->foreign('equipment_id')->references('id')->on('equipments')->onDelete('set null');
        });

        Schema::create('delivery_med_supplies', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('delivery_id')->nullable(); // Allow null to prevent cascade deletion
            $table->unsignedInteger('med_sup_id')->nullable(); // Allow null to prevent cascade deletion
            $table->string('med_sup_name')->notNullable();
            $table->unsignedInteger('quantity')->notNullable();
            $table->string('status')->nullable();
            $table->timestamps();

            $table->foreign('delivery_id')->references('id')->on('deliveries')->onDelete('set null');
            $table->foreign('med_sup_id')->references('id')->on('medical_supplies')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_med_supplies');
        Schema::dropIfExists('delivery_equipments');
        Schema::dropIfExists('delivery_medicines');
        Schema::dropIfExists('deliveries');
    }
};
