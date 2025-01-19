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
        Schema::create('requests', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('rhu_id')->notNullable();
            $table->unsignedInteger('requester_user_id')->notNullable();
            $table->unsignedInteger('barangay_id')->nullable();
            $table->string('status')->default('Pending')->notNullable();
            $table->timestamp('requested_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('date_approved')->nullable();
            $table->unsignedInteger('approver_user_id')->nullable();
            $table->string('approver_name')->nullable();
            $table->unsignedInteger('approver_rhu')->nullable();
            $table->string('approver_position')->nullable();
            $table->string('reject_reason')->nullable();
            $table->timestamps();

            $table->foreign('rhu_id')->references('id')->on('rhus');
            $table->foreign('requester_user_id')->references('id')->on('users');
            $table->foreign('approver_user_id')->references('id')->on('users');
            $table->foreign('barangay_id')->references('id')->on('barangays');
        });

        Schema::create('medicine_request_names', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('request_id')->notNullable();
            $table->unsignedInteger('generic_name_id')->notNullable();
            $table->integer('quantity');
            $table->timestamps();

            $table->foreign('request_id')->references('id')->on('requests');
            $table->foreign('generic_name_id')->references('id')->on('generic_names');
        });

        Schema::create('equipment_request_names', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('request_id')->notNullable();
            $table->string('equipment_name')->notNullable();
            $table->integer('quantity');
            $table->timestamps();

            $table->foreign('request_id')->references('id')->on('requests');
        });

        Schema::create('supply_request_names', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('request_id')->notNullable();
            $table->string('medical_supply_name')->notNullable();
            $table->integer('quantity');
            $table->timestamps();

            $table->foreign('request_id')->references('id')->on('requests');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
