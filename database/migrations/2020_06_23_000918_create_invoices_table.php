<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('serie',12)->nullable();
            $table->decimal('subtotal',12,2);
            $table->decimal('taxes',12,2)->default('0');
            $table->decimal('discount',12,2)->default('0');
            $table->decimal('total',12,2)->default('0');

            //FK for Invoice Types table
            $table->unsignedBigInteger('type_id')->nullable()->before('created_at');
            $table->foreign('type_id')->references('id')->on('invoice_types');

            //FK for Invoice Stages table
            $table->unsignedBigInteger('stage_id')->nullable()->before('created_at');
            $table->foreign('stage_id')->references('id')->on('invoice_stages');

            //FK for RANQHANA_USERS table
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('ranqhana_users');

            $table->softDeletes();
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
        Schema::dropIfExists('invoices');
    }
}
