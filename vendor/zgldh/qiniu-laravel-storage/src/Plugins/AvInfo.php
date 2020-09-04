<?php
/**
 * Created by PhpStorm.
 * User: MarlonFan
 * Date: 2017/1/13
 * Time: 16:44
 */

namespace zgldh\QiniuStorage\Plugins;

use League\Flysystem\Plugin\AbstractPlugin;

/**
 * Class AvInfo
 * 查看多媒体文件属性 <br>
 * $disk        = \Storage::disk('qiniu'); <br>
 * $re          = $disk->getDriver()->avInfo('filename.mp3'); <br>
 * @package zgldh\QiniuStorage\Plugins
 */
class AvInfo extends AbstractPlugin {

    /**
     * Get the method name.
     *
     * @return string
     */
    public function getMethod()
    {
        return 'avInfo';
    }

    public function handle($path = null)
    {
        return $this->filesystem->getAdapter()->avInfo($path);
    }
}
