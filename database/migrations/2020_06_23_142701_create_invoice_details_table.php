<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_details', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('quantity');
            $table->decimal('price',7,2);
            $table->decimal('total',12,2);

            $table->unsignedBigInteger('item_id')->nullable(true);
            $table->foreign('item_id')->references('id')->on('items');

            $table->unsignedBigInteger('invoice_id')->nullable(true);
            $table->foreign('invoice_id')->references('id')->on('invoices');
            
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
        Schema::dropIfExists('invoice_details');
    }
}
