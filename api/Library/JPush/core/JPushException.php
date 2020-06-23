<?php

/**
 * JPush Exception
 * User: xiezefan
 * Date: 15/12/18
 * Time: 下午3:14
 */
class APIConnectionException extends Exception {
    public $isResponseTimeout;
    function __construct($message, $isResponseTimeout = false) {
        parent::__construct($message);
        $this->isResponseTimeout = $isResponseTimeout;
    }
}

class APIRequestException extends Exception {
    public $httpCode;
    public $code;
    public $message;
    public $response;

    public $rateLimitLimit;
    public $rateLimitRemaining;
    public $rateLimitReset;

    private static $expected_keys = array('code', 'message');

    function __construct($response){
        $this->response = $response['body'];
        $this->httpCode = $response['http_code'];
        $payload = json_decode($response['body'], true);
        if ($payload != null) {
            $error = $payload['error'];
            if (!is_null($error)) {
                foreach (self::$expected_keys as $key) {
                    if (array_key_exists($key, $error)) {
                        $this->$key = $error[$key];
                    }
                }
            } else {
                foreach (self::$expected_keys as $key) {
                    if (array_key_exists($key, $payload)) {
                        $this->$key = $payload[$key];
                    }
                }
            }
        }
        $headers = $response['headers'];
        if (is_array($headers)) {
            $this->rateLimitLimit = $headers['X-Rate-Limit-Limit'];
            $this->rateLimitRemaining = $headers['X-Rate-Limit-Remaining'];
            $this->rateLimitReset = $headers['X-Rate-Limit-Reset'];
        }
    }



}