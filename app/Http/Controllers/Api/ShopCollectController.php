<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\ShopCollect;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ShopCollect as ShopCollectResource;

class ShopCollectController extends ApiController
{
    // 添加或取消收藏
    public function addordelete(Request $request)
    {
        // 参数校验
        $validator = Validator::make($request->all(),
            [
                'typeId' => 'required',
                'valueId' => 'required|numeric',
            ],
            [
                'typeId.required' => '参数缺失',
                'valueId.required' => '参数缺失',
                'valueId.numeric' => '非法参数',
            ]
        );
        if ($validator->fails()) {
            return $this->failed($validator->errors(), 403);
        }

        if(empty(\Auth::user()->id)){
            $user_id = 0;
        }else{
            $user_id = \Auth::user()->id;
        }
        $where['user_id'] = $user_id;
        $where['value_id'] = $request->valueId;
        $where['type_id'] = $request->typeId;
        $info = ShopCollect::getCollectDetail($where);
        $type =  'add';
        if(empty($info->value_id)){
            // 添加
            $newCollect = new ShopCollect();
            $newCollect->user_id = $user_id;
            $newCollect->value_id = $request->valueId;
            $newCollect->type_id = $request->typeId;
            $newCollect->add_time = time();
            $newCollect->is_attention = ShopCollect::STATE_ATTENTION;
            $newCollect->save();
            return  $this->success(['type'=>$type]);
        }
        if($info->is_attention == ShopCollect::STATE_ATTENTION){
            $info->is_attention = ShopCollect::STATE_NOT_ATTENTION;
            $info->save();
            $type = 'del';
        }else{
            $info->is_attention = ShopCollect::STATE_ATTENTION;
            $info->save();
        }
        return  $this->success(['type'=>$type]);

    }

    /**
     * 获取收藏列表
     */
    public function getList(Request $request){
        // 参数校验
        $validator = Validator::make($request->all(),
            [
                'typeId' => 'required',
            ],
            [
                'typeId.required' => '参数缺失',
            ]
        );

        if ($validator->fails()) {
            return $this->failed($validator->errors(), 403);
        }
        if(empty(\Auth::user()->id)){
            $user_id = 0;
        }else{
            $user_id = \Auth::user()->id;
        }

        $list = ShopCollect::getList(['user_id'=>$user_id,'type_id'=>$request->typeId]);
        return $this->success(ShopCollectResource::collection($list));
    }



}