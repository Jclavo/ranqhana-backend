<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('amount',7,2)->default(0);
            $table->decimal('money',7,2)->default(0)->nullable();
            $table->dateTime('payment_date',0);
            // $table->timestamp('payment_date')->useCurrent();
            $table->dateTime('real_payment_date',0)->nullable();

            //FK Invoice
            $table->unsignedBigInteger('invoice_id');
            $table->foreign('invoice_id')->references('id')->on('invoices');

            //FK Payment Method
            $table->unsignedBigInteger('method_id');
            $table->foreign('method_id')->references('code')->on('payment_methods');

            //FK Payment Stage
            $table->unsignedBigInteger('stage_id');
            $table->foreign('stage_id')->references('code')->on('payment_stages');

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
        Schema::dropIfExists('payments');
    }
}
