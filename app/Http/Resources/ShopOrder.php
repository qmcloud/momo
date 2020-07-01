<?php

namespace App\Http\Resources;

use App\Models\ShopOrder as ShopOrderDB;
use Illuminate\Http\Resources\Json\Resource;
use App\Logic\AddressLogic;

class ShopOrder extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            // 主表信息
            "id" => $this->id,
            "order_sn" => $this->order_sn,
            "order_status" => $this->order_status,
            "shipping_status" => $this->shipping_status,
            "pay_status" => $this->pay_status,
            "consignee" => $this->consignee,
            "country" => $this->country,
            "city" => $this->city,
            "district" => $this->district,
            "address" => $this->address,
            "mobile" => $this->mobile,
            "postscript" => $this->postscript,
            "pay_name" => $this->pay_name,
            "pay_id" => $this->pay_id,
            "actual_price" => $this->actual_price,
            "order_price" => $this->order_price,
            "goods_price" => $this->goods_price,
            "add_time" => $this->add_time,
            "confirm_time" => $this->confirm_time,
            "pay_time" => $this->pay_time,
            "coupon_price" => $this->coupon_price,
            "order_status_text" => ShopOrderDB::getStatusDisplayMap()[$this->order_status],
            'full_region' => AddressLogic::getRegionNameById($this->province) .' '. AddressLogic::getRegionNameById($this->city).' '.AddressLogic::getRegionNameById($this->district),
            "handleOption" => [
                'pay' => ($this->order_status == ShopOrderDB::STATUS_WAIT_PAY) ? 1 : 0,
            ],
            // 附表信息
            "goodsList" => ShopOrderGoods::collection($this->orderGoods),

        ];
    }

}
