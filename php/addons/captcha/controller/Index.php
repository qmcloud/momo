<?php

namespace addons\captcha\controller;

use addons\captcha\library\Captcha;
use think\addons\Controller;
use think\Session;

class Index extends Controller
{

    public function index()
    {
        $this->error("当前插件暂无前台页面");
    }

    public function build()
    {
        $id = $this->request->param('id', '');
        $config = get_addon_config('captcha');
        
        /* 实例化 */
        $captcha = new Captcha();

        // 验证码宽度
        $captcha->width = config('captcha.imageW');
        // 验证码高度
        $captcha->height = config('captcha.imageH');
        // 验证码个数
        $captcha->nums = config('captcha.length');
        // 随机字符串
        if (isset($config['random']) && $config['random']) {
            $captcha->random = $config['random'];
        }
        // 随机数大小
        $captcha->font_size = config('captcha.fontSize');
        // 字体路径
        //$ver->font_path = __DIR__.'/Font/zhankukuhei.ttf';
        // 是否为动态验证码
        $captcha->is_gif = isset($config['is_gif']) ? $config['is_gif'] : true;
        // 动图帧数
        $captcha->gif_fps = $config['gif_fps'] ? $config['gif_fps'] : 10;

        /* 生成验证码 */
        $code = $captcha->getCode();

        $originCaptcha = new \think\captcha\Captcha();

        // 保存验证码
        $key = $this->authcode($originCaptcha->seKey, $originCaptcha->seKey);

        $secode = [];
        $secode['verify_code'] = $this->authcode(mb_strtoupper($code), $originCaptcha->seKey); // 把校验码保存到session
        $secode['verify_time'] = time(); // 验证码创建时间

        Session::set($key . $id, $secode, '');

        /* 生成验证码图片 */
        $captcha->doImg($code);
    }

    /* 加密验证码 */
    private function authcode($str, $seKey)
    {
        $key = substr(md5($seKey), 5, 8);
        $str = substr(md5($str), 8, 10);
        return md5($key . $str);
    }

}
