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
 * Class Qetag
 * 得到最后一次上传文件的 Qetag <br>
 * $disk        = \Storage::disk('qiniu'); <br>
 * $re          = $disk->getDriver()->qetag(); <br>
 * @package zgldh\QiniuStorage\Plugins
 */
class Qetag extends AbstractPlugin
{
    /**
     * Get the method name.
     *
     * @return string
     */
    public function getMethod()
    {
        return 'qetag';
    }

    public function handle($path = null)
    {
        return $this->filesystem->getAdapter()->getLastQetag();
    }
}
