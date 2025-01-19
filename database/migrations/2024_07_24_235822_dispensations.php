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
        Schema::create('dispensations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('barangay_id');
            $table->string('provider_name', 100)->notNullable();
            $table->string('position', 50)->notnullable();
            $table->string('recipients_name', 100)->notnullable();
            $table->string('address', 255)->nullable();
            $table->text('reason')->nullable();
            $table->date('dispense_date')->nullable();
            $table->unsignedInteger('age')->nullable();
            $table->date('birthday')->nullable();  
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('barangay_id')->references('id')->on('barangays')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('dispensations');
    }
};
