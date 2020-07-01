<?php

namespace App\Http\Controllers\Api;


use App\Models\Carousel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CreateCityDataController extends ApiController
{
    /**
     * 根据省市生成数据
     */
    public function index(Request $request)
    {
        if(!$request->provinceId){
            return [];
        }
        $province = DB::table('br_area')->where('area_id', $request->provinceId)->first();
        // 二级地区
        $dataTwo = [];
        if($province){
            $citys = DB::table('br_area')->where('area_parent_id', $request->provinceId)->get();
            foreach($citys as $k=>$v){
                // 3级地区
                $district = DB::table('br_area')->where('area_parent_id', $v->area_id)->get();
                $dataThree = [];
                foreach($district as $k2=>$v3){
                    $dataThree[] = [
                        'name' => $v3->area_name,
                        'id' => $v3->area_id,
                        'pid'=> $v->area_id,
                    ];
                }
                $dataTwo[] = [
                    'name' => $v->area_name,
                    'id' => $v->area_id,
                    'pid'=> $province->area_id,
                    'districtList' => $dataThree
                ];
            }
        }
        $dataOne = [
            'name' => $province->area_name,
            'id' => $province->area_id,
            'cityList' =>$dataTwo
        ];
        return json_encode($dataOne, JSON_UNESCAPED_UNICODE);
    }
}
