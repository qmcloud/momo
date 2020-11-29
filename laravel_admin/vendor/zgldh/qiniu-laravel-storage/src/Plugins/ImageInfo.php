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
 * Class ImageInfo
 * 查看图像属性 <br>
 * $disk        = \Storage::disk('qiniu'); <br>
 * $re          = $disk->getDriver()->imageInfo('foo/bar1.css'); <br>
 * @package zgldh\QiniuStorage\Plugins
 */
class ImageInfo extends AbstractPlugin {

    /**
     * Get the method name.
     *
     * @return string
     */
    public function getMethod()
    {
        return 'imageInfo';
    }

    public function handle($path = null)
    {
        return $this->filesystem->getAdapter()->imageInfo($path);
    }
}