<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\ShopCategory;
use App\Http\Resources\ShopCategory as ShopCategoryResource;
use Illuminate\Support\Facades\Validator;

class ShopCategoryController extends ApiController
{
    // 分类目录全部分类数据接口
    public function getCatalogIndex(Request $request)
    {
        // 获取顶级分类
        $parentCategory = ShopCategory::getParentCategory();
        $currentCategory = ShopCategory::getChildrenCategoryByParentId($parentCategory[0]['id']);
        $outData = [
            'categoryList' => ShopCategoryResource::collection($parentCategory),
            'currentCategory' =>new ShopCategoryResource($parentCategory[0]),
        ];
        return $this->success($outData);
    }

    // 分类目录当前分类数据接口
    public function getCatalogCurrent(Request $request)
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
        $parentCategory = ShopCategory::getParentCategory(['id'=>$request->id]);
        $currentCategory = new ShopCategoryResource($parentCategory[0]);
        return $this->success($currentCategory);
    }


}