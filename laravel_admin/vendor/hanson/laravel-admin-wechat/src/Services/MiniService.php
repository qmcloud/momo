<?php


namespace Hanson\LaravelAdminWechat\Services;


use Illuminate\Support\Facades\Cache;

class MiniService
{
    /**
     * 通过 code 获取 session 信息
     *
     * @param string $appId
     * @param string $code
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function session(string $appId, string $code)
    {
        $app = \Hanson\LaravelAdminWechat\Facades\ConfigService::getInstanceByAppId($appId);

        $result = $app->auth->session($code);

        Cache::forever($this->getSessionKey($result['openid']), $result['session_key']);

        return $result;
    }

    /**
     * 获取 session 的缓存 key
     *
     * @param string $openId
     * @return string
     */
    protected function getSessionKey(string $openId)
    {
        return config('admin.extensions.wechat.session_key', 'mini.session.').$openId;
    }

    /**
     * 解密消息
     *
     * @param string $appId
     * @param string $openId
     * @param string $iv
     * @param string $encryptedData
     * @return mixed
     * @throws \EasyWeChat\Kernel\Exceptions\DecryptException
     */
    public function decrypt(string $appId, string $openId, string $iv, string $encryptedData)
    {
        $app = \Hanson\LaravelAdminWechat\Facades\ConfigService::getInstanceByAppId($appId);

        $sessionKey = Cache::get($this->getSessionKey($openId));

        return $app->encryptor->decryptData($sessionKey, $iv, $encryptedData);
    }
}
