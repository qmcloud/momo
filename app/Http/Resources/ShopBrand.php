<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ShopBrand extends Resource
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
        if (!empty($this->list_pic_url)) {
            $pic = $this->list_pic_url;
            foreach ($pic as $eachPic) {
                $pics[] =  config('filesystems.disks.oss.url').'/'.$eachPic;

            }
        }
        return [
            "id"=>$this->id,
            "name"=> $this->name,
            "list_pic_url"=> $pics,
            "simple_desc"=> $this->simple_desc,
            "pic_url"=>  config('filesystems.disks.oss.url').'/'.$this->pic_url,
            "is_show"=> $this->is_show,
            "floor_price"=> $this->floor_price,
            "new_pic_url"=> config('filesystems.disks.oss.url').'/'.$this->new_pic_url,
        ];
    }

}
