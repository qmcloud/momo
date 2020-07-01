<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldToProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shop_product', function (Blueprint $table) {
            $table->string('goods_specification_names',255)->default('')->comment('规格键名中文');
            $table->string('goods_spec_item_ids',50)->default('')->comment('规格条目ids');
            $table->string('goods_spec_item_names',255)->default('')->comment('规格条目名称');
        });
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
        Schema::table('shop_product', function (Blueprint $table) {
            $table->dropColumn('goods_specification_names');
            $table->dropColumn('goods_spec_item_ids');
            $table->dropColumn('goods_spec_item_names');
        });
    }
}
