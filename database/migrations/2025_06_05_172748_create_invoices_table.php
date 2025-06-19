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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_id')->unique(); // e.g. inv_0001
            $table->string('emp_id');               // FK to employees.emp_id
            $table->integer('month');
            $table->integer('year');
            $table->decimal('total_amount', 10, 2);
            $table->timestamps();
        });
    }
    

    public function down()
    {
        Schema::dropIfExists('invoices');
    }
};
