<?php
namespace Qiniu\Pili;

final class Utils
{
    public static function base64UrlEncode($str)
    {
        $find = array('+', '/');
        $replace = array('-', '_');
        return str_replace($find, $replace, base64_encode($str));
    }

    public static function base64UrlDecode($str)
    {
        $find = array('-', '_');
        $replace = array('+', '/');
        return base64_decode(str_replace($find, $replace, $str));
    }

    public static function digest($secret, $data)
    {
        return hash_hmac('sha1', $data, $secret, true);
    }

    public static function sign($secret, $data)
    {
        return self::base64UrlEncode(self::digest($secret, $data));
    }

    public static function getUserAgent($sdkName, $sdkVersion)
    {
        $ua = sprintf("%s/%s", $sdkName, $sdkVersion);
        if (extension_loaded('curl')) {
            $curlVersion = curl_version();
            $ua .= ' curl/' . $curlVersion['version'];
        }
        $ua .= ' PHP/' . PHP_VERSION;
        return $ua;
    }

}
?>
