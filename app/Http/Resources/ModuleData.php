<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ModuleData extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            "id"=>$this->id,
            "item_type"=> $this->item_type,
            "item_data"=> $this->item_data,
            "item_title"=> $this->item_title,
            "item_desc"=> $this->item_desc,
            "item_link_special_id"=> $this->item_link_special_id,
            "goodsList"=> [],
            "carousels"=> [],
        ];
        if($this->goodsList){
            $data['goodsList'] = ShopGoods::collection($this->goodsList);
        }
        if($this->carousels){
            $data['carousels'] = Carousel::collection($this->carousels);
        }
        return $data;
    }

}
