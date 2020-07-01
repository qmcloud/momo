<?php

namespace App\Http\Resources;

use App\Models\ShopCategory as ShopCategoryDB;
use Illuminate\Http\Resources\Json\Resource;

class ShopCart extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $price = 0;
        $product = '';
        if($this->product_id){
            $price = $this->checked_products->retail_price;
            $product = $this->checked_products->goods_spec_item_names;
        }else{
            $price = $this->shop_goods->retail_price;
        }
        $label = '';
        if($this->checked_products){
            $label = '选择规格 ：';
            $spec = $this->checked_products->goods_specification_names;
            $specArr = explode('_',$spec);
            $spec_item = $this->checked_products->goods_spec_item_names;
            $spec_itemArr = explode('_',$spec_item);
            foreach($specArr as $k => $v){
                $label .= $v.':'.$spec_itemArr[$k].' ';
            }
        }
        return [
            "id"=>$this->id,
            "user_id"=> $this->uid,
            "goods_id"=> $this->goods_id,
            "goods_sn"=> $this->goods_sn,
            "goods_name"=> $this->goods_name.' '.$product,
            "market_price"=> $this->market_price,
            "product_id"=> $this->product_id,
            "retail_price"=> $price,
            "label"=> $label,
            "number"=> $this->number,
            "checked"=> $this->checked,
            'freight_price' => $this->shop_goods->freight_price,
            "list_pic_url"=>  config('filesystems.disks.oss.url').'/'.$this->list_pic_url,
            "primary_pic_url"=>  config('filesystems.disks.oss.url').'/'.$this->shop_goods->primary_pic_url,
        ];
    }

}
