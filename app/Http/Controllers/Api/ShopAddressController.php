<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Logic\AddressLogic;
use App\Models\ShopAddress;
use Illuminate\Support\Facades\DB;

class ShopAddressController extends ApiController
{

    // 收货地址列表
    public function addressList(Request $request)
    {
        if (empty(\Auth::user()->id)) {
            $user_id = 0;
        } else {
            $user_id = \Auth::user()->id;
        }
        $list = AddressLogic::getAddrList(['uid' => $user_id]);
        return $this->success($list);
    }

    // 收货地址详情
    public function addressDetail(Request $request)
    {
        // 参数校验
        $validator = Validator::make($request->all(),
            [
                'id' => 'required',
            ],
            [
                'id.required' => 'id参数缺失',
            ]
        );
        if ($validator->fails()) {
            return $this->failed($validator->errors(), 403);
        }
        $info = AddressLogic::getOneAddr($request->id);
        return $this->success($info);
    }

    // 保存收货地址
    public function addressSave(Request $request)
    {
        if (empty(\Auth::user()->id)) {
            $user_id = 0;
        } else {
            $user_id = \Auth::user()->id;
        }
        // 参数校验
        $validator = Validator::make($request->all(),
            [
                'id' => 'required',
                'name' => 'required',
                'mobile' => 'required',
                'province_id' => 'required',
                'city_id' => 'required',
                'district_id' => 'required',
                'address' => 'required',
                'is_default' => 'required',
            ],
            [
                'id.required' => 'id参数缺失',
                'name.required' => '收货人参数缺失',
                'mobile.required' => '收货人手机号参数缺失',
                'province_id.required' => 'province_id参数缺失',
                'city_id.required' => 'city_id参数缺失',
                'district_id.required' => 'district_id参数缺失',
                'address.required' => '详细地址参数缺失',
                'is_default.required' => 'is_default参数缺失',
            ]
        );
        if ($validator->fails()) {
            return $this->failed($validator->errors(), 403);
        }
        if ($request->id && $request->id > 0) {
            $model = ShopAddress::find($request->id);
        } else {
            $model = new ShopAddress();
        }
        $model->user_name = $request->name;
        $model->mobile = $request->mobile;
        $model->uid = $user_id;
        $model->country_id = 1;
        $model->country = '中国';
        $model->province_id = $request->province_id;
        $model->province = $request->province;
        $model->city_id = $request->city_id;
        $model->city = $request->city;
        $model->district_id = $request->district_id;
        $model->district = $request->district;
        $model->address = $request->address;
        $model->is_default = intval($request->is_default);
        $model->status = ShopAddress::STATUS_ON;
        // 开启事务
        DB::beginTransaction();
        try {
            DB::table('shop_address')
                ->where('is_default', ShopAddress::DEFAULT_ON)
                ->update(['is_default' => ShopAddress::DEFAULT_OFF]);
            $re = $model->save();
            DB::commit();
            return $this->message('操作成功');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->failed('报错失败', 403);
        }

    }

    // 删除收货地址
    public function addressDelete(Request $request)
    {
        // 参数校验
        $validator = Validator::make($request->all(),
            [
                'id' => 'required',
            ],
            [
                'id.required' => 'id参数缺失',
            ]
        );
        if ($validator->fails()) {
            return $this->failed($validator->errors(), 403);
        }
        $model = ShopAddress::find($request->id);
        $re = $model->delete();
        if ($re) {
            return $this->message('操作成功');
        }
    }


}