<?php
namespace App\Http\Controllers\Api;

use App\Models\Iphone;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\DB;
class IphoneController extends ApiController
{
    use AuthenticatesUsers;
    public function getUserIphone(Request $request)
    {
        //$ll=DB::select('select * from users where openid = ?', [1231464465545885426545]);
        //$ll = DB::table('users')->where('openid', '1231464465545885426545')->first();

        $ll = new \App\Models\Iphone;
        $info = $ll->userInfo();
        if (!is_null($info->mobile))
        {
            return $this->message("你的手机已绑定成功");
        }else {
            return $this->failed('未绑定号码', 44);
        }

    }

    public function saveUserIphone(Request $request)
    {
        //dd(1);
//        $iphone = new Iphone();
//        $this->validate(request(),[
//            'mobile'=>'required|string|11'
//        ]);
//        $iphone->mobile = request('mobile');
        //$ll = DB::table('users')->where('openid', '1231464465545885426545')->get();
        $num =DB::table('users')->where('openid', '1231464465545885426545')->update(['mobile' => 13923456889]);

        //$num =DB::table('users')->insert(['mobile' => 13923456789]);
        //$num=1345678123;
        if ($num)
        {
            return $this->message("你的手机已绑定成功");
        }else {
            return $this->failed('未绑定号码', 44);
        }

    }



}