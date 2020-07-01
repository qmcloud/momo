<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ProjectType extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $pics =[];
        if (!empty($this->carousel_imgs)) {
            $pic = $this->carousel_imgs;
            foreach ($pic as $eachPic) {
                $pics[] =  config('filesystems.disks.oss.url').'/'.$eachPic;

            }
        }
        return [
            "id"=>$this->id,
            "goods_name"=> $this->type_name,
            "goods_desc"=> $this->type_desc,
            "type_name"=> $this->type_name,
            "title"=> $this->special_title,
            "type_img"=> config('filesystems.disks.oss.url').'/'.$this->type_img,
            "carousel_imgs"=> $pics,
            "description"=> $this->description,
            "salenum"=> $this->salenum,
            "goods_price"=> $this->basal_price,
        ];
    }
}
