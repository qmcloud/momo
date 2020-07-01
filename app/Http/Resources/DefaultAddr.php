<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class DefaultAddr extends Resource
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
            'id' => $this->id,
            'provinceId' => $this->aid_p,
            'cityId' => $this->aid_c,
            'districtId' => $this->aid_a,
            'provinceStr' => $this->province,
            'cityStr' => $this->city,
            'areaStr' => $this->area,
            'linkMan' => $this->true_name,
            'address' => $this->addr,
            'mobile' => $this->mobile,
            'code' => $this->postcode,
            'isDefault' => $this->is_default,
            'sid' => $this->sid,
            'did' => $this->did,
            'school_name' => $this->school_name,
            'dorm_name' => $this->dorm_name,
        ];
    }
}
