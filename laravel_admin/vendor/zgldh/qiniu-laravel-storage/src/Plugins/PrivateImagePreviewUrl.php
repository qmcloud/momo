<?php
/**
 * Created by PhpStorm.
 * User: Frowhy
 * Date: 2016/5/24
 * Time: 13:09
 */

namespace zgldh\QiniuStorage\Plugins;

use League\Flysystem\Plugin\AbstractPlugin;

/**
 * Class PrivateImagePreviewUrl
 * 获取私有bucket图片预览URL <br>
 * $disk        = \Storage::disk('qiniu'); <br>
 * $re          = $disk->getDriver()->privateImagePreviewUrl('foo/bar1.css',$ops); <br>
 * @package zgldh\QiniuStorage\Plugins
 */
class PrivateImagePreviewUrl extends AbstractPlugin {

    /**
     * Get the method name.
     *
     * @return string
     */
    public function getMethod()
    {
        return 'privateImagePreviewUrl';
    }

    public function handle($path = null, $ops = null)
    {
        return $this->filesystem->getAdapter()->privateImagePreviewUrl($path, $ops);
    }
}