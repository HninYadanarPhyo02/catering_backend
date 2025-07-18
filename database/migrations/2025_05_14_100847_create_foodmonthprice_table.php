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
        Schema::create('foodmonthprice', function (Blueprint $table) {
            $table->id();
            $table->string('food_id');
            $table->string('food_name');
            $table->date('date');
            $table->decimal('price');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foodmonthprice');
    }
};
