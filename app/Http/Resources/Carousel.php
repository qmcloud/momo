<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class Carousel extends Resource
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
            "businessId"=>$this->goods_id,
            "spec_item_id"=>$this->spec_item_id,
            "type"=> $this->carousel_type,
            "title"=> $this->carousel_title,
            "picUrl"=> config('filesystems.disks.oss.url').'/'.$this->carousel_img,
            "info"=> $this->carousel_info,
            "state"=> $this->state,
            "carousel_type"=> $this->carousel_type,
            "carousel_type_data"=> $this->carousel_type_data,
        ];
    }
}
