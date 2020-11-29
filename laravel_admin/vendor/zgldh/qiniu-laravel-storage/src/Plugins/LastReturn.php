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
 * Class LastReturn
 * 得到最后一次上传文件的 返回值 <br>
 * $disk        = \Storage::disk('qiniu'); <br>
 * $re          = $disk->getDriver()->lastReturn(); <br>
 * @package zgldh\QiniuStorage\Plugins
 */
class LastReturn extends AbstractPlugin
{
    /**
     * Get the method name.
     *
     * @return string
     */
    public function getMethod()
    {
        return 'lastReturn';
    }

    public function handle($path = null)
    {
        return $this->filesystem->getAdapter()->getLastReturn();
    }
}
