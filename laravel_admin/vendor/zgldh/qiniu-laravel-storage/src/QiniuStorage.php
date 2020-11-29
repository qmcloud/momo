<?php namespace zgldh\QiniuStorage;


class QiniuStorage
{
    private $storage = null;
    private static $instance = [];

    public static function disk($name)
    {
        if (!isset(self::$instance[$name])) {
            self::$instance[$name] = new self($name);
        }

        return self::$instance[$name];
    }

    private function __construct($name)
    {
        $this->storage = \Storage::disk($name);
    }

    /**
     * 文件是否存在
     * @param $key
     * @return bool
     */
    public function exists($key)
    {
        return $this->storage->exists($key);
    }

    /**
     * 获取文件内容
     * @param $key
     * @return string
     */
    public function get($key)
    {
        return $this->storage->get($key);
    }

    /**
     * 上传文件
     * @param $key
     * @param $contents
     * @return bool
     */
    public function put($key, $contents)
    {
        return $this->storage->put($key, $contents);
    }

    /**
     * 附加内容到文件开头
     * @param $key
     * @param $contents
     * @return int
     */
    public function prepend($key, $contents)
    {
        return $this->storage->prepend($key, $contents);
    }

    /**
     * 附加内容到文件结尾
     * @param $key
     * @param $content
     * @return int
     */
    public function append($key, $contents)
    {
        return $this->storage->append($key, $contents);
    }

    /**
     * 删除文件
     * @param $key
     * @return bool
     */
    public function delete($key)
    {
        return $this->storage->delete($key);

    }

    /**
     * 复制文件到新的路径
     * @param $key
     * @param $key2
     * @return bool
     */
    public function copy($key, $key2)
    {
        return $this->storage->copy($key, $key2);

    }

    /**
     * 移动文件到新的路径
     * @param $key
     * @param $key2
     * @return bool
     */
    public function move($key, $key2)
    {
        return $this->storage->move($key, $key2);

    }

    public function size($key)
    {
        return $this->storage->size($key);

    }

    public function lastModified($key)
    {
        return $this->storage->lastModified($key);

    }

    public function files($key)
    {
        return $this->storage->files($key);
    }

    public function allFiles($key)
    {
        return $this->storage->files($key);
    }

    public function directories($key)
    {
        return $this->storage->files($key);
    }

    public function allDirectories($key)
    {
        return $this->storage->files($key);
    }

    public function makeDirectory($key)
    {
        return $this->storage->makeDirectory($key);
    }

    public function deleteDirectory($key)
    {
        return $this->storage->deleteDirectory($key);
    }

    /**
     * 获取上传Token
     * @param $key
     * @param $expires
     * @param $policy
     * @param $strictPolicy
     * @return bool
     */
    public function uploadToken($key = null, $expires = 3600, $policy = null, $strictPolicy = true)
    {
        return $this->storage->getDriver()->uploadToken($key, $expires, $policy, $strictPolicy);
    }

    /**
     *  下次 put 操作，将使用该 uploadToken 进行上传。 常用于持久化操作。
     * @param $token
     * @return mixed
     */
    public function withUploadToken($token)
    {
        $this->storage->getDriver()->withUploadToken($token);
    }

    /**
     * 获取下载地址
     * @param $key
     * @return mixed
     */
    public function downloadUrl($key, $domainType = 'default')
    {
        return $this->storage->getDriver()->downloadUrl($key, $domainType);
    }

    /**
     * 获取私有bucket下载地址
     * @param $key
     * @return mixed
     */
    public function privateDownloadUrl($key, $domainType = 'default')
    {
        return $this->storage->getDriver()->privateDownloadUrl($key, $domainType);
    }

    /**
     * 获取多媒体文件信息
     * @param $key
     * @return mixed
     */
    public function avInfo($key)
    {
        return $this->storage->getDriver()->avInfo($key);
    }

    /**
     * 获取图片信息
     * @param $key
     * @return mixed
     */
    public function imageInfo($key)
    {
        return $this->storage->getDriver()->imageInfo($key);
    }

    /**
     * 获取图片EXIF信息
     * @param $key
     * @return mixed
     */
    public function imageExif($key)
    {
        return $this->storage->getDriver()->imageExif($key);
    }

    /**
     * 获取图片预览URL
     * @param $key
     * @param $opts
     * @return mixed
     */
    public function imagePreviewUrl($key, $opts)
    {
        return $this->storage->getDriver()->imagePreviewUrl($key, $opts);
    }

    /**
     * 获取私有bucket图片预览URL
     * @param $key
     * @param $opts
     * @return mixed
     */
    public function privateImagePreviewUrl($key, $opts)
    {
        return $this->storage->getDriver()->privateImagePreviewUrl($key, $opts);
    }

    /**
     * 执行持久化数据处理
     * @param $key
     * @param $opts
     * @param $pipline
     * @param $force
     * @param $notify_url
     * @return mixed
     */
    public function persistentFop($key, $opts, $pipline = null, $force = false, $notify_url = null)
    {
        return $this->storage->getDriver()->persistentFop($key, $opts, $pipline, $force, $notify_url);
    }

    /**
     * 查看持久化数据处理的状态
     * @param $id
     * @return mixed
     */
    public function persistentStatus($id)
    {
        return $this->storage->getDriver()->persistentStatus($id);
    }

    /**
     * 验证回调是否合法
     * @param $id
     * @return boolean
     */
    public function verifyCallback($contentType, $originAuthorization, $url, $body)
    {
        return $this->storage->getDriver()->verifyCallback($contentType, $originAuthorization, $url, $body);
    }

    /**
     * 调用fetch将 foo.jpg 数据以 bar.jpg 的名字储存起来。
     * @param $url
     * @param $key
     * @return bool
     */
    public function fetch($url, $key)
    {
        return $this->storage->getDriver()->fetch($url, $key);
    }

    /**
     * 得到最后一次执行 put, copy, append 等写入操作后，得到的hash值。详见 https://github.com/qiniu/qetag
     * @return string
     */
    public function qetag()
    {
        return $this->storage->getDriver()->qetag();
    }

    /**
     * 得到最后一次执行 put, copy, append 等写入操作后，得到的返回值。
     * @return array
     */
    public function lastReturn()
    {
        return $this->storage->getDriver()->getLastReturn();
    }
}
