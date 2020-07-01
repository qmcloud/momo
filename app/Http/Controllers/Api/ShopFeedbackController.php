<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\ShopFeedback;
use Illuminate\Support\Facades\Validator;

class ShopFeedbackController extends ApiController
{
    // 获取反馈信息
    public function getDataList(Request $request)
    {
        return $this->success(ShopFeedback::$Option);
    }

    // 用户进行反馈
    public function feedbackHandle(Request $request)
    {
        // 参数校验
        $validator = Validator::make($request->all(),
            [
                'msg_type' => 'required',
                'msg_content' => 'required',
            ],
            [
                'msg_type.required' => '参数缺失',
                'msg_content.required' => '内容缺失',
            ]
        );
        if ($validator->fails()) {
            return $this->failed($validator->errors(), 403);
        }
        $model = new ShopFeedback();
        $model->uid = \Auth::user()->id;
        $model->user_name = \Auth::user()->name;
        $model->msg_title = ShopFeedback::$Option[$request->msg_type];
        $model->msg_type = $request->msg_type;
        $model->user_contact = $request->user_contact;
        $model->msg_status = ShopFeedback::STATUS_ON;
        $model->msg_content = $request->msg_content;
        $model->save();
        return $this->message('反馈成功');
    }

}