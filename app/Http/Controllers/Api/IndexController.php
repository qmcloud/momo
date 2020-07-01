<?php

namespace App\Http\Controllers\Api;

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
        echo 1111;die;
        $outData = [];
        $list = ModuleLogic::getHomeSpecial(['item_status'=>SpecialItem::IFSHOW_YES]);
        $outData['navList'] = Navigation::getNavList();
        $outData['itemList'] = ModuleData::collection($list)->additional(['code' => 200]);
        return $this->success($outData);
    }
}
