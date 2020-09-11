<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWechatMerchantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wechat_merchants', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->unsignedTinyInteger('type')->default(1)->comment('1-普通商户号 2-服务商');
            $table->string('mch_id', 32)->unique();
            $table->string('app_id');
            $table->string('key');
            $table->string('cert_path')->nullable();
            $table->string('key_path')->nullable();
            $table->string('notify_url')->nullable();
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
        Schema::dropIfExists('wechat_merchants');
    }
}
