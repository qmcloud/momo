<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class Bargain extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $pics = [];
        if (!empty($this->images)) {
            $pic = $this->images;
            foreach ($pic as $eachPic) {
                $pics[] = config('filesystems.disks.oss.url') . '/' . $eachPic;

            }
        }
        $goods_pics = [];
        if (!empty($this->goods) && !empty($this->goods->list_pic_url)) {
            $pic = $this->goods->list_pic_url;
            foreach ($pic as $eachPic) {
                $goods_pics[] = config('filesystems.disks.oss.url') . '/' . $eachPic;

            }
        }
        $goodsNum = $this->total_num - $this->sales;
        $goodsNum = ($goodsNum > $this->goods->goods_number) ? $this->goods->goods_number : $goodsNum;
        return [
            "id" => $this->id,
            "title" => $this->title,
            "description" => $this->description,
            "bargain_images" => $pics,
            "list_pic_url" => $goods_pics,
            "start_time" => $this->description,
            "stop_time" => $this->stop_time,
            "sales" => $this->sales,
            "limit_num" => $this->limit_num,
            "min_price" => $this->min_price,
            "retail_price" => $this->goods->retail_price,
            "goods_brief" => $this->goods->goods_brief,
            "goods_number" => $goodsNum,// 庫存
            "goods_desc" => $this->goods->goods_desc,
        ];
    }

}
