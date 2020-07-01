<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\User;


class ShopCouponCenter extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $user = $this->getUserCoupon? $this->getUserCoupon->toArray():[];
        $nowNu =  $this->total_num - $this->reward_num;
        $button_info =[
            'status'=>1,
            'text'=>'立即领取'
        ];
        $can_get_num = $user_num = !empty($user)?$this->limit_num - $user['coupon_number']:$this->limit_num;
        if($nowNu<0){
            $can_get_num = 0;
            $button_info =[
                'status'=>0,
                'text'=>'已领光'
            ];
        }

        if($user_num<=0){
            $can_get_num = 0;
            $button_info =[
                'status'=>0,
                'text'=>'已领取'
            ];
        }
        return [
            "id"=>$this->id,
            "name"=> $this->name,
            'max_amount' => $this->max_amount,
            'min_amount' => $this->min_amount,
            "min_goods_amount"=> $this->min_goods_amount,
            "type_money"=> (int)$this->type_money,
            'expire_type'=> $this->expire_type,
            "icon"=> $this->icon,
            'type' => $this->type,
            'brief' => $this->brief,
            "desc"=> $this->desc,
            "expire_day"=> $this->expire_day,
            "send_start_date"=> $this->send_start_date,
            "send_end_date"=> $this->send_end_date,
            "use_start_date"=>$this->use_start_date,
            'use_end_date' => $this->use_end_date,
            'use_end_date' => $this->use_end_date,
            'button_info' => $button_info,
            'can_get_num' => $can_get_num,
            'user_info' => $user,
        ];
    }

}
