<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use EasyWeChat\Factory;

class WeixinController extends ApiController
{

    // 微信生成的小程序码  分享的path需要是已经发布了的哦
    public function getwxacodeunlimit(Request $request)
    {
        // 参数校验
        $validator = Validator::make($request->all(),
            [
                'scene' => 'required',
                'path' => 'required',
                'width' => 'numeric',
                'get_body' => 'numeric',
            ],
            [
                'scene.required' => '参数缺失',
                'path.required' => '参数缺失',
            ]
        );
        if ($validator->fails()) {
            return $this->failed($validator->errors(), 403);
        }
        $config = config('wechat.mini_program.default');
        $app = Factory::miniProgram($config);
        $response = $app->app_code->getUnlimit($request->scene, [
            'page'  => $request->path,
            'width' => $request->width?$request->width:400,
        ]);
        if($request->get_body){
            return $this->success(['file'=>base64_encode($response->getBodyContents())]);
        }
        $name = md5($request->path.$request->scene.mt_rand(100000,999999).time());
        // 保存图片到服务器
        if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
            $filename = $response->saveAs('./images', $name.'.png');
        }
        return $this->success(['file'=>env('APP_URL').'/images'.$filename]);
    }
}