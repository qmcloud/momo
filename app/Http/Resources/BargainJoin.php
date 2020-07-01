<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class BargainJoin extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $goods_spec_item_names = '';
        $price = $this->goods->retail_price;
        if ($this->product_id) {
            $goods_spec_item_names = $this->product->goods_spec_item_names;
            $price = $this->product->retail_price;
        }
        $bargainStatus = 0;
        if ($this->bargain_price_min == $this->bargain_price || $this->help_num >= $this->bargain->help_num) {
            $bargainStatus = 1;
        }
        return [
            "id" => $this->id,
            "uid" => $this->uid,
            "product_id" => $this->product_id,
            "goods_id" => $this->goods_id,
            "bargain_price" => $this->bargain_price,
            "bargain_price_min" => $this->bargain_price_min,
            "goods_name" => $this->goods->goods_name,
            "retail_price" => $price,
            "bargainStatus" => $bargainStatus,
            "primary_pic_url" => config('filesystems.disks.oss.url') . '/' . $this->goods->primary_pic_url,
            "title" => $this->bargain->title,
            "start_time" => $this->bargain->start_time,
            "stop_time" => $this->bargain->stop_time,
            "price" => $this->price,
            "help_num" => $this->help_num,
            "goods_spec_item_names" => $goods_spec_item_names,
            "helpList" => BargainHelpList::collection($this->get_helps),
        ];
    }

}
