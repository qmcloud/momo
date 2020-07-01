<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\ShopComment;
use App\Logic\ShopCommentLogic;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ShopCommentController extends ApiController
{

    function __construct()
    {

    }

    // 获取评论列表
    public function getCommentList(Request $request){
        // 参数校验
        $validator = Validator::make($request->all(),
            [
                'valueId' => 'required',
                'typeId' => 'required|numeric',
                'showType'=>'numeric'
            ],
            [
                'valueId.required' => '商品id参数缺失',
                'typeId.required' => '评论类型参数缺失',
                'typeId.numeric' => '请输入正确的评论类型',
            ]
        );
        if ($validator->fails()) {
            return $this->failed($validator->errors(), 403);
        }
        $commentList = ShopCommentLogic::getCommentList(['value_id' =>$request->valueId,'type_id'=>$request->typeId],$request->showType,10);
        return $commentList;
    }

    // 评论总数
    public function getCommentCount(Request $request){
        $validator = Validator::make($request->all(),
            [
                'valueId' => 'required',
                'typeId' => 'required|numeric',
            ],
            [
                'valueId.required' => '商品id参数缺失',
                'typeId.required' => '评论类型参数缺失',
                'typeId.numeric' => '请输入正确的评论类型',
            ]
        );
        if ($validator->fails()) {
            return $this->failed($validator->errors(), 403);
        }
        $where = [
            'value_id' =>$request->valueId,
            'type_id'=>$request->typeId,
            'status'=>ShopComment::STATE_ON
        ];
        $hasPicCount = ShopComment::has('get_comment_picture')->where($where)->count();
        $allCount = ShopComment::where($where)->count();
        return $this->success(['hasPicCount'=>$hasPicCount,'allCount'=>$allCount]);
    }

    // 发表评论
    public function commentAdd(Request $request){
        $validator = Validator::make($request->all(),
            [
                'valueId' => 'required',
                'typeId' => 'required|numeric',
                'content'=>  'required|min:5',
            ],
            [
                'valueId.required' => '商品id参数缺失',
                'typeId.required' => '评论类型参数缺失',
                'typeId.numeric' => '请输入正确的评论类型',
            ]
        );
        if ($validator->fails()) {
            return $this->failed($validator->errors(), 403);
        }
        $user_id = 0;
        if(!empty(\Auth::user()->id)){
            $user_id = \Auth::user()->id;
        }
        $conmentModel = new ShopComment();
        $conmentModel->type_id = $request->typeId;
        $conmentModel->value_id = $request->valueId;
        $conmentModel->content = $request->input('content');
        $conmentModel->add_time = Carbon::now();
        $conmentModel->user_id = $user_id;
        $re = $conmentModel->save();
        return $this->message('添加成功');
    }



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
        if($info->is_attention ==ShopCollect::STATE_ATTENTION){
            $info->is_attention = ShopCollect::STATE_NOT_ATTENTION;
            $info->save();
            $type = 'del';
        }else{
            $info->is_attention = ShopCollect::STATE_ATTENTION;
            $info->save();
        }
        return  $this->success(['type'=>$type]);

    }


}