<?php

namespace addons\third\library;

use addons\third\model\Third;
use app\common\model\User;
use fast\Random;
use think\Db;
use think\Exception;

/**
 * 第三方登录服务类
 *
 */
class Service
{

    /**
     * 第三方登录
     * @param string $platform 平台
     * @param array  $params   参数
     * @param array  $extend   会员扩展信息
     * @param int    $keeptime 有效时长
     * @return boolean
     */
    public static function connect($platform, $params = [], $extend = [], $keeptime = 0)
    {

        $time = time();
        $nickname = $params['nickname'] ?? ($params['userinfo']['nickname'] ?? '');
        $avatar = $params['avatar'] ?? ($params['userinfo']['avatar'] ?? '');
        $values = [
            'platform'      => $platform,
            'openid'        => $params['openid'],
            'openname'      => $nickname,
            'access_token'  => $params['access_token'],
            'refresh_token' => $params['refresh_token'],
            'expires_in'    => $params['expires_in'],
            'logintime'     => $time,
            'expiretime'    => $time + $params['expires_in'],
        ];
        $values = array_merge($values, $params);

        $auth = \app\common\library\Auth::instance();

        $auth->keeptime($keeptime);
        //是否有自己的
        $third = Third::get(['platform' => $platform, 'openid' => $params['openid']], 'user');
        if ($third) {
            if (!$third->user) {
                $third->delete();
            } else {
                $third->allowField(true)->save($values);
                // 写入登录Cookies和Token
                return $auth->direct($third->user_id);
            }
        }

        //存在unionid就需要判断是否需要生成新记录
        if (isset($params['unionid']) && !empty($params['unionid'])) {
            $third = Third::get(['platform' => $platform, 'unionid' => $params['unionid']], 'user');
            if ($third) {
                if (!$third->user) {
                    $third->delete();
                } else {
                    // 保存第三方信息
                    $values['user_id'] = $third->user_id;
                    $third = Third::create($values, true);
                    // 写入登录Cookies和Token
                    return $auth->direct($third->user_id);
                }
            }
        }

        if ($auth->id) {
            if (!$third) {
                $values['user_id'] = $auth->id;
                Third::create($values, true);
            }
            $user = $auth->getUser();
        } else {
            // 先随机一个用户名,随后再变更为u+数字id
            $username = Random::alnum(20);
            $password = Random::alnum(6);
            $domain = request()->host();

            Db::startTrans();
            try {
                // 默认注册一个会员
                $result = $auth->register($username, $password, $username . '@' . $domain, '', $extend);
                if (!$result) {
                    throw new Exception($auth->getError());
                }
                $user = $auth->getUser();
                $fields = ['username' => 'u' . $user->id, 'email' => 'u' . $user->id . '@' . $domain];
                if ($nickname) {
                    $fields['nickname'] = $nickname;
                }
                if ($avatar) {
                    $fields['avatar'] = function_exists("xss_clean") ? xss_clean(strip_tags($avatar)) : strip_tags($avatar);
                }

                // 更新会员资料
                $user = User::get($user->id);
                $user->save($fields);

                // 保存第三方信息
                $values['user_id'] = $user->id;
                Third::create($values, true);
                Db::commit();
            } catch (\Exception $e) {
                Db::rollback();
                $auth->logout();
                return false;
            }
        }
        // 写入登录Cookies和Token
        return $auth->direct($user->id);
    }


    public static function isBindThird($platform, $openid, $apptype = '', $unionid = '')
    {
        $conddtions = [
            'platform' => $platform,
            'openid'   => $openid
        ];
        if ($apptype) {
            $conddtions['apptype'] = $apptype;
        }
        $third = Third::get($conddtions, 'user');
        //第三方存在
        if ($third) {
            //用户失效
            if (!$third->user) {
                $third->delete();
                return false;
            }
            return true;
        }
        if ($unionid) {
            $third = Third::get(['platform' => $platform, 'unionid' => $unionid], 'user');
            if ($third) {
                //
                if (!$third->user) {
                    $third->delete();
                    return false;
                }
                return true;
            }
        }

        return false;
    }
}
