<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIconToSpecialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('special', 'icon')) {
            Schema::table('special', function (Blueprint $table) {
                $table->string('icon',150)->default(0)->comment('图标');
            });
        }
        //
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        if (Schema::hasColumn('special', 'icon')) {
            Schema::table('special', function (Blueprint $table) {
                $table->dropColumn('icon');
            });
        }
    }
}
