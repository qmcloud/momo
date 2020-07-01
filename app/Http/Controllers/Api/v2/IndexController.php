<?php

namespace App\Http\Controllers\Api\v2;

use App\Logic\ModuleLogic;
use App\Models\SpecialItem;
use App\Http\Controllers\Api\ApiController;
use  App\Http\Resources\ModuleData;
use App\Models\Navigation;

class IndexController extends ApiController
{
    /**
     * xiaoT技术首页信息version2
     * @return mixed
     */
    public function index()
    {
        // 先获取当前登录的用户信息
//        if (empty(\Auth::user())) {
//            return $this->failed('用户未登录', 401);
//        }else{
//            $user_id = \Auth::user()->id;
//        }
        $outData = [];
        $list = ModuleLogic::getHomeSpecial(['item_status'=>SpecialItem::IFSHOW_YES]);
        $outData['navList'] = Navigation::getNavList();
        $outData['itemList'] = ModuleData::collection($list)->additional(['code' => 200]);
        return $this->success($outData);
    }
}
