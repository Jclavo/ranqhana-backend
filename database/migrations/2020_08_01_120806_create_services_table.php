<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name',45);
            $table->string('description',200)->nullable();
            $table->decimal('price',7,2)->default(0);

            //FK for RANQHANA_USERS table
            $table->unsignedBigInteger('user_id')->nullable(true);
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
        Schema::dropIfExists('services');
    }
}
