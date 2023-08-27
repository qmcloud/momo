<?php

namespace addons\epay\library;

use think\Exception;

class OrderException extends Exception
{
    public function __construct($message = "", $code = 0, $data = [])
    {
        $this->message = $message;
        $this->code = $code;
        $this->data = $data;
    }

}
