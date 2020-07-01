<?php

namespace App\Http\Resources;

use App\Models\ShopCategory as ShopCategoryDB;
use Illuminate\Http\Resources\Json\Resource;

class ShopGoods extends Resource
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
        $specification_info = [];
        if($this->products){
            foreach($this->products as $product){
                $specification_ids = explode('_',$product->goods_specification_ids);
                $specification_names = explode('_',$product->goods_specification_names);
                $spec_item_ids = explode('_',$product->goods_spec_item_ids);
                $spec_item_names = explode('_',$product->goods_spec_item_names);
                foreach($specification_ids as $k =>$s_ids){

                    $specification_info[$s_ids]['sp_id'] = $s_ids;
                    $specification_info[$s_ids]['sp_name'] = $specification_names[$k];
                    if(!isset($specification_info[$s_ids]['items'])){
                        $specification_info[$s_ids]['items'] = [];
                    }
                    $specification_info[$s_ids]['items'][$spec_item_ids[$k]] = [
                        'sp_item_id' =>$spec_item_ids[$k],
                        'sp_item_name' =>$spec_item_names[$k],
                    ];
                }
            }
            $specification_info = array_values($specification_info);
        }
        return [
            "id"=>$this->id,
            "goods_name"=> $this->goods_name,
            "goods_number"=> $this->goods_number,
            "keywords"=> $this->keywords,
            "goods_brief"=> $this->goods_brief,
            "goods_desc"=> $this->goods_desc,
            "counter_price"=> $this->counter_price,
            "extra_price"=> $this->extra_price,
            "is_new"=> $this->is_new,
            "goods_unit"=> $this->goods_unit,
            "primary_pic_url"=>  config('filesystems.disks.oss.url').'/'.$this->primary_pic_url,
            "list_pic_url"=> $pics,
            "retail_price"=> $this->retail_price,
            "sell_volume"=> $this->sell_volume,
            "unit_price"=> $this->unit_price,
            "promotion_desc"=> $this->promotion_desc,
            "promotion_tag"=> $this->promotion_tag,
            "vip_exclusive_price"=> $this->vip_exclusive_price,
            "is_vip_exclusive"=> $this->is_vip_exclusive,
            "is_limited"=> $this->is_limited,
            "is_hot"=> $this->is_hot,
            "products"=> $this->products,
            'specification_info'=>$specification_info
        ];
    }

}
