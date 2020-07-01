<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class Special extends Resource
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
            "class_id"=> $this->class_id,
            "title"=> $this->special_title,
            "icon"=> config('filesystems.disks.oss.url').'/'.$this->icon,
            "link_url"=> $this->link_url,
        ];
    }
}
