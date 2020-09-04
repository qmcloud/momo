<?php
/**
 * Created by PhpStorm.
 * User: ZhangWB
 * Date: 2015/4/21
 * Time: 16:42
 */

namespace zgldh\QiniuStorage\Plugins;

use League\Flysystem\Plugin\AbstractPlugin;

/**
 * Class UploadToken
 * 获取上传Token <br>
 * $disk        = \Storage::disk('qiniu'); <br>
 * $re          = $disk->getDriver()->uploadToken('foo/bar1.css'); <br>
 * @package zgldh\QiniuStorage\Plugins
 */
class UploadToken extends AbstractPlugin {

    /**
     * Get the method name.
     *
     * @return string
     */
    public function getMethod()
    {
        return 'uploadToken';
    }

    public function handle($path = null, $expires = 3600, $policy = null, $strictPolicy = true)
    {
        return $this->filesystem->getAdapter()->uploadToken($path, $expires, $policy, $strictPolicy);
    }
}