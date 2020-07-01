<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class GoodDetail extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
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
        if (!empty($this->goods_carousel)) {
            $carouselPic = $this->goods_carousel;
            foreach ($carouselPic as $eachPic) {
                $carouselPics[] = [
                    'pic' => config('filesystems.disks.oss.url').'/'.$eachPic
                ];
            }
        } else {
            $carouselPics[] = [
                'pic' => config('filesystems.disks.oss.url').'/'.$this->goods_main_image
            ];
        }

        $content = '';
        foreach ($pics as $eachPic) {
            $content .= '<div><img src="'.$eachPic['pic'].'"></div>';
        }

        return [
            'id' => $this->id,
            'pics' => $pics,
            'carousel' => $carouselPics,
            'basicInfo' => [
                'id' => $this->id,
                'name' => $this->goods_name,
                'minPrice' => $this->goods_price,
                'numberOrders' => $this->goods_salenum,
                'pic' => config('filesystems.disks.oss.url').'/'.$this->goods_main_image,
                'stores' => $this->goods_storage,
            ],
            'logistics' =>1, // 需要填写收货地址
            'content' => $content,
        ];
    }
}
