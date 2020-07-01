<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UserTableSeeder::class);
        $this->call(AdminMenuTableSeeder::class);
        $this->call(CarouselTableSeeder::class);
        $this->call(ClassesTableSeeder::class);
        $this->call(NewsTableSeeder::class);
        $this->call(ProjectFuncdotTableSeeder::class);
        $this->call(ProjectFunctypeTableSeeder::class);
        $this->call(ProjectModelTableSeeder::class);
        $this->call(ProjectTypeTableSeeder::class);
        $this->call(ShopAddressTableSeeder::class);
        $this->call(ShopAttributeTableSeeder::class);
        $this->call(ShopAttributeCategoryTableSeeder::class);
        $this->call(ShopCollectTableSeeder::class);
        $this->call(ShopCommentTableSeeder::class);
        $this->call(ShopCommentPictureTableSeeder::class);
        $this->call(ShopCouponTableSeeder::class);
        $this->call(ShopGoodsTableSeeder::class);
        $this->call(ShopGoodsAttributeTableSeeder::class);
        $this->call(ShopGoodsGalleryTableSeeder::class);
        $this->call(ShopGoodsIssueTableSeeder::class);
        $this->call(ShopGoodsSpecificationTableSeeder::class);
        $this->call(ShopOrderTableSeeder::class);
        $this->call(ShopOrderExpressTableSeeder::class);
        $this->call(ShopOrderGoodsTableSeeder::class);
        $this->call(ShopProductTableSeeder::class);
        $this->call(ShopRegionTableSeeder::class);
        $this->call(ShopShipperTableSeeder::class);
        $this->call(ShopSpecificationTableSeeder::class);
        $this->call(ShopTopicTableSeeder::class);
        $this->call(ShopTopicCategoryTableSeeder::class);
        $this->call(ShopUserCouponTableSeeder::class);
        $this->call(SpecialTableSeeder::class);
        $this->call(ShopCategoryTableSeeder::class);
        $this->call(ShopBrandTableSeeder::class);
        $this->call(SpecialItemTableSeeder::class);
        $this->call(VersionTableSeeder::class);
        $this->call(NavigationTableSeeder::class);
        $this->call(AdminMenuOneTableSeeder::class);
    }
}
