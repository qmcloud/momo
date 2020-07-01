<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\User;


class ShopComment extends Resource
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
        if (!empty($this->get_comment_picture)) {
            $pic = $this->get_comment_picture;
            foreach ($pic as $eachPic) {
                $pics[] =  config('filesystems.disks.oss.url').'/'.$eachPic->pic_url;

            }
        }
        $user = [
            'nickname' => 'T粉',
            'avatar' => '../../static/images/default.header.png'
        ];
        if($this->user_id){
            $user = User::find($this->user_id);
        }
        $shop_product = '';
        if($this->product_id){
            $shop_product =' 选择规格：'. $this->shop_product->goods_spec_item_names;
        }
        if($this->product_id){
            $shop_product =' 选择规格：'. $this->shop_product->goods_spec_item_names;
        }
        return [
            "id"=>$this->id,
            "value_id"=> $this->value_id,
            "star"=> $this->star,
            "star_str"=> $this->star >=4 ? '好评':'中评',
            'nickname' => $user['nickname'],
            'avatar' => $user['avatar'],
            "content"=> $this->content,
            "user_id"=> $this->user_id,
            "new_content"=> $this->new_content,
            "pic_list"=> $pics,
            "add_time"=>$this->created_at,
            'property' => $shop_product
        ];
    }

}
