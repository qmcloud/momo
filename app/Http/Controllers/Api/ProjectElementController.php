<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Logics\ProjectControl;
use App\Models\ProjectType;
use Illuminate\Support\Facades\Validator;

class ProjectElementController extends ApiController
{
    // 获取项目类型
    public function getProjectType(Request $request)
    {
        $outData = ProjectControl::getJsonProjectTypes();
        return $this->success($outData);
    }

    // 技术项目模块拼装成商品
    public function getProjectGoods(Request $request)
    {
        // 参数校验
        $validator = Validator::make($request->all(),
            [
                'type_id' => 'required',
            ],
            [
                'type_id.required' => '项目类型参数缺失',
            ]
        );
        if ($validator->fails()) {
            return $this->failed($validator->errors(), 403);
        }
        $outData['info'] = ProjectType::getProjectInfo(['id' => $request->type_id]);
        $outData['sku'] = ProjectControl::getJsonProjectGoodsByTypeId($request->type_id);
        return $this->success($outData);
    }

    // 技术商品转换成商城商品
    public function transform(Request $request)
    {
        // 参数校验
        $validator = Validator::make($request->all(),
            [
                'type_id' => 'required',
                'specificationList' => 'required',
            ],
            [
                'type_id.required' => '项目类型参数缺失',
                'specificationList.required' => '未选择商品',
            ]
        );
        if ($validator->fails()) {
            return $this->failed($validator->errors(), 403);
        }
        $outData = ProjectControl::transform($request->type_id,$request->specificationList);
        return $this->success($outData);
    }
}