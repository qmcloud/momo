<?php

namespace App\Http\Resources;

use App\Models\ShopOrder as ShopOrderDB;
use Illuminate\Http\Resources\Json\Resource;

class ShopOrderGoods extends Resource
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
            "goods_name"=> $this->goods_name,
            "number"=> $this->number,
            "list_pic_url"=> (strpos($this->list_pic_url,'http')===false) ?config('filesystems.disks.oss.url').'/'.$this->list_pic_url:$this->list_pic_url,
            "actual_price"=> $this->actual_price,
        ];
    }

}
