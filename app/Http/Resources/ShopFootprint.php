<?php

namespace App\Http\Resources;

use App\Models\ShopCategory as ShopCategoryDB;
use Illuminate\Http\Resources\Json\Resource;

class ShopFootprint extends Resource
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
            "goods_id"=> $this->shop_goods->id,
            "goods_name"=> $this->shop_goods->goods_name,
            "primary_pic_url"=>  config('filesystems.disks.oss.url').'/'.$this->shop_goods->primary_pic_url,
            "goods_brief"=> $this->shop_goods->goods_brief,
            "retail_price"=> $this->shop_goods->retail_price,
            "add_time"=> $this->add_time,
        ];
    }

}
