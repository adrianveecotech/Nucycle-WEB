<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class WasteClearanceSchedulePaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('waste_clearance_schedule_payment', function (Blueprint $table) {
            $table->id();
            $table->date('invoice_date');
            $table->string('unit_price');
            $table->string('total_price');
            $table->date('receipt_date');
            $table->string('receipt_number');
            $table->string('total_amount');
            $table->string('image');
            $table->string('waste_clearance_schedule_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('waste_clearance_schedule_payment');
    }
}
