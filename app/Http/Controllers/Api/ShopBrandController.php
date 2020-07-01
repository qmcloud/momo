<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\ShopBrand;
use Illuminate\Support\Facades\Validator;

class ShopBrandController extends ApiController
{
    // 获取品牌列表
    public function getList(Request $request)
    {
        return [];
    }

    // 获取品牌详情
    public function getDetail(Request $request)
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
        $outData = ShopBrand::getDetail(['id'=>$request->id]);;
        return $this->success($outData);
    }

}