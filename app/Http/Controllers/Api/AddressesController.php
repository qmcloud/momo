<?php
/**
 * Created by PhpStorm.
 * Date: 2018/3/7
 * Time: 11:28
 */
namespace App\Http\Controllers\Api;

use App\Http\Requests;
use App\Models\Address;
use App\Models\Addrjson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


class AddressesController extends ApiController
{
    // 新增校外收货地区信息
    public function outsideCreate(Request $request)
    {
        // 表单验证
        $validator = Validator::make($request->all(),
            [
                'provinceId' => 'required|max:10',
                'cityId' => 'required|max:10',
                'linkMan' => 'required|max:10',
                'address' => 'required',
                'mobile' => 'required',
            ],
            [
                'provinceId.required' => '省市参数缺失',
                'cityId.required' => '城市参数缺失',
                'linkMan.required' => '收件人参数缺失',
                'address.required' => '详细地址参数缺失',
                'mobile.required' => '收货人手机号参数缺失',
            ]
        );
        if ($validator->fails()) {
            return $this->failed($validator->errors(), 401);
        }
        // 这里后面加上valicate验证
        if ($request->input('id')) {
            // 编辑
            $Addresses = Address::find($request->input('id'));
        } else {
            $Addresses = new Address;
        }
        $Addresses->uid = \Auth::user()->id;
        $Addresses->aid_p = $request->input('provinceId');
        $Addresses->aid_c = $request->input('cityId');
        $Addresses->aid_a = $request->input('districtId') ? $request->input('districtId') : 0;
        $Addresses->province = Addrjson::find(intval($request->input('provinceId')))->area_name;
        $Addresses->city = Addrjson::find(intval($request->input('cityId')))->area_name;
        if (intval($request->input('districtId'))) {
            $Addresses->area = Addrjson::find(intval($request->input('districtId')))->area_name;
        }
        $Addresses->postcode = $request->input('code', '');
        $Addresses->addr = $request->input('address', '');
        $Addresses->true_name = $request->input('linkMan');
        $Addresses->mobile = $request->input('mobile');
        $Addresses->status = 1;
        $Addresses->is_default = $request->input('isDefault') ? 1 : 0;
        $Addresses->save();
        if ($Addresses->id) {
            DB::table('addresses')
                ->where([
                    ['uid' , \Auth::user()->id],
                    ['id', '!=', $Addresses->id],
                ])
                ->update(['is_default' => 0]);
            return $this->message('操作成功');
        };
        return $this->failed('操作失败', 401);
    }

    //校内地址添加和修改
    public function insideCreate(Request $request)
    {
        // 表单验证
        $validator = Validator::make($request->all(),
            [
                'linkMan' => 'required|max:10',
                'mobile' => 'required',
                'schoolID' => 'required|max:10',
                'schoolName' => 'required|max:30',
                'dormName' => 'required|max:30',
                'dormID' => 'required|max:10',
                'address' => 'required',
            ],
            [
                'linkMan.required' => '收件人参数缺失',
                'mobile.required' => '收货人手机号参数缺失',
                'schoolID.required' => '学校ID参数缺失',
                'schoolName.required' => '学校名参数缺失',
                'dormName.required' => '宿舍楼参数缺失',
                'dormID.required' => '宿舍楼ID参数缺失',
                'address.required' => '宿舍号参数缺失',

            ]
        );
        if ($validator->fails()) {
            return $this->failed($validator->errors(), 401);
        }
        // 这里后面加上valicate验证
        if ($request->input('id')) {
            // 编辑
            $addresses = Address::find($request->input('id'));
        } else {
            $addresses = new Address;
        }
        $userID = Auth::user()->id;
        $schoolName = $request->input('schoolName');
        $dormName = $request->input('dormName');
        $addr = $schoolName.'  '.$dormName.'  '.$request->input('address', '');

        $addresses->uid = $userID;
        $addresses->sid = $request->input('schoolID');
        $addresses->did = $request->input('dormID');
        $addresses->school_name = $schoolName;
        $addresses->dorm_name = $dormName;
        $addresses->addr = $addr;
        $addresses->true_name = $request->input('linkMan');
        $addresses->mobile = $request->input('mobile');
        $addresses->is_default = $request->input('isDefault') ? Address::ISDEFAULT_YES : Address::ISDEFAULT_NO;
        $addresses->status = Address::STATE_VALID;

        if ($addresses->save()) {
            DB::table('addresses')
                ->where([
                    ['uid' , $userID],
                    ['id', '!=', $addresses->id],
                ])
                ->update(['is_default' => Address::ISDEFAULT_NO]);
            return $this->message('操作成功');
        }

        return $this->failed('操作失败', 402);

    }

    /**
     * 更新默认地址
     * @param Request $request
     */
    public function updateDefault(Request $request){
        if($request->input('id')){
            $Addresses = Address::find($request->input('id'));
            $Addresses->is_default = $request->input('isDefault') ? 1 : 0;
            if($Addresses->save()){
                DB::table('addresses')
                    ->where([
                        ['uid' , \Auth::user()->id],
                        ['id', '!=', $Addresses->id],
                    ])
                    ->update(['is_default' => 0]);
                return $this->message('操作成功');
            }
        }
        return $this->failed('操作失败', 401);
    }

    /**
     * 删除
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request){

        if($request->input('id') && Address::destroy($request->input('id'))){
            return $this->message('操作成功');
        }
        return $this->failed('操作失败', 401);
    }
    /**
     * 获取收货地址列表
     * @return mixed
     */
    public function getList(Request $request)
    {
        $where = [
            'uid' => \Auth::user()->id,
        ];
        $AddressList = Address::getAddrList($where);
        if ($AddressList) {
            return $this->success($AddressList);
        }
        return $this->failed('没有数据', 401);
    }

    public function getDetail(Request $request){
        $address = Address::getAddr([
            ['uid', '=', \Auth::user()->id],
            ['id',$request->id]
        ]);
        if ($address) {
            return $this->success($address);
        }
        return $this->failed('没有数据', 200);
    }

    /**
     * 获取默认地址
     * @return mixed
     */
    public function defaultAddr()
    {
        $defaultAddress = Address::getAddr([
            ['uid', '=', \Auth::user()->id],
            ['is_default',1]
        ]);
        if ($defaultAddress) {
            return $this->success($defaultAddress);
        }
        return $this->failed('没有数据', 200);
    }
}