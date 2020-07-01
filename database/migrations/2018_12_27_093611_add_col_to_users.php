<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    
        Schema::table('users', function (Blueprint $table) {
            $table->integer('user_points')->unsigned()->default(0)->comment('会员积分');
            $table->decimal('available_predeposit', 10)->default(0.00)->comment('预存款可用金额');
            $table->decimal('freeze_predeposit', 10)->default(0.00)->comment('预存款冻结金额');
			$table->integer('user_exppoints')->unsigned()->default(0)->comment('会员经验值');
            $table->string('join_sn',40)->nullable()->comment('邀请码');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       
    }
}
