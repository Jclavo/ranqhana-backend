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
            $table->bigIncrements('id');

            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('stock_type_id');

            $table->timestamps();

            $table->foreign('item_id')
            ->references('id')
            ->on('items')
            ->onDelete('cascade');

            $table->foreign('stock_type_id')
            ->references('id')
            ->on('stock_types')
            ->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_stock_type');
    }
}
