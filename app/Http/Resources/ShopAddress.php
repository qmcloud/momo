<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ShopAddress extends Resource
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
            "name"=> $this->user_name,
            "country_id"=> $this->country_id,
            "country"=> $this->country,
            "province_id"=> $this->province_id,
            "province_name"=> $this->province,
            "city_id"=> $this->city_id,
            "city_name"=> $this->city,
            "district_id"=> $this->district_id,
            "district_name"=> $this->district,
            "address"=> $this->address,
            "mobile"=> $this->mobile,
            "is_default"=> $this->is_default,
            "full_region"=> $this->province.' '.$this->city.' '.$this->district.' ' ,
        ];
    }

}
