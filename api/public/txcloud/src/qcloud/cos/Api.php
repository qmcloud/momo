<?php

namespace QCloud\Cos;

class Api {
    //版本
    const VERSION = 'v4.3.7';
    //计算sign签名的时间参数
    const EXPIRED_SECONDS = 180;
    //1M
    const SLICE_SIZE_1M = 1048576;
    //20M 大于20M的文件需要进行分片传输
    const MAX_UNSLICE_FILE_SIZE = 20971520;
	//失败尝试次数
    const MAX_RETRY_TIMES = 3;
    //错误代码
    const COSAPI_SUCCESS         = 0;
    const COSAPI_PARAMS_ERROR    = -1;
    const COSAPI_NETWORK_ERROR   = -2;
    const COSAPI_INTEGRITY_ERROR = -3;

    //HTTP请求超时时间
    private $timeout = 60;
    private $endPoint = 'http://region.file.myqcloud.com/files/v2/';
    private $region = 'gz'; // default region is guangzou
    private $auth;
    private $httpClient;
    private $config;

    public function __construct($config) {
        if (empty($config['app_id']) || empty($config['secret_id']) || empty($config['secret_key'])) {
            throw new \Exception('Config need app_id,secret_id,secret_key!');
        }
        $this->config = $config;
        $this->auth = new Auth($config['app_id'], $config['secret_id'], $config['secret_key']);
        $this->httpClient = new HttpClient();

        if (isset($config['region'])) {
            $this->setRegion($config['region']);
        }

        if (isset($config['timeout'])) {
            $this->setTimeout($config['timeout']);
        }
    }

	/**
     * 设置HTTP请求超时时间
     * @param  int  $timeout  超时时长
     */
    public function setTimeout($timeout = 60) {
        if (!is_int($timeout) || $timeout < 0) {
            return false;
        }

        $this->timeout = $timeout;
        return true;
    }

    public function setRegion($region) {
        $this->region = $region;
    }

    /**
     * 上传文件,自动判断文件大小,如果小于20M则使用普通文件上传,大于20M则使用分片上传
     * @param  string  $bucket   bucket名称
     * @param  string  $srcPath      本地文件路径
     * @param  string  $dstPath      上传的文件路径
     * @param  string  $bizAttr      文件属性
     * @param  string  $slicesize    分片大小(512k,1m,2m,3m)，默认:1m
     * @param  string  $insertOnly   同名文件是否覆盖
     * @return [type]                [description]
     */
    public function upload($bucket, $srcPath, $dstPath, $bizAttr=null, $sliceSize=null, $insertOnly=null) {


        if (!file_exists($srcPath)) {
            return array(
                        'code' => self::COSAPI_PARAMS_ERROR,
                        'message' => 'file ' . $srcPath .' not exists',
                        'data' => array()
                    );
        }


        if (!$dstPath || !is_string($dstPath)
                      || $dstPath[strlen($dstPath) - 1] == '/') {
            return array(
                        'code' => self::COSAPI_PARAMS_ERROR,
                        'message' => 'dstPath ' . $dstPath .' invalid',
                        'data' => array()
                    );
        }

        $dstPath = $this->normalizerPath($dstPath, false);

        //文件大于20M则使用分片传输
        if (filesize($srcPath) < self::MAX_UNSLICE_FILE_SIZE ) {
            return $this->uploadFile($bucket, $srcPath, $dstPath, $bizAttr, $insertOnly);
        } else {
            $sliceSize = $this->getSliceSize($sliceSize);
            return $this->uploadBySlicing($bucket, $srcPath, $dstPath, $bizAttr, $sliceSize, $insertOnly);
        }
    }

    /* *
     * 上传内存中的内容
     * @param  string  $bucket      bucket名称
     * @param  string  $content     文件内容，二进制安全
     * @param  string  $dstPath     上传的文件路径
     * @param  string  $bizAttr     文件属性
     * @param  int     $insertOnly  是否覆盖同名文件:0 覆盖,1:不覆盖
     *
     * */
    public function uploadBuffer(
        $bucket, $content, $dstPath,
        $bizAttr=null, $insertOnly=null) {

	    if (strlen($content) >= self::MAX_UNSLICE_FILE_SIZE) {
		    return array(
                'code' => self::COSAPI_PARAMS_ERROR,
                'message' => 'content larger then 20M, not supported',
                'data' => array()
            );
	    }

        if (!$dstPath || !is_string($dstPath)
                      || $dstPath[strlen($dstPath) - 1] == '/') {
            return array(
                        'code' => self::COSAPI_PARAMS_ERROR,
                        'message' => 'dstPath ' . $dstPath .' invalid',
                        'data' => array()
                    );
        }

	    $dstPath = $this->cosUrlEncode($dstPath);
        $expired = time() + self::EXPIRED_SECONDS;
        $url = $this->generateResUrl($bucket, $dstPath);
        $signature = $this->auth->createReusableSignature($expired, $bucket);
        $fileSha = sha1($content);

        $data = array(
            'op' => 'upload',
            'sha' => $fileSha,
            'biz_attr' => (isset($bizAttr) ? $bizAttr : ''),
            'filecontent' => $content,
        );

        if (isset($insertOnly) && strlen($insertOnly) > 0) {
            $data['insertOnly'] = (($insertOnly == 0 || $insertOnly == '0' ) ? 0 : 1);
        }

        $req = array(
            'url' => $url,
            'method' => 'post',
            'timeout' => $this->timeout,
            'data' => $data,
            'header' => array(
                'Authorization: ' . $signature,
            ),
        );

        return $this->sendRequest($req);
    }

    /**
     * 下载文件
     * @param  string  $bucket  bucket名称
     * @param  string  $srcPath     本地文件路径
     * @param  string  $dstPath     上传的文件路径
     * @return [type]               [description]
     */
    public function download($bucket, $srcPath, $dstPath) {
        $srcInfo = $this->stat($bucket, $srcPath);
        if ($srcInfo['code'] !== 0) {
            return array(
                'code' => self::COSAPI_PARAMS_ERROR,
                'message' => 'file '.$srcPath.' does not exists.',
                'data' => array()
            );
        }

        $url = $srcInfo['data']['source_url'];
        $sha = $srcInfo['data']['sha'];
        $expired = time() + self::EXPIRED_SECONDS;
        $signature = $this->auth->createReusableSignature($expired, $bucket);
        $req = array(
            'url' => $url,
            'method' => 'get',
            'timeout' => $this->timeout,
            'header' => array(
                'Authorization: ' . $signature,
            ),
        );

        $result = $this->httpClient->download($req, $dstPath);
        if ($result['code'] !== self::COSAPI_SUCCESS) {
            return array(
                'code' => $result['code'],
                'message' => $result['message'],
                'data' => array()
            );
        }
        return array(
            'code' => self::COSAPI_SUCCESS,
            'message' => '',
            'data' => array()
        );
    }

    /*
     * 创建目录
     * @param  string  $bucket bucket名称
     * @param  string  $folder       目录路径
	 * @param  string  $bizAttr    目录属性
     */
    public function createFolder($bucket, $folder, $bizAttr = null) {
        if (!$this->isValidPath($folder)) {
            return array(
                        'code' => self::COSAPI_PARAMS_ERROR,
                        'message' => 'folder ' . $folder . ' is not a valid folder name',
                        'data' => array()
                    );
        }

        $folder = $this->normalizerPath($folder, True);
        $folder = $this->cosUrlEncode($folder);
        $expired = time() + self::EXPIRED_SECONDS;
        $url = $this->generateResUrl($bucket, $folder);
        $signature = $this->auth->createReusableSignature($expired, $bucket);

        $data = array(
            'op' => 'create',
            'biz_attr' => (isset($bizAttr) ? $bizAttr : ''),
        );

        $data = json_encode($data);

        $req = array(
            'url' => $url,
            'method' => 'post',
            'timeout' => $this->timeout,
            'data' => $data,
            'header' => array(
                'Authorization: ' . $signature,
                'Content-Type: application/json',
            ),
        );

        return $this->sendRequest($req);
    }

    /*
     * 目录列表
     * @param  string  $bucket bucket名称
     * @param  string  $path     目录路径，sdk会补齐末尾的 '/'
     * @param  int     $num      拉取的总数
     * @param  string  $offset   透传字段,用于翻页,前端不需理解,需要往前/往后翻页则透传回来
     */
    public function listFolder(
                    $bucket, $folder, $num = 20,
                    $context = null) {
        $folder = $this->normalizerPath($folder, True);

        return $this->listBase($bucket, $folder, $num, $context);
    }

    /*
     * 目录列表(前缀搜索)
     * @param  string  $bucket bucket名称
     * @param  string  $prefix   列出含此前缀的所有文件
     * @param  int     $num      拉取的总数
     * @param  string  $offset   透传字段,用于翻页,前端不需理解,需要往前/往后翻页则透传回来
     */
    public function prefixSearch(
                    $bucket, $prefix, $num = 20,
                    $context = null) {
        $path = $this->normalizerPath($prefix);

        return $this->listBase($bucket, $prefix, $num, $context);
    }

    /*
     * 目录更新
     * @param  string  $bucket bucket名称
     * @param  string  $folder      文件夹路径,SDK会补齐末尾的 '/'
     * @param  string  $bizAttr   目录属性
     */
    public function updateFolder($bucket, $folder, $bizAttr = null) {
        $folder = $this->normalizerPath($folder, True);

        return $this->updateBase($bucket, $folder, $bizAttr);
    }

   /*
     * 查询目录信息
     * @param  string  $bucket bucket名称
     * @param  string  $folder       目录路径
     */
    public function statFolder($bucket, $folder) {
        $folder = $this->normalizerPath($folder, True);

        return $this->statBase($bucket, $folder);
    }

    /*
     * 删除目录
     * @param  string  $bucket bucket名称
     * @param  string  $folder       目录路径
	 *  注意不能删除bucket下根目录/
     */
    public function delFolder($bucket, $folder) {
        if (empty($bucket) || empty($folder)) {
            return array(
                    'code' => self::COSAPI_PARAMS_ERROR,
                    'message' => 'bucket or path is empty');
        }

        $folder = $this->normalizerPath($folder, True);

        return $this->delBase($bucket, $folder);
    }

    /*
     * 更新文件
     * @param  string  $bucket  bucket名称
     * @param  string  $path        文件路径
     * @param  string  $authority:  eInvalid(继承Bucket的读写权限)/eWRPrivate(私有读写)/eWPrivateRPublic(公有读私有写)
	 * @param  array   $customer_headers_array 携带的用户自定义头域,包括
     * 'Cache-Control' => '*'
     * 'Content-Type' => '*'
     * 'Content-Disposition' => '*'
     * 'Content-Language' => '*'
     * 'x-cos-meta-自定义内容' => '*'
     */
    public function update($bucket, $path,
                  $bizAttr = null, $authority=null,$customer_headers_array=null) {
        $path = $this->normalizerPath($path);

        return $this->updateBase($bucket, $path, $bizAttr, $authority, $customer_headers_array);
    }

    /*
     * 查询文件信息
     * @param  string  $bucket  bucket名称
     * @param  string  $path        文件路径
     */
    public function stat($bucket, $path) {
        $path = $this->normalizerPath($path);

        return $this->statBase($bucket, $path);
    }

    /*
     * 删除文件
     * @param  string  $bucket
     * @param  string  $path      文件路径
     */
    public function delFile($bucket, $path) {
        if (empty($bucket) || empty($path)) {
            return array(
                    'code' => self::COSAPI_PARAMS_ERROR,
                    'message' => 'path is empty');
        }

        $path = $this->normalizerPath($path);

        return $this->delBase($bucket, $path);
    }

    /**
     * 内部方法, 上传文件
     * @param  string  $bucket  bucket名称
     * @param  string  $srcPath     本地文件路径
     * @param  string  $dstPath     上传的文件路径
     * @param  string  $bizAttr     文件属性
     * @param  int     $insertOnly  是否覆盖同名文件:0 覆盖,1:不覆盖
     * @return [type]               [description]
     */
    private function uploadFile($bucket, $srcPath, $dstPath, $bizAttr = null, $insertOnly = null) {
        $srcPath = realpath($srcPath);
	    $dstPath = $this->cosUrlEncode($dstPath);

	    if (filesize($srcPath) >= self::MAX_UNSLICE_FILE_SIZE ) {
		    return array(
                'code' => self::COSAPI_PARAMS_ERROR,
                'message' => 'file '.$srcPath.' larger then 20M, please use uploadBySlicing interface',
                'data' => array()
            );
	    }

        $expired = time() + self::EXPIRED_SECONDS;
        $url = $this->generateResUrl($bucket, $dstPath);
        $signature = $this->auth->createReusableSignature($expired, $bucket);
        $fileSha = hash_file('sha1', $srcPath);

        $data = array(
            'op' => 'upload',
            'sha' => $fileSha,
            'biz_attr' => (isset($bizAttr) ? $bizAttr : ''),
        );

        $data['filecontent'] = file_get_contents($srcPath);

        if (isset($insertOnly) && strlen($insertOnly) > 0) {
            $data['insertOnly'] = (($insertOnly == 0 || $insertOnly == '0' ) ? 0 : 1);
        }

        $req = array(
            'url' => $url,
            'method' => 'post',
            'timeout' => $this->timeout,
            'data' => $data,
            'header' => array(
                'Authorization: ' . $signature,
            ),
        );

        return $this->sendRequest($req);
    }

    /**
     * 内部方法,上传文件
     * @param  string  $bucket  bucket名称
     * @param  string  $srcPath     本地文件路径
     * @param  string  $dstPath     上传的文件路径
     * @param  string  $bizAttr     文件属性
     * @param  string  $sliceSize   分片大小
     * @param  int     $insertOnly  是否覆盖同名文件:0 覆盖,1:不覆盖
     * @return [type]                [description]
     */
    private function uploadBySlicing(
            $bucket, $srcFpath,  $dstFpath, $bizAttr=null, $sliceSize=null, $insertOnly=null) {
        $srcFpath = realpath($srcFpath);
        $fileSize = filesize($srcFpath);
        $dstFpath = $this->cosUrlEncode($dstFpath);
        $url = $this->generateResUrl($bucket, $dstFpath);
        $sliceCount = ceil($fileSize / $sliceSize);
        // expiration seconds for one slice mutiply by slice count
        // will be the expired seconds for whole file
        $expiration = time() + (self::EXPIRED_SECONDS * $sliceCount);
        if ($expiration >= (time() + 10 * 24 * 60 * 60)) {
            $expiration = time() + 10 * 24 * 60 * 60;
        }
        $signature = $this->auth->createReusableSignature($expiration, $bucket);

        $sliceUploading = new SliceUploading($this->timeout * 1000, self::MAX_RETRY_TIMES);
        for ($tryCount = 0; $tryCount < self::MAX_RETRY_TIMES; ++$tryCount) {
            if ($sliceUploading->initUploading(
                        $signature,
                        $srcFpath,
                        $url,
                        $fileSize, $sliceSize, $bizAttr, $insertOnly)) {
                break;
            }

            $errorCode = $sliceUploading->getLastErrorCode();
            if ($errorCode === -4019) {
                // Delete broken file and retry again on _ERROR_FILE_NOT_FINISH_UPLOAD error.
                Cosapi::delFile($bucket, $dstFpath);
                continue;
            }

            if ($tryCount === self::MAX_RETRY_TIMES - 1) {
                return array(
                            'code' => $sliceUploading->getLastErrorCode(),
                            'message' => $sliceUploading->getLastErrorMessage(),
                            'request_id' => $sliceUploading->getRequestId(),
                        );
            }
        }

        if (!$sliceUploading->performUploading()) {
            return array(
                        'code' => $sliceUploading->getLastErrorCode(),
                        'message' => $sliceUploading->getLastErrorMessage(),
                        'request_id' => $sliceUploading->getRequestId(),
                    );
        }

        if (!$sliceUploading->finishUploading()) {
            return array(
                        'code' => $sliceUploading->getLastErrorCode(),
                        'message' => $sliceUploading->getLastErrorMessage(),
                        'request_id' => $sliceUploading->getRequestId(),
                    );
        }

        return array(
                    'code' => 0,
                    'message' => 'SUCCESS',
                    'request_id' => $sliceUploading->getRequestId(),
                    'data' => array(
                        'access_url' => $sliceUploading->getAccessUrl(),
                        'resource_path' => $sliceUploading->getResourcePath(),
                        'source_url' => $sliceUploading->getSourceUrl(),
                    ),
                );
    }

    /*
     * 内部公共函数
     * @param  string  $bucket bucket名称
     * @param  string  $path       文件夹路径
     * @param  int     $num        拉取的总数
     * @param  string  $context    在翻页查询时候用到
     */
    private function listBase(
            $bucket, $path, $num = 20, $context = null) {
        $path = $this->cosUrlEncode($path);
        $expired = time() + self::EXPIRED_SECONDS;
        $url = $this->generateResUrl($bucket, $path);
        $signature = $this->auth->createReusableSignature($expired, $bucket);

        $data = array(
            'op' => 'list',
        );

		if ($num < 0 || $num > 199) {
            return array(
                        'code' => self::COSAPI_PARAMS_ERROR,
                        'message' => 'parameter num invalid, num need less then 200',
                    );
		}
        $data['num'] = $num;

        if (isset($context)) {
            $data['context'] = $context;
        }

        $url = $url . '?' . http_build_query($data);

        $req = array(
                    'url' => $url,
                    'method' => 'get',
                    'timeout' => $this->timeout,
                    'header' => array(
                        'Authorization: ' . $signature,
                    ),
                );

        return $this->sendRequest($req);
    }

    /*
     * 内部公共方法(更新文件和更新文件夹)
     * @param  string  $bucket  bucket名称
     * @param  string  $path        路径
     * @param  string  $bizAttr     文件/目录属性
     * @param  string  $authority:  eInvalid/eWRPrivate(私有)/eWPrivateRPublic(公有读写)
	 * @param  array   $customer_headers_array 携带的用户自定义头域,包括
     * 'Cache-Control' => '*'
     * 'Content-Type' => '*'
     * 'Content-Disposition' => '*'
     * 'Content-Language' => '*'
     * 'x-cos-meta-自定义内容' => '*'
     */
    private function updateBase(
            $bucket, $path, $bizAttr = null, $authority = null, $custom_headers_array = null) {
        $path = $this->cosUrlEncode($path);
        $expired = time() + self::EXPIRED_SECONDS;
        $url = $this->generateResUrl($bucket, $path);
        $signature = $this->auth->createNonreusableSignature($bucket, $path);

        $data = array('op' => 'update');

	    if (isset($bizAttr)) {
	        $data['biz_attr'] = $bizAttr;
	    }

	    if (isset($authority) && strlen($authority) > 0) {
			if($this->isAuthorityValid($authority) == false) {
                return array(
                        'code' => self::COSAPI_PARAMS_ERROR,
                        'message' => 'parameter authority invalid');
			}

	        $data['authority'] = $authority;
	    }

	    if (isset($custom_headers_array)) {
	        $data['custom_headers'] = array();
	        $this->add_customer_header($data['custom_headers'], $custom_headers_array);
	    }

        $data = json_encode($data);

        $req = array(
            'url' => $url,
            'method' => 'post',
            'timeout' => $this->timeout,
            'data' => $data,
            'header' => array(
                'Authorization: ' . $signature,
                'Content-Type: application/json',
            ),
        );

		return $this->sendRequest($req);
    }

    /*
     * 内部方法
     * @param  string  $bucket  bucket名称
     * @param  string  $path        文件/目录路径
     */
    private function statBase($bucket, $path) {
        $path = $this->cosUrlEncode($path);
        $expired = time() + self::EXPIRED_SECONDS;
        $url = $this->generateResUrl($bucket, $path);
        $signature = $this->auth->createReusableSignature($expired, $bucket);

        $data = array('op' => 'stat');

        $url = $url . '?' . http_build_query($data);

        $req = array(
            'url' => $url,
            'method' => 'get',
            'timeout' => $this->timeout,
            'header' => array(
                'Authorization: ' . $signature,
            ),
        );

        return $this->sendRequest($req);
    }

    /*
     * 内部私有方法
     * @param  string  $bucket  bucket名称
     * @param  string  $path        文件/目录路径路径
     */
    private function delBase($bucket, $path) {
        if ($path == "/") {
            return array(
                    'code' => self::COSAPI_PARAMS_ERROR,
                    'message' => 'can not delete bucket using api! go to ' .
                                 'http://console.qcloud.com/cos to operate bucket');
        }

        $path = $this->cosUrlEncode($path);
        $expired = time() + self::EXPIRED_SECONDS;
        $url = $this->generateResUrl($bucket, $path);
        $signature = $this->auth->createNonreusableSignature($bucket, $path);

        $data = array('op' => 'delete');

        $data = json_encode($data);

        $req = array(
            'url' => $url,
            'method' => 'post',
            'timeout' => $this->timeout,
            'data' => $data,
            'header' => array(
                'Authorization: ' . $signature,
                'Content-Type: application/json',
            ),
        );

        return $this->sendRequest($req);
    }

    /*
     * 内部公共方法, 路径编码
     * @param  string  $path 待编码路径
     */
	private function cosUrlEncode($path) {
        return str_replace('%2F', '/',  rawurlencode($path));
    }

    /*
     * 内部公共方法, 构造URL
     * @param  string  $bucket
     * @param  string  $dstPath
     */
    private function generateResUrl($bucket, $dstPath) {
        $endPoint = str_replace('region', $this->region, $this->endPoint);

        return $endPoint . $this->config['app_id'] . '/' . $bucket . $dstPath;
    }

	/*
     * 内部公共方法, 发送消息
     * @param  string  $req
     */
    private function sendRequest($req) {
        $rsp = $this->httpClient->sendRequest($req);
        if ($rsp === false) {
            return array(
                'code' => self::COSAPI_NETWORK_ERROR,
                'message' => 'network error',
            );
        }

        $info = $this->httpClient->info();
        $ret = json_decode($rsp, true);

        if ($ret === NULL) {
            return array(
                'code' => self::COSAPI_NETWORK_ERROR,
                'message' => $rsp,
                'data' => array()
            );
        }

        return $ret;
    }

    /**
     * Get slice size.
     */
	private function getSliceSize($sliceSize) {
        // Fix slice size to 1MB.
        return self::SLICE_SIZE_1M;
	}

    /*
     * 内部方法, 规整文件路径
     * @param  string  $path      文件路径
     * @param  string  $isfolder  是否为文件夹
     */
	private function normalizerPath($path, $isfolder = False) {
		if (preg_match('/^\//', $path) == 0) {
            $path = '/' . $path;
        }

        if ($isfolder == True) {
            if (preg_match('/\/$/', $path) == 0) {
                $path = $path . '/';
            }
        }

        // Remove unnecessary slashes.
        $path = preg_replace('#/+#', '/', $path);

		return $path;
	}

    /**
     * 判断authority值是否正确
     * @param  string  $authority
     * @return [type]  bool
     */
    private function isAuthorityValid($authority) {
        if ($authority == 'eInvalid' || $authority == 'eWRPrivate' || $authority == 'eWPrivateRPublic') {
            return true;
	    }
	    return false;
    }

    /**
     * 判断是否符合自定义属性
     * @param  string  $key
     * @return [type]  bool
     */
    private function isCustomer_header($key) {
        if ($key == 'Cache-Control' || $key == 'Content-Type' ||
                $key == 'Content-Disposition' || $key == 'Content-Language' ||
                $key == 'Content-Encoding' ||
                substr($key,0,strlen('x-cos-meta-')) == 'x-cos-meta-') {
            return true;
	    }
	    return false;
    }

	/**
     * 增加自定义属性到data中
     * @param  array  $data
	 * @param  array  $customer_headers_array
     * @return [type]  void
     */
    private function add_customer_header(&$data, &$customer_headers_array) {
        if (count($customer_headers_array) < 1) {
            return;
        }
	    foreach($customer_headers_array as $key=>$value) {
            if($this->isCustomer_header($key)) {
	            $data[$key] = $value;
            }
	    }
    }

    // Check |$path| is a valid file path.
    // Return true on success, otherwise return false.
    private function isValidPath($path) {
        if (strpos($path, '?') !== false) {
            return false;
        }
        if (strpos($path, '*') !== false) {
            return false;
        }
        if (strpos($path, ':') !== false) {
            return false;
        }
        if (strpos($path, '|') !== false) {
            return false;
        }
        if (strpos($path, '\\') !== false) {
            return false;
        }
        if (strpos($path, '<') !== false) {
            return false;
        }
        if (strpos($path, '>') !== false) {
            return false;
        }
        if (strpos($path, '"') !== false) {
            return false;
        }

        return true;
    }

    /**
     * Copy a file.
     * @param $bucket bucket name.
     * @param $srcFpath source file path.
     * @param $dstFpath destination file path.
     * @param $overwrite if the destination location is occupied, overwrite it or not?
     * @return array|mixed.
     */
    public function copyFile($bucket, $srcFpath, $dstFpath, $overwrite = false) {
        $srcFpath = $this->normalizerPath($srcFpath, false);
        $srcFpath = $this->cosUrlEncode($srcFpath);
        $url = $this->generateResUrl($bucket, $srcFpath);
        $sign = $this->auth->createNonreusableSignature($bucket, $srcFpath);
        $data = array(
            'op' => 'copy',
            'dest_fileid' => $dstFpath,
            'to_over_write' => $overwrite ? 1 : 0,
        );
        $req = array(
            'url' => $url,
            'method' => 'post',
            'timeout' => $this->timeout,
            'data' => $data,
            'header' => array(
                'Authorization: ' . $sign,
            ),
        );

        return $this->sendRequest($req);
    }

    /**
     * Move a file.
     * @param $bucket bucket name.
     * @param $srcFpath source file path.
     * @param $dstFpath destination file path.
     * @param $overwrite if the destination location is occupied, overwrite it or not?
     * @return array|mixed.
     */
    public function moveFile($bucket, $srcFpath, $dstFpath, $overwrite = false) {
        $srcFpath = $this->normalizerPath($srcFpath, false);
        $srcFpath = $this->cosUrlEncode($srcFpath);
        $url = $this->generateResUrl($bucket, $srcFpath);
        $sign = $this->auth->createNonreusableSignature($bucket, $srcFpath);
        $data = array(
            'op' => 'move',
            'dest_fileid' => $dstFpath,
            'to_over_write' => $overwrite ? 1 : 0,
        );
        $req = array(
            'url' => $url,
            'method' => 'post',
            'timeout' => $this->timeout,
            'data' => $data,
            'header' => array(
                'Authorization: ' . $sign,
            ),
        );

        return $this->sendRequest($req);
    }

    /**
     * Get file's url for downloading.
     * @param $bucket bucket name.
     * @param $fpath file path.
     * @param $expireAfterSecs url will expire after this secconds.
     * @return array|mixed.
     */
    public function getDownloadUrl($bucket, $fpath, $expireAfterSecs) {
        $fpath = $this->normalizerPath($fpath, false);
        $expiration = time() + $expireAfterSecs;
        $signature = $this->auth->createReusableSignature($expiration, $bucket);
        $appId = $this->config['app_id'];
        $region = $this->config['region'];

        $accessUrl = "http://$bucket-$appId.file.myqcloud.com$fpath?sign=$signature";
        $sourceUrl = "http://$bucket-$appId.cos${region}.myqcloud.com$fpath?sign=$signature";
        $url = "http://$region.file.myqcloud.com/files/v2/${appId}${fpath}?sign=$signature";

        return array(
            'code' => 0,
            'message' => 'SUCCESS',
            'data' => array(
                'access_url' => $accessUrl,
                'source_url' => $sourceUrl,
                'url' => $url,
            ),
        );
    }
}
