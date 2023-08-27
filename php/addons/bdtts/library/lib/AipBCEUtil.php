<?php
/*
* Copyright (c) 2017 Baidu.com, Inc. All Rights Reserved
*
* Licensed under the Apache License, Version 2.0 (the "License"); you may not
* use this file except in compliance with the License. You may obtain a copy of
* the License at
*
* Http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
* WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
* License for the specific language governing permissions and limitations under
* the License.
*/
namespace addons\bdtts\library\lib;
/**
 * BCE Util
 */
class AipHttpUtil
{
    // 根据RFC 3986，除了：
    //   1.大小写英文字符
    //   2.阿拉伯数字
    //   3.点'.'、波浪线'~'、减号'-'以及下划线'_'
    // 以外都要编码
    public static $PERCENT_ENCODED_STRINGS;

    //填充编码数组
    public static function __init()
    {
        AipHttpUtil::$PERCENT_ENCODED_STRINGS = array();
        for ($i = 0; $i < 256; ++$i) {
            AipHttpUtil::$PERCENT_ENCODED_STRINGS[$i] = sprintf("%%%02X", $i);
        }

        //a-z不编码
        foreach (range('a', 'z') as $ch) {
            AipHttpUtil::$PERCENT_ENCODED_STRINGS[ord($ch)] = $ch;
        }

        //A-Z不编码
        foreach (range('A', 'Z') as $ch) {
            AipHttpUtil::$PERCENT_ENCODED_STRINGS[ord($ch)] = $ch;
        }

        //0-9不编码
        foreach (range('0', '9') as $ch) {
            AipHttpUtil::$PERCENT_ENCODED_STRINGS[ord($ch)] = $ch;
        }

        //以下4个字符不编码
        AipHttpUtil::$PERCENT_ENCODED_STRINGS[ord('-')] = '-';
        AipHttpUtil::$PERCENT_ENCODED_STRINGS[ord('.')] = '.';
        AipHttpUtil::$PERCENT_ENCODED_STRINGS[ord('_')] = '_';
        AipHttpUtil::$PERCENT_ENCODED_STRINGS[ord('~')] = '~';
    }

    /**
     * 在uri编码中不能对'/'编码
     * @param  string $path
     * @return string
     */
    public static function urlEncodeExceptSlash($path)
    {
        return str_replace("%2F", "/", AipHttpUtil::urlEncode($path));
    }

    /**
     * 使用编码数组编码
     * @param  string $path
     * @return string
     */
    public static function urlEncode($value)
    {
        $result = '';
        for ($i = 0; $i < strlen($value); ++$i) {
            $result .= AipHttpUtil::$PERCENT_ENCODED_STRINGS[ord($value[$i])];
        }
        return $result;
    }

    /**
     * 生成标准化QueryString
     * @param  array $parameters
     * @return array
     */
    public static function getCanonicalQueryString(array $parameters)
    {
        //没有参数，直接返回空串
        if (count($parameters) == 0) {
            return '';
        }

        $parameterStrings = array();
        foreach ($parameters as $k => $v) {
            //跳过Authorization字段
            if (strcasecmp('Authorization', $k) == 0) {
                continue;
            }
            if (!isset($k)) {
                throw new \InvalidArgumentException(
                    "parameter key should not be null"
                );
            }
            if (isset($v)) {
                //对于有值的，编码后放在=号两边
                $parameterStrings[] = AipHttpUtil::urlEncode($k)
                    . '=' . AipHttpUtil::urlEncode((string) $v);
            } else {
                //对于没有值的，只将key编码后放在=号的左边，右边留空
                $parameterStrings[] = AipHttpUtil::urlEncode($k) . '=';
            }
        }
        //按照字典序排序
        sort($parameterStrings);

        //使用'&'符号连接它们
        return implode('&', $parameterStrings);
    }

    /**
     * 生成标准化uri
     * @param  string $path
     * @return string
     */
    public static function getCanonicalURIPath($path)
    {
        //空路径设置为'/'
        if (empty($path)) {
            return '/';
        } else {
            //所有的uri必须以'/'开头
            if ($path[0] == '/') {
                return AipHttpUtil::urlEncodeExceptSlash($path);
            } else {
                return '/' . AipHttpUtil::urlEncodeExceptSlash($path);
            }
        }
    }

    /**
     * 生成标准化http请求头串
     * @param  array $headers
     * @return array
     */
    public static function getCanonicalHeaders($headers)
    {
        //如果没有headers，则返回空串
        if (count($headers) == 0) {
            return '';
        }

        $headerStrings = array();
        foreach ($headers as $k => $v) {
            //跳过key为null的
            if ($k === null) {
                continue;
            }
            //如果value为null，则赋值为空串
            if ($v === null) {
                $v = '';
            }
            //trim后再encode，之后使用':'号连接起来
            $headerStrings[] = AipHttpUtil::urlEncode(strtolower(trim($k))) . ':' . AipHttpUtil::urlEncode(trim($v));
        }
        //字典序排序
        sort($headerStrings);

        //用'\n'把它们连接起来
        return implode("\n", $headerStrings);
    }
}
AipHttpUtil::__init();


class AipSignOption
{
    const EXPIRATION_IN_SECONDS = 'expirationInSeconds';

    const HEADERS_TO_SIGN = 'headersToSign';

    const TIMESTAMP = 'timestamp';

    const DEFAULT_EXPIRATION_IN_SECONDS = 1800;

    const MIN_EXPIRATION_IN_SECONDS = 300;

    const MAX_EXPIRATION_IN_SECONDS = 129600;
}


class AipSampleSigner
{

    const BCE_AUTH_VERSION = "bce-auth-v1";
    const BCE_PREFIX = 'x-bce-';

    //不指定headersToSign情况下，默认签名http头，包括：
    //    1.host
    //    2.content-length
    //    3.content-type
    //    4.content-md5
    public static $defaultHeadersToSign;

    public static function  __init()
    {
        AipSampleSigner::$defaultHeadersToSign = array(
            "host",
            "content-length",
            "content-type",
            "content-md5",
        );
    }

    /**
     * 签名
     * @param  array $credentials
     * @param  string $httpMethod
     * @param  string $path
     * @param  array  $headers
     * @param  string $params
     * @param  array  $options
     * @return string
     */
    public static function sign(
        array $credentials,
        $httpMethod,
        $path,
        $headers,
        $params,
        $options = array()
    ) {
        //设定签名有效时间
        if (!isset($options[AipSignOption::EXPIRATION_IN_SECONDS])) {
            //默认值1800秒
            $expirationInSeconds = AipSignOption::DEFAULT_EXPIRATION_IN_SECONDS;
        } else {
            $expirationInSeconds = $options[AipSignOption::EXPIRATION_IN_SECONDS];
        }

        //解析ak sk
        $accessKeyId = $credentials['ak'];
        $secretAccessKey = $credentials['sk'];

        //设定时间戳，注意：如果自行指定时间戳需要为UTC时间
        if (!isset($options[AipSignOption::TIMESTAMP])) {
            //默认值当前时间
            $timestamp = gmdate('Y-m-d\TH:i:s\Z');
        } else {
            $timestamp = $options[AipSignOption::TIMESTAMP];
        }

        //生成authString
        $authString = AipSampleSigner::BCE_AUTH_VERSION . '/' . $accessKeyId . '/'
            . $timestamp . '/' . $expirationInSeconds;

        //使用sk和authString生成signKey
        $signingKey = hash_hmac('sha256', $authString, $secretAccessKey);

        //生成标准化URI
        $canonicalURI = AipHttpUtil::getCanonicalURIPath($path);

        //生成标准化QueryString
        $canonicalQueryString = AipHttpUtil::getCanonicalQueryString($params);

        //填充headersToSign，也就是指明哪些header参与签名
        $headersToSign = null;
        if (isset($options[AipSignOption::HEADERS_TO_SIGN])) {
            $headersToSign = $options[AipSignOption::HEADERS_TO_SIGN];
        }

        //生成标准化header
        $canonicalHeader = AipHttpUtil::getCanonicalHeaders(
            AipSampleSigner::getHeadersToSign($headers, $headersToSign)
        );

        //整理headersToSign，以';'号连接
        $signedHeaders = '';
        if ($headersToSign !== null) {
            $signedHeaders = strtolower(
                trim(implode(";", $headersToSign))
            );
        }

        //组成标准请求串
        $canonicalRequest = "$httpMethod\n$canonicalURI\n"
            . "$canonicalQueryString\n$canonicalHeader";

        //使用signKey和标准请求串完成签名
        $signature = hash_hmac('sha256', $canonicalRequest, $signingKey);

        //组成最终签名串
        $authorizationHeader = "$authString/$signedHeaders/$signature";

        return $authorizationHeader;
    }

    /**
     * 根据headsToSign过滤应该参与签名的header
     * @param  array $headers
     * @param  array $headersToSign
     * @return array
     */
    public static function getHeadersToSign($headers, $headersToSign)
    {

        $arr = array();
        foreach ($headersToSign as $value) {
            $arr[] = strtolower(trim($value));
        }

        //value被trim后为空串的header不参与签名
        $result = array();
        foreach ($headers as $key => $value) {
            if (trim($value) !== '') {
                $key = strtolower(trim($key));
                if (in_array($key, $arr)) {
                    $result[$key] = $value;
                } 
            }
        }

        //返回需要参与签名的header
        return $result;
    }

    /**
     * 检查header是不是默认参加签名的：
     * 1.是host、content-type、content-md5、content-length之一
     * 2.以x-bce开头
     * @param  array $header
     * @return boolean
     */
    public static function isDefaultHeaderToSign($header)
    {
        $header = strtolower(trim($header));
        if (in_array($header, AipSampleSigner::$defaultHeadersToSign)) {
            return true;
        }
        return substr_compare($header, AipSampleSigner::BCE_PREFIX, 0, strlen(AipSampleSigner::BCE_PREFIX)) == 0;
    }
}
AipSampleSigner::__init();
