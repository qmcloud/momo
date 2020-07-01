<?php
/**
 * User: sqc
 * Date: 18-5-7
 * Time: 下午5:12
 */

namespace App\Logics;

use App\Models\ShopOrder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Models\ProjectFuncdot;
use App\Models\ProjectFunctype;
use App\Models\ProjectModel;
use App\Models\ProjectType;
use App\Models\ShopGoods;
use App\Models\ShopGoodsTechnology;
use App\Http\Resources\ProjectType as ProjectTypeResource;

class ProjectControl
{
    const PROJECT_GOODS_CACHE_KEY = 'ProjectGoodsCacheKey';
     static public $transformHtml = "<ol> %s </ol> ";
     static public $transformHtmlLI = "<li> %s </li>";

    // 获取项目类型json格式
    static public function getJsonProjectTypes()
    {
        return ProjectTypeResource::collection(ProjectType::where([
            ['status', '=', ProjectType::STATUS_ON]
        ])->orderBy('sort', 'ASC')->get());
    }

    // 拼装获取项目 function_type  model dot
    static public function getJsonProjectGoodsByTypeId($type_id)
    {
        // 添加缓存
        $projectGoodsInfo = Cache::get(static::PROJECT_GOODS_CACHE_KEY);
        if (!$projectGoodsInfo) {
            $dbProjectFuncTypes = static::getProjectFuncTypesByOneLevel([$type_id, 0]);
            $dbProjectFuncTypes = array_column($dbProjectFuncTypes->toArray(), null, 'id');
            foreach ($dbProjectFuncTypes as $funcTypeKey => &$funcTypeVal) {
                $modelsTmp = static::getProjectModelsByOneLevel([$funcTypeVal['id']]);
                $modelsTmp = array_column($modelsTmp->toArray(), null, 'id');
                foreach ($modelsTmp as $modelsTmpKey => &$modelsTmpVal) {
                    $tmpDots = static::getProjectDotByOneLevel([$modelsTmpVal['id']]);
                    $modelsTmpVal['dots'] = array_column($tmpDots->toArray(), null, 'id');
                }
                $funcTypeVal['models'] = $modelsTmp;
            }
            $expiresAt = Carbon::now()->addMinutes(120);// 缓存2个小时
            Cache::put(static::PROJECT_GOODS_CACHE_KEY, \GuzzleHttp\json_encode($dbProjectFuncTypes), $expiresAt);
            return $dbProjectFuncTypes;
        }
        return \GuzzleHttp\json_decode($projectGoodsInfo);

    }

    // 获取项目类型
    static public function getProjectTypes($form = 1)
    {
        $default = [];
        if($form){
            $default = ['-1'=>'请选择','0' => '全部'];
        }else{
            $default = ['0' => '全部'];
        }
        $dbProjectTypes = ProjectType::pluck('type_name', 'id')->all();
        $projectTypes =   $default + $dbProjectTypes;
        return $projectTypes;
    }

    // 获取项目功能分类管理 一级操作
    static public function getProjectFuncTypesByOneLevel($type_id = [])
    {
        if (count($type_id) == 1 && in_array(0, $type_id)) {
            return ProjectFunctype::where([
                ['status', '=', ProjectFunctype::STATUS_ON]
            ])->orderBy('sort', 'ASC')->get();
        }
        $dbProjectFuncTypes = ProjectFunctype::where([
            ['status', '=', ProjectFunctype::STATUS_ON]
        ])->orderBy('sort', 'ASC')->whereIn('type_id', $type_id)->get();
        return $dbProjectFuncTypes;
    }

    // 获取项目功能模块管理  一级操作
    static public function getProjectModelsByOneLevel($functype_id = [])
    {
        if (in_array(0, $functype_id)) {
            return ProjectModel::where([
                ['status', '=', ProjectModel::STATUS_ON]
            ])->orderBy('sort', 'ASC')->get();
        }
        $dbProjectModels = ProjectModel::where([
            ['status', '=', ProjectModel::STATUS_ON]
        ])->orderBy('sort', 'ASC')->whereIn('functype_id', $functype_id)->get();
        return $dbProjectModels;
    }

    // 获取功能点
    static public function getProjectDotByOneLevel($model_id = [])
    {
        if (in_array(0, $model_id)) {
            return ProjectFuncdot::where([
                ['status', '=', ProjectFuncdot::STATUS_ON]
            ])->orderBy('sort', 'ASC')->get();
        }
        $dbProjectModels = ProjectFuncdot::where([
            ['status', '=', ProjectFuncdot::STATUS_ON]
        ])->orderBy('sort', 'ASC')->whereIn('model_id', $model_id)->get();
        return $dbProjectModels;
    }

    // 转换成商城商品
    static public function transform($type_id,$projectGoods){
        $projectTypeInfo = ProjectType::find($type_id);// 项目类型信息
        if(!$type_id || !$projectGoods || empty($projectTypeInfo)){
            return 0;
        }
        $funcTypeIds =[];
        $modelIds = [];
        $dotIds = [];
        $descHtml = '';
        $checkedProductPrice = $projectTypeInfo->basal_price;// 真实零售价格
        $checkedShowPrice = $projectTypeInfo->basal_price;// 展示价格
        foreach($projectGoods as $proVal){
            if(!empty($proVal['checkdotsNum']) && !empty($proVal['models'])){
                $funcTypeIds[] =  $proVal['id'];
                $modelsHtml = '';
                foreach($proVal['models'] as $modelVal){
                    if(empty($modelVal['dots'])){
                        continue;
                    }
                    $dotsHtml = '';
                    foreach($modelVal['dots'] as $dotsVal){
                        if(!empty($dotsVal['checked'])){
                            if(!in_array($dotsVal['model_id'],$modelIds)){
                                $modelIds[] = $dotsVal['model_id'];
                            }
                            $dotIds[] = $dotsVal['id'];
                            $checkedProductPrice = PriceCalculate($checkedProductPrice,'+',$dotsVal['discount_price']);
                            $checkedShowPrice =  PriceCalculate($checkedShowPrice,'+',$dotsVal['price']);
                            $dotsHtml .= sprintf(self::$transformHtmlLI,$dotsVal['funcdot_name']);
                        }
                    }
                    $dotsHtml = sprintf(self::$transformHtml,$dotsHtml);
                    $modelsHtml .= sprintf(self::$transformHtmlLI,$modelVal['model_name']);
                    $modelsHtml .=$dotsHtml;
                }
                $modelsHtml = sprintf(self::$transformHtml,$modelsHtml,'');
                $descHtml .= sprintf(self::$transformHtmlLI,$proVal['functype_name']);
                $descHtml .= $modelsHtml;
                $descHtml = sprintf(self::$transformHtml,$descHtml);
            }
        }
        sort($funcTypeIds);sort($modelIds);sort($dotIds);
        $goods_sn = md5($type_id.'_'.implode("_",$funcTypeIds).implode("_",$modelIds).implode("_",$dotIds));
        $goodsInfo = ShopGoods::getGoodsDetail(['goods_sn'=>$goods_sn]);
        if($goodsInfo){
            return $goodsInfo;
        }

        $newGoods = new ShopGoods();
        $newGoods->category_id = 0;  // 分类id
        $newGoods->goods_name = $projectTypeInfo->type_name; // 商品名称
        $newGoods->brand_id = $projectTypeInfo->brand_id; //品牌id
        $newGoods->is_on_sale = ShopGoods::STATE_ON_SALE; // 是否上架
        $newGoods->goods_number = 100;   // 商品库存量
        $newGoods->primary_pic_url = $projectTypeInfo->type_img;  // 商品主图
        $newGoods->list_pic_url = $projectTypeInfo->carousel_imgs;  // 商品列表图
        $newGoods->keywords = $projectTypeInfo->type_name;   // 商品关键词
        $newGoods->goods_brief = $projectTypeInfo->type_desc;   // 商品摘要
        $newGoods->goods_sn = $goods_sn;// 商品编号
        $newGoods->goods_desc = $descHtml;   // 商品描述
        $newGoods->counter_price = $checkedShowPrice;   // 专柜价格
        $newGoods->retail_price = $checkedProductPrice;  // 零售价格
        $newGoods->promotion_desc = '';// 促销描述
        $newGoods->promotion_tag = '';// 促销标签
        $newGoods->vip_exclusive_price = $checkedProductPrice;// 会员专享价
        $newGoods->is_vip_exclusive = ShopGoods::STATE_NOT_VIP;// 是否是会员专属
        $newGoods->is_limited = ShopGoods::STATE_SALE_NOT_LIMIT;// 是否限购
        $newGoods->save();
        return $newGoods;
    }


    // 获取项目类型
    static public function getJsonForProjectTypes()
    {
        $dbProjectTypes = ProjectType::select('type_name as text', 'id')->where(
            ['status'=> ProjectType::STATUS_ON]
        )->get()->toArray();
        $projectTypes = array_merge([['id'=>'-1','text'=> '请选择'],['id'=>'0','text' => '全部']], $dbProjectTypes);
        return $projectTypes;
    }
}