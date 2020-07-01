<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news', function (Blueprint $table) {
            $table->increments('id');
            $table->string('news_title', 100)->nullable(false)->default('')->comment('新闻标题');
            $table->string('news_lable', 20)->nullable(false)->default('')->comment('新闻标签');
            $table->string('news_author', 20)->nullable(false)->default('')->comment('新闻发布作者');
            $table->string('news_content', 200)->nullable(false)->default('')->comment('新闻内容');
            $table->string('news_link', 200)->nullable(false)->default('')->comment('新闻链接');
            $table->string('news_comment', 200)->nullable(True)->default('')->comment('新闻用户评论');
            $table->tinyInteger('news_isRead')->unsigned()->nullable(false)->default(0)->comment('新闻阅读状态(0-未阅读,1-已阅读)');
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
        Schema::dropIfExists('news');
    }
}
