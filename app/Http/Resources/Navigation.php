<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class Navigation extends Resource
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
            "link_type"=> $this->link_type,
            "nav_title"=> $this->nav_title,
            "icon"=> config('filesystems.disks.oss.url').'/'.$this->icon,
            "link_data"=> $this->link_data,
        ];
    }
}
