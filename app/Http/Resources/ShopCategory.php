<?php

namespace App\Http\Resources;

use App\Models\ShopCategory as ShopCategoryDB;
use Illuminate\Http\Resources\Json\Resource;

class ShopCategory extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $subCategoryList = ShopCategory::collection(ShopCategoryDB::getChildrenCategoryByParentId($this->id));
        return [
            "id"=>$this->id,
            "name"=> $this->name,
            "keywords"=> $this->keywords,
            "front_desc"=> $this->front_desc,
            "sort_order"=> $this->sort_order,
            "banner_url"=> config('filesystems.disks.oss.url').'/'.$this->banner_url,
            "icon_url"=> config('filesystems.disks.oss.url').'/'.$this->icon_url,
            "img_url"=> config('filesystems.disks.oss.url').'/'.$this->img_url,
            "wap_banner_url"=> config('filesystems.disks.oss.url').'/'.$this->img_url,
            "level"=> $this->level,
            "front_name"=> $this->front_name,
            'subCategoryList' =>$subCategoryList
        ];
    }

}
