<?php
/**
 * sqc
 * xiaoT科技
 */
namespace App\Logic;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\ShopRegion;
use App\Models\ShopAddress;
use App\Http\Resources\ShopAddress as ShopAddressResource;

class AddressLogic
{

    /**
     * 根据省区县 获取宿舍和学校信息
     * @param  [int] $parent_id 上级地区id
     */
    public function getRegionList($parent_id)
    {
        return ShopRegion::getList(['parent_id' =>$parent_id]);
    }

    /**
     * 获取收货地址列表
     * @param  [int] $where
     */
    static public function getAddrList($where)
    {
        $list = ShopAddress::getList($where);
        return ShopAddressResource::collection($list);
    }

    static public function getOneAddr($addressId,$uid = 0){

        if(empty($addressId) && $uid){
            $info = ShopAddress::getDefault($uid);
        }else{
            $info = ShopAddress::getOne(['id' =>$addressId]);
        }
        if(empty($info)){
            return ['id'=>-1];
        }
        return new ShopAddressResource($info);
    }

    static public function getRegionNameById($addressId){
        $where['id'] = $addressId;
        $region = ShopRegion::getOne($where);
        return $region['name'];
    }
}
