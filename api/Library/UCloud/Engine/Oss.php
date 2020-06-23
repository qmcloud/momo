<?php
/**
 * 阿里云oss上传驱动
 *
 *
 *
 *
 */
class UCloud_Engine_Oss {
    /**
     * 上传文件根目录
     * @var string
     */
    private $rootPath;

    private $fileType;
    /**
     * 上传错误信息
     * @var string
     */
    private $error  = '';
    private $config = array(
        'access_id'  => '', //阿里云Access Key ID
        'access_key' => '', //阿里云Access Key Secret
        'bucket'     => '', //空间名称
        'host'       => '', //服务器地址
        'timeout'    => 90, //超时时间
    );
    /**
     * 构造函数，用于设置上传根路径
     * @param array  $config FTP配置
     */
    public function __construct($root, $config) {

        $new_config = array();
        $new_config['access_id'] = $config['accessKey'];
        $new_config['access_key'] = $config['secretKey'];
        $new_config['bucket'] = $config['bucket'];
        $new_config['host'] = $config['api'];
        $new_config['timeout'] = $config['timeout'];

        /* 默认FTP配置 */
        $this->config = array_merge($this->config, $new_config);

        /* 设置根目录 */
        $this->rootPath = trim($root, './') . '/';
    }
    /**
     * 检测上传根目录(阿里云上传时支持自动创建目录，直接返回)
     * @param string $rootpath   根目录
     * @return boolean true-检测通过，false-检测失败
     */
    public function checkRootPath($rootpath) {
        return true;
    }
    /**
     * 检测上传目录(阿里云上传时支持自动创建目录，直接返回)
     * @param  string $savepath 上传目录
     * @return boolean          检测结果，true-通过，false-失败
     */
    public function checkSavePath($savepath) {
        return true;
    }
    /**
     * 创建文件夹 (阿里云上传时支持自动创建目录，直接返回)
     * @param  string $savepath 目录名称
     * @return boolean          true-创建成功，false-创建失败
     */
    public function mkdir($savepath) {
        return true;
    }
    /**
     * 保存指定文件
     * @param  array   $file    保存的文件信息
     * @param  boolean $replace 同名文件是否覆盖
     * @return boolean          保存状态，true-成功，false-失败
     */
    public function save($file, $replace = true) {
        $this->fileType = $file['type'];

        $resource = fopen($file['tmp_name'], 'r');

        //正则去除最后一个斜杠
        $file['savepath'] = preg_replace('/(.*?)(?:\/(?!\w)|$)/', '$1', $file['savepath']);     
        
        $path = $this->rootPath . $file['savepath'] .'/'. $file['savename'];
        return $this->request($path, $resource);
    }

    /**
     * 请求阿里云oss
     * @param  [type] $path     [description]
     * @param  [type] $resource [description]
     * @return [type]           [description]
     */
    private function request($path, $resource = null){
        $_headers = array('Content-Type: ' . $this->fileType);

        $uri = "/{$this->config['bucket']}{$path}";

        $date = gmdate('D, d M Y H:i:s \G\M\T');

        array_push($_headers, "Authorization: {$this->sign($uri, $date)}");
        array_push($_headers, "Date: {$date}");

        fseek($resource, 0, SEEK_END);
        $length = ftell($resource);
        fseek($resource, 0);
        array_push($_headers, "Content-Length: {$length}");
        $ch  = curl_init('http://' . $this->config['host'] . $uri);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_INFILE, $resource);
        curl_setopt($ch, CURLOPT_INFILESIZE, $length);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $_headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->config['timeout']);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        $response = curl_exec($ch);
        $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        fclose($resource);
        if ($status == 200) {
            return true;
        } else {
            $this->error = $response;
            return false;
        }
    }


    /**
     * 获取最后一次上传错误信息
     * @return string 错误信息
     */
    public function getError() {
        return $this->error;
    }


    /**
     * 生成请求签名
     * @param  string  $uri    请求URI
     * @param  string  $date   请求时间
     * @return string          请求签名
     */
    private function sign( $uri, $date){
        $sign_string = "PUT\n\n" . $this->fileType . "\n" . $date . "\n" . $uri;
        $sign        = $this->hex_to_base64(hash_hmac('sha1', $sign_string, $this->config['access_key']));
        return 'OSS ' . $this->config['access_id'] . ':' . $sign;
    }

    private function hex_to_base64($str) {
        $result = '';
        for ($i = 0; $i < strlen($str); $i += 2) {
            $result .= chr(hexdec(substr($str, $i, 2)));
        }
        return base64_encode($result);
    }
}