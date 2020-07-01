<?php
/**
 * sqc @小T科技 2018.10.06
 *
 *
 */
namespace App\Logic;

use function GuzzleHttp\default_ca_bundle;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\ShopGoods;
use App\Models\SpecialItem;


class ModuleLogic
{

    public function __construct()
    {

    }

    static public function getModuleBCheckedGoodsById($id,$isGetIds='')
    {
        $specialItem = SpecialItem::find($id);
        if(empty($specialItem) || empty($specialItem->item_data)){
            return [];
        }
        $ids = explode(',',ltrim($specialItem->item_data,','));
        if($isGetIds){
            return $ids;
        }
        //var_dump($goodsList->toArray());die;
        return ShopGoods::whereIn('id',$ids)->get();
        //return ShopGoodsLogic::getGoodsList(['id'=>$ids]);
    }

    /**
     * 获取首页专题
     */
    static public function getHomeSpecial($where = []){
        $where = array_merge([
            'special_id' => 0
        ],$where);
        return static::specialList($where);
    }

    /**
     * 获取专题列表
     */
    static public function specialList($where){
        $model = new SpecialItem();
        $list = $model->with('carousels')->where($where)->orderByRaw('sort desc')->get();
        foreach ($list as $itemKey => &$itemVal){
            switch ($itemVal->item_type) {
                case 'adv':
                    # code...
                    break;
                case 'moduleB':
                    // 商品
                    $ids = explode(',',ltrim($itemVal->item_data,','));
                    $itemVal->goodsList = ShopGoods::whereIn('id',$ids)->get();
                    break;
                case 'moduleC':

                    break;
                case 'moduleE':
                    // 商品
                    $ids = explode(',',ltrim($itemVal->item_data,','));
                    $itemVal->goodsList = ShopGoods::whereIn('id',$ids)->get();
                    break;
                case 'moduleF':
                    // 商品
                    $ids = explode(',',ltrim($itemVal->item_data,','));
                    $itemVal->goodsList = ShopGoods::whereIn('id',$ids)->get();
                    break;
                default:
                    # code...
                    break;
            }
        }
        return $list;
    }
}
