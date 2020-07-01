 <?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateShopCartTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('shop_cart', function(Blueprint $table)
		{
			$table->increments('id')->comment('购物车表');
			$table->integer('uid')->unsigned()->default(0)->comment('用户id');
			$table->char('session_id', 32)->default('')->index('session_id')->comment('session_id');
			$table->integer('goods_id')->unsigned()->default(0)->comment('商品');
			$table->string('goods_sn', 60)->default('')->comment('商品sn');
			$table->integer('product_id')->unsigned()->default(0)->comment('主sku id');
			$table->string('goods_name', 120)->default('')->comment('商品名称');
			$table->decimal('market_price', 10)->unsigned()->default(0.00)->comment('市场价格');
			$table->decimal('retail_price', 10)->default(0.00)->comment('商品单价');
			$table->smallInteger('number')->unsigned()->default(0)->comment('购买数量');
			$table->text('goods_specifition_name_value', 65535)->nullable()->comment('规格属性组成的字符串，用来显示用');
			$table->string('goods_specifition_ids', 60)->default('')->comment('product表对应的goods_specifition_ids');
			$table->boolean('checked')->default(1)->comment('是否选中');
			$table->string('list_pic_url')->default('')->comment('商品列表图');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('shop_cart');
	}

}
