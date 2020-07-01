<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\ShopTopic;
use App\Http\Resources\ShopTopic as ShopTopicResource;
use Illuminate\Support\Facades\Validator;

class ShopTopicController extends ApiController
{
    // 获取专题列表
    public function getTopicList(Request $request)
    {
        return ShopTopicResource::collection(ShopTopic::getTopicListByPage())->additional(['code' => 200]);
    }

    // 获取专题详情
    public function getTopicDetail(Request $request)
    {
        // 参数校验
        $validator = Validator::make($request->all(),
            [
                'id' => 'required',
            ],
            [
                'id.required' => '参数缺失',
            ]
        );
        if ($validator->fails()) {
            return $this->failed($validator->errors(), 403);
        }
        $outData = new ShopTopicResource(ShopTopic::getTopicDetail(['id' => $request->id]));
        return $this->success($outData);
    }

}