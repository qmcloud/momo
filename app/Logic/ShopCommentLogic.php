<?php
/**
 * sqc @小T科技 2018.03.06
 *
 *
 */
namespace App\Logic;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Resources\ShopComment as ShopCommentResource;
use App\Models\ShopComment;

class ShopCommentLogic
{

    public function __construct()
    {

    }

    // 获取评论列表
    static public function getCommentList($where= [],$showType = 0,$pagesize=''){
        if($showType == 0){
            $comments = ShopComment::getCommentList($where,$pagesize);
        }else{
            $comments = ShopComment::getCommentListPics($where,$pagesize);
        }

        return ShopCommentResource::collection($comments)->additional(['code' =>200,'status' => 'success']);
    }
}
