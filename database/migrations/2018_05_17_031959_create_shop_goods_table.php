<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateShopGoodsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('shop_goods', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('category_id')->unsigned()->default(0)->index('cat_id')->comment('分类id');
			$table->string('goods_sn', 60)->default('')->index('goods_sn')->comment('商品编号');
			$table->string('goods_name', 120)->default('')->comment('商品名称');
			$table->integer('brand_id')->unsigned()->default(0)->index('brand_id')->comment('品牌id');
			$table->integer('goods_number')->unsigned()->default(0)->index('goods_number')->comment('商品库存量');
			$table->string('keywords')->default('')->comment('商品关键词');
			$table->string('goods_brief')->default('')->comment('商品摘要');
			$table->longText('goods_desc')->nullable()->comment('商品描述');
			$table->boolean('is_on_sale')->default(1)->comment('是否上架');
			$table->smallInteger('sort_order')->unsigned()->default(100)->index('sort_order')->comment('商品排序');
			$table->boolean('is_delete')->default(0)->comment('商品删除状态 0 正常 1已删除');
			$table->integer('attribute_category')->unsigned()->default(0)->comment('商品属性分类');
			$table->decimal('counter_price', 10)->unsigned()->default(0.00)->comment('专柜价格');
			$table->decimal('extra_price', 10)->unsigned()->default(0.00)->comment('附加价格');
			$table->decimal('freight_price', 10)->unsigned()->default(0.00)->comment('运费');
			$table->boolean('is_new')->default(0);
			$table->string('goods_unit', 45)->default('')->comment('商品单位');
			$table->string('primary_pic_url')->default('')->comment('商品主图');
			$table->string('list_pic_url',500)->default('')->comment('商品列表图');
			$table->decimal('retail_price', 10)->unsigned()->default(0.00)->comment('零售价格');
			$table->integer('sell_volume')->unsigned()->default(0)->comment('销售量');
			$table->integer('primary_product_id')->unsigned()->default(0)->comment('主sku　product_id');
			$table->decimal('unit_price', 10)->unsigned()->default(0.00)->comment('单位价格，单价');
			$table->string('promotion_desc')->comment('促销描述');
			$table->string('promotion_tag', 45)->comment('促销标签');
			$table->decimal('vip_exclusive_price', 10)->unsigned()->comment('会员专享价');
			$table->boolean('is_vip_exclusive')->comment('是否是会员专属');
			$table->boolean('is_limited')->comment('是否限购');
			$table->boolean('is_hot')->default(0)->comment('是否推荐');
			$table->comment = '商城商品表';
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
		Schema::drop('shop_goods');
	}

}
