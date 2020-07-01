<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ShopCollect extends Resource
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
            "name"=> $this->shop_goods['goods_name'],
            "value_id"=> $this->value_id,
            "retail_price"=> $this->shop_goods['retail_price'],
            "list_pic_url"=> config('filesystems.disks.oss.url').'/'.$this->shop_goods['primary_pic_url'],
            "goods_brief"=> $this->shop_goods['goods_brief'],
            "add_time"=> date('Y-m-d H:i:s',$this->add_time),
        ];
    }

}
