<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRanqhanaUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ranqhana_users', function (Blueprint $table) {
            // $table->bigIncrements('id');
            $table->unsignedBigInteger('external_user_id')->unique();
            $table->string('login');
            $table->bigInteger('company_project_id');
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
        Schema::dropIfExists('ranqhana_users');
    }
}
