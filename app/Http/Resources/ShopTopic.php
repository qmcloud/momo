<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ShopTopic extends Resource
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
        if (!empty($this->item_pic_url)) {
            $pic = $this->item_pic_url;
            foreach ($pic as $eachPic) {
                $pics[] =  config('filesystems.disks.oss.url').'/'.$eachPic;

            }
        }
        return [
            "id"=>$this->id,
            "title"=> $this->title,
            "content"=> $this->content,
            "avatar"=> config('filesystems.disks.oss.url').'/'.$this->avatar,
            "price_info"=> $this->price_info,
            "subtitle"=> $this->subtitle,
            "read_count"=> $this->read_count,
            "scene_pic_url"=> config('filesystems.disks.oss.url').'/'.$this->scene_pic_url,
            "item_pic_url"=> $pics,
            "sort_order"=> $this->sort_order,
        ];
    }

}
