<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\ShopFootprint;
use Illuminate\Support\Facades\Validator;

class ShopFootprintController extends ApiController
{
    // 足迹列表
    public function getList(Request $request)
    {
        if (empty(\Auth::user()->id)) {
            $uid = 0;
        } else {
            $uid = \Auth::user()->id;
        }
        $outData = ShopFootprint::getList(['uid'=>$uid]);
         return $this->success($outData);
    }

}