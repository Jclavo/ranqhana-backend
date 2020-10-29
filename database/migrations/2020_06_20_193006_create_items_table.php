<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name',45);
            $table->string('description',200)->nullable();
            $table->integer('stock')->default(0);
            $table->decimal('price',7,2)->default(0);
            $table->boolean('stocked')->default(true);

            //FK for UNITS table
            $table->unsignedBigInteger('unit_id');
            $table->foreign('unit_id')->references('code')->on('units');

            //FK for RANQHANA_USERS table
            $table->unsignedBigInteger('user_id');
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
        Schema::dropIfExists('items');
    }
}
