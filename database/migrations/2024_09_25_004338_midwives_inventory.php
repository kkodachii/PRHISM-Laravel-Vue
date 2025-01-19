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
        Schema::create('midwife_inventory', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->notNullable();
            $table->unsignedInteger('rhu_id')->notNullable();
            $table->unsignedInteger('barangay_id')->notNullable();
            $table->string('type')->notNullable();
            $table->string('name')->notNullable();
            $table->unsignedInteger('quantity')->notNullable();
            $table->string('status')->nullable();
            $table->text('description')->nullable();
            $table->string('brand')->nullable();
            $table->unsignedInteger('category_id')->nullable();
            $table->timestamp('expiration_date')->nullable();
            $table->unsignedInteger('request_id')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('rhu_id')->references('id')->on('rhus');
            $table->foreign('barangay_id')->references('id')->on('barangays');
            $table->foreign('category_id')->references('id')->on('medical_categories');
            $table->foreign('request_id')->references('id')->on('requests');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('midwife_inventory');
    }
};
