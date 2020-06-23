<?php

/**
 * 验证码处理
 */
namespace Api\Controller;
use Think\Controller;
class MobileverifyController extends Controller {

    public function index() {
       echo hook_one("send_mobile_verify_code",array());
    }
    

}

