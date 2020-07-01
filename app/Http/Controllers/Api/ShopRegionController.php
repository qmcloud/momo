<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Logic\AddressLogic;

class ShopRegionController extends ApiController
{

    // 获取区域列表
    public function regionList(Request $request)
    {
        // 参数校验
        $validator = Validator::make($request->all(),
            [
                'parentId' => 'required',
            ],
            [
                'parentId.required' => '参数缺失',
            ]
        );
        if ($validator->fails()) {
            return $this->failed($validator->errors(), 403);
        }
        $addressLogic = new AddressLogic();
        $regionList =  $addressLogic->getRegionList($request->parentId);
        return $this->success($regionList);
    }

}