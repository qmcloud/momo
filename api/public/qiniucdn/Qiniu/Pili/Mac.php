<?php
namespace Qiniu\Pili;

class Mac
{
    public $_accessKey;
    public $_secretKey;

    public function __construct($accessKey, $secretKey)
    {
        $this->_accessKey = $accessKey;
        $this->_secretKey = $secretKey;
    }

    public function MACToken($method, $url, $contentType, $body)
    {
        $url = parse_url($url);
        $data = '';
        if (!empty($url['path'])) {
            $data = $method . ' ' . $url['path'];
        }
        if (!empty($url['query'])) {
            $data .= '?' . $url['query'];
        }
        if (!empty($url['host'])) {
            $data .= "\nHost: " . $url['host'];
            if (isset($url['port'])) {
                $data .= ':' . $url['port'];
            }
        }
        if (!empty($contentType)) {
            $data .= "\nContent-Type: " . $contentType;
        }
        $data .= "\n\n";
        if (!empty($body)) {
            $data .= $body;
        }
        return 'Qiniu ' . $this->_accessKey . ':' . Utils::sign($this->_secretKey, $data);
    }
}

?>
