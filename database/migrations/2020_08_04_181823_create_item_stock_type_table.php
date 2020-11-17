<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemStockTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_stock_type', function (Blueprint $table) {
            
            $table->unsignedBigInteger('item_id');

            $table->foreign('item_id')
            ->references('id') 
            ->on('items')
            ->onDelete('cascade');

            //FK stock type
            $table->unsignedBigInteger('stock_type_id');

            $table->foreign('stock_type_id')
            ->references('code') 
            ->on('stock_types')
            ->onDelete('cascade');

            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_typeables');
    }
}
