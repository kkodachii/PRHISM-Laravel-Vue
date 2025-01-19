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
        Schema::create('medical_supplies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('med_sup_name')->notNullable();
            $table->unsignedInteger('batch_id')->notNullable();
            $table->unsignedInteger('barangay_id')->notNullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('quantity')->nullable();
            $table->unsignedInteger('reserved')->nullable();
            $table->string('provider')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->string('status')->notNullable();
            $table->date('date_acquired')->notNullable();
            $table->timestamps();
            $table->boolean('archived')->default(false); // Add archived column
            $table->softDeletes(); // Add soft deletes column

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('batch_id')->references('id')->on('batches');
            $table->foreign('barangay_id')->references('id')->on('barangays');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_supplies');
    }
};
