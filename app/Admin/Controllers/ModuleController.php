<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Models\SpecialItem;
use App\Logic\ModuleLogic;
use Illuminate\Support\Facades\Input;

class ModuleController extends Controller
{
    public function index()
    {
        return Admin::content(function (Content $content) {


            $content->header('专栏');
            $content->description('专栏控制台');

            $content->body($this->getView());
        });

    }
    public function getView(){
        $specialId = Input::get('id',0);
        return view('admin.module',compact('specialId'));
    }

    public function module_list(){
        $specialId = Input::get('id',0);
        if(!$specialId){
            $list = ModuleLogic::getHomeSpecial();
        }else{
            $list = ModuleLogic::specialList(['special_id' => $specialId]);
        }
        return view('admin.module.list',compact('list','specialId'));
    }
}
