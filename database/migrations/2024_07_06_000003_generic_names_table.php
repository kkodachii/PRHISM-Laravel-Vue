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
        Schema::create('generic_names', function (Blueprint $table) {
            $table->increments('id');
            $table->string('generic_name')->notNullable();
            $table->unsignedInteger('category_id')->notNullable();

            $table->foreign('category_id')->references('id')->on('medical_categories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('generic_names');
    }
};
