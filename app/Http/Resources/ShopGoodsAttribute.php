<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ShopGoodsAttribute extends Resource
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
            "goods_id"=>$this->goods_id,
            "name"=> $this->get_attribute->name,
            "value"=> $this->value,
        ];
    }

}
