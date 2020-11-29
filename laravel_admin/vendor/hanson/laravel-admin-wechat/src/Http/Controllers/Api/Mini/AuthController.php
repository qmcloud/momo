<?php

namespace Hanson\LaravelAdminWechat\Http\Controllers\Api\Mini;

use Hanson\LaravelAdminWechat\Events\DecryptMobile;
use Hanson\LaravelAdminWechat\Events\DecryptUserInfo;
use Hanson\LaravelAdminWechat\Models\WechatUser;
use Hanson\LaravelAdminWechat\Services\MiniService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function checkToken()
    {
        $wechatUser = Auth::guard('mini')->user();

        return ok($wechatUser);
    }

    public function login(Request $request, MiniService $service)
    {
        $request->validate([
            'code' => 'required',
            'app_id' => 'required',
        ]);

        $session = $service->session($appId = $request->get('app_id'), $request->get('code'));

        $wechatUser = config('admin.extensions.wechat.wechat_user', WechatUser::class)::query()->firstOrCreate([
            'openid' => $session['openid'],
            'app_id' => $appId,
        ]);

        $token = auth('mini')->login($wechatUser);

        return ok([
            'access_token' => 'bearer '.$token,
            'expires_in' => auth('mini')->factory()->getTTL() * 60,
            'wechat_user' => $wechatUser,
        ]);
    }

    protected function decryptMobile(Request $request, MiniService $service)
    {
        $request->validate([
            'iv' => 'required',
            'encrypted_data' => 'required',
            'app_id' => 'required',
        ]);

        $wechatUser = Auth::guard('mini')->user();

        $decryptedData = $service->decrypt($request->get('app_id'), $wechatUser->openid, $request->get('iv'), $request->get('encrypted_data'));

        event(new DecryptMobile($decryptedData, $wechatUser));

        return ok($decryptedData);
    }

    protected function decryptUserInfo(Request $request, MiniService $service)
    {
        $request->validate([
            'iv' => 'required',
            'encrypted_data' => 'required',
            'app_id' => 'required',
        ]);

        $wechatUser = Auth::guard('mini')->user();

        $decryptedData = $service->decrypt($request->get('app_id'), $wechatUser->openid, $request->get('iv'), $request->get('encrypted_data'));

        $wechatUser->update([
            'nickname' => $decryptedData['nickName'],
            'country' => $decryptedData['country'],
            'province' => $decryptedData['province'],
            'city' => $decryptedData['city'],
            'gender' => $decryptedData['gender'],
            'avatar' => $decryptedData['avatarUrl'],
        ]);

        event(new DecryptUserInfo($decryptedData, $wechatUser));

        return ok($decryptedData);
    }
}
