<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->tinyInteger('role')->unsigned()->default(1)->comment('0 正常用户 >1 则为有角色用户');
                $table->tinyInteger('state')->unsigned()->default(1)->comment('会员状态 0 关闭 1开启');
                $table->string('unionid',40)->nullable()->comment('微信的unionid');
                $table->string('openid',40)->nullable()->comment('微信的openid');
                $table->string('nickname',60)->nullable()->comment('微信的昵称');
                $table->string('avatar',500)->nullable()->comment('会员头像');
                $table->string('picture',60)->nullable()->comment('会员大图');
                $table->string('login_time',30)->nullable()->comment('会员最近登录时间');
                $table->string('login_ip',18)->nullable()->comment('会员最近登录ip');
            });
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('school', function (Blueprint $table) {
            //
        });
    }
}
