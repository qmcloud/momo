<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class Good extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {

        if (!empty($this->goods_description_pictures)) {
            $pic = $this->goods_description_pictures;
            foreach ($pic as $eachPic) {
                $pics[] = [
                    'pic' => config('filesystems.disks.oss.url').'/'.$eachPic
                ];
            }
        } else {
            $pics[] = [
                'pic' => config('filesystems.disks.oss.url').'/'.$this->goods_main_image
            ];
        }
        return [
            'id' => $this->id,
            'pics' => $pics,
            'name' => $this->goods_name,
            'minPrice' => $this->goods_price,
            'originalPrice' => $this->goods_marketprice,
            'numberOrders' => $this->goods_salenum,
            'pic' => config('filesystems.disks.oss.url').'/'.$this->goods_main_image,
            'stores' => $this->goods_storage,
        ];
    }
}
