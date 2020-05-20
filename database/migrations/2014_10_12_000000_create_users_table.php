<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('username', 25)->unique();
            $table->char('password', 255);
            $table->char('email', 240)->unique();
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('selected_video_id')->nullable();
            $table->rememberToken();
            $table->dateTime('first_login_time')->nullable();
            $table->dateTime('last_login_time')->nullable();
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
        Schema::dropIfExists('users');
    }
}
