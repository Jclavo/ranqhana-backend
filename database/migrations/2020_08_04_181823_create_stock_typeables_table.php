<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockTypeablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_typeables', function (Blueprint $table) {
            $table->unsignedBigInteger('stock_type_id');

            $table->foreign('stock_type_id')
            ->references('id')
            ->on('stock_types')
            ->onDelete('cascade');

            $table->unsignedBigInteger('stock_typeable_id');
            $table->string('stock_typeable_type');

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
        Schema::dropIfExists('stock_typeables');
    }
}
