<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\Models\ShopCoupon;
use App\User;


class ShopMyCoupon extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id"=>$this->id,
            "coupon_id"=>$this->coupon_id,
            "name"=> $this->getCoupon->name,
            'max_amount' => $this->getCoupon->max_amount,
            'min_amount' => $this->getCoupon->min_amount,
            "min_goods_amount"=> $this->getCoupon->min_goods_amount,
            "type_money"=> (int)$this->getCoupon->type_money,
            "icon"=> $this->getCoupon->icon,
            'type' => $this->getCoupon->type,
            'type_label' => $this->getCoupon->type==ShopCoupon::TYPE_DISCOUNT ? '折':'元',
            'brief' => $this->getCoupon->brief,
            "desc"=> $this->getCoupon->desc,
            "expire_day"=> $this->getCoupon->expire_day,
            "use_status"=> $this->use_status,
            "coupon_number"=> $this->coupon_number,
            "uid"=> $this->uid,
            "used_time"=>$this->used_time,
            'reward_time' => $this->reward_time,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
        ];
    }

}
