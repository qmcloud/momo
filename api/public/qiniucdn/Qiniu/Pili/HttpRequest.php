<?php
namespace Qiniu\Pili;

use \Qiniu\Pili\HttpResponse;

class HttpRequest
{
    const DELETE = "DELETE";
    const GET = "GET";
    const POST = "POST";

    private static $verifyPeer = false;
    private static $socketTimeout = null;
    private static $defaultHeaders = array();

    /**
     * Verify SSL peer
     * @param bool $enabled enable SSL verification, by default is false
     */
    public static function verifyPeer($enabled)
    {
        self::$verifyPeer = $enabled;
    }

    /**
     * Set a timeout
     * @param integer $seconds timeout value in seconds
     */
    public static function timeout($seconds)
    {
        self::$socketTimeout = $seconds;
    }

    /**
     * Set a new default header to send on every request
     * @param string $name header name
     * @param string $value header value
     */
    public static function defaultHeader($name, $value)
    {
        self::$defaultHeaders[$name] = $value;
    }

    /**
     * Clear all the default headers
     */
    public static function clearDefaultHeaders()
    {
        self::$defaultHeaders = array();
    }

    /**
     * This function is useful for serializing multidimensional arrays, and avoid getting
     * the "Array to string conversion" notice
     */
    public static function http_build_query_for_curl($arrays, &$new = array(), $prefix = null)
    {
        if (is_object($arrays)) {
            $arrays = get_object_vars($arrays);
        }
        foreach ($arrays AS $key => $value) {
            $k = isset($prefix) ? $prefix . '[' . $key . ']' : $key;
            if (!$value instanceof \CURLFile AND (is_array($value) OR is_object($value))) {
                self::http_build_query_for_curl($value, $new, $k);
            } else {
                $new[$k] = $value;
            }
        }
    }

    private static function getArrayFromQuerystring($querystring)
    {
        $pairs = explode("&", $querystring);
        $vars = array();
        foreach ($pairs as $pair) {
            $nv = explode("=", $pair, 2);
            $name = $nv[0];
            $value = $nv[1];
            $vars[$name] = $value;
        }
        return $vars;
    }

    /**
     * Ensure that a URL is encoded and safe to use with cURL
     * @param  string $url URL to encode
     * @return string
     */
    private static function encodeUrl($url)
    {
        $url_parsed = parse_url($url);
        $scheme = $url_parsed['scheme'] . '://';
        $host = $url_parsed['host'];
        $port = (isset($url_parsed['port']) ? $url_parsed['port'] : null);
        $path = (isset($url_parsed['path']) ? $url_parsed['path'] : null);
        $query = (isset($url_parsed['query']) ? $url_parsed['query'] : null);
        if ($query != null) {
            $query = '?' . http_build_query(self::getArrayFromQuerystring($url_parsed['query']));
        }
        if ($port && $port[0] != ":")
            $port = ":" . $port;
        $result = $scheme . $host . $port . $path . $query;
        return $result;
    }

    private static function getHeader($key, $val)
    {
        $key = trim($key);
        return $key . ": " . $val;
    }

    /**
     * Send a cURL request
     * @param string $httpMethod HTTP method to use
     * @param string $url URL to send the request to
     * @param mixed $body request body
     * @param array $headers additional headers to send
     * @throws Exception if a cURL error occurs
     * @return HttpResponse
     */
    public static function send($httpMethod, $url, $body = NULL, $headers = array())
    {
        if ($headers == NULL)
            $headers = array();
        $annexHeaders = array();
        $finalHeaders = array_merge($headers, self::$defaultHeaders);
        foreach ($finalHeaders as $key => $val) {
            $annexHeaders[] = self::getHeader($key, $val);
        }
        $lowerCaseFinalHeaders = array_change_key_case($finalHeaders);
        $ch = curl_init();
        if ($httpMethod != self::GET) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $httpMethod);
            if (is_array($body) || $body instanceof Traversable) {
                self::http_build_query_for_curl($body, $postBody);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postBody);
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            }
        } else if (is_array($body)) {
            if (strpos($url, '?') !== false) {
                $url .= "&";
            } else {
                $url .= "?";
            }
            self::http_build_query_for_curl($body, $postBody);
            $url .= urldecode(http_build_query($postBody));
        }
        curl_setopt($ch, CURLOPT_URL, self::encodeUrl($url));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $annexHeaders);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, self::$verifyPeer);
        curl_setopt($ch, CURLOPT_ENCODING, ""); // If an empty string, "", is set, a header containing all supported encoding types is sent.
        if (self::$socketTimeout != null) {
            curl_setopt($ch, CURLOPT_TIMEOUT, self::$socketTimeout);
        }
        $response = curl_exec($ch);
        $error = curl_error($ch);
        if ($error) {
            throw new \Exception($error);
        }
        // Split the full response in its headers and body
        $curl_info = curl_getinfo($ch);
        $header_size = $curl_info["header_size"];
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        $httpCode = $curl_info["http_code"];
        if ($httpCode >= 400) {
            throw new \Exception($body);
        }
        return new HttpResponse($httpCode, $body, $header);
    }
}

?>
