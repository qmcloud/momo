<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWechatUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wechat_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('app_id', 32);
            $table->unsignedBigInteger('user_id')->default(0);
            $table->string('openid', 32)->index();
            $table->string('nickname')->nullable();
            $table->string('avatar')->nullable();
            $table->unsignedTinyInteger('gender')->default(0);
            $table->string('country')->nullable();
            $table->string('province')->nullable();
            $table->string('city')->nullable();
            $table->timestamp('subscribed_at')->nullable();
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
        Schema::dropIfExists('wechat_users');
    }
}
