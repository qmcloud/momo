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
 * Class ImageExif
 * 查看图像EXIF <br>
 * $disk        = \Storage::disk('qiniu'); <br>
 * $re          = $disk->getDriver()->imageExif('foo/bar1.css'); <br>
 * @package zgldh\QiniuStorage\Plugins
 */
class ImageExif extends AbstractPlugin {

    /**
     * Get the method name.
     *
     * @return string
     */
    public function getMethod()
    {
        return 'imageExif';
    }

    public function handle($path = null)
    {
        return $this->filesystem->getAdapter()->imageExif($path);
    }
}