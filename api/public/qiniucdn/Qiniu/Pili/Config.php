<?php
namespace Qiniu\Pili;

final class Config
{
    const SDK_VERSION = '2.1.0';
    const SDK_USER_AGENT = 'pili-sdk-php';

    public $USE_HTTPS = false;
    public $API_HOST = 'pili.qiniuapi.com';
    public $API_VERSION = 'v2';

    protected static $_instance = NULL;

    protected function __construct()
    {
    }

    protected function __clone()
    {
    }

    public static function getInstance()
    {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __get($property)
    {
        if (property_exists(self::getInstance(), $property)) {
            return self::getInstance()->$property;
        } else {
            return NULL;
        }
    }

    public function __set($property, $value)
    {
        if (property_exists(self::getInstance(), $property)) {
            self::getInstance()->$property = $value;
        }
        return self::getInstance();
    }
}

?>
