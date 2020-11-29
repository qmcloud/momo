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
 * Class WithUploadToken
 * 下次 put 操作，将使用该 uploadToken 进行上传。 常用于持久化操作。 <br>
 * $disk        = \Storage::disk('qiniu'); <br>
 * $re          = $disk->getDriver()->withUploadToken($token); <br>
 * @package zgldh\QiniuStorage\Plugins
 */
class WithUploadToken extends AbstractPlugin
{

    /**
     * Get the method name.
     *
     * @return string
     */
    public function getMethod()
    {
        return 'withUploadToken';
    }

    public function handle($token)
    {
        $this->filesystem->getAdapter()->withUploadToken($token);
    }
}