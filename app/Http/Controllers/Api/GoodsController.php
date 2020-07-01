<?php

namespace App\Http\Controllers\Api;

use Validator;
use App\Logic\GoodsLogic;
use App\Models\Good;
use Illuminate\Http\Request;


class GoodsController extends ApiController
{
    /**
     * 获取商品列表信息
     */
    public function index(Request $request)
    {
        $where[] = ['goods_state','=',1];
        if ($request->input('categoryId')) {
            $where[] = ['class_id', '=', $request->input('categoryId')];
        }
        if ($request->input('nameLike')) {
            $where[] = ['goods_name', 'like', "%{$request->input('nameLike')}%"];
        }
        return GoodsLogic::getGoodsList($where);
    }

    public function detail(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'id' => 'required'
            ],
            [
                'id.required' => 'id is required'
            ]
        );

        if ($validator->fails()) {
            return $this->failed($validator->errors(), 401);
        }

        $result = Good::getGoodInfoByID($request->id);
        if ($result) {
            return $result;
        } else {
            return $this->failed('This Good is not exists', 401);
        }
    }

}
