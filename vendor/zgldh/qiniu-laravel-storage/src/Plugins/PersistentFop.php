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
 * Class PersistentFop
 * 执行持久化操作 <br>
 * $disk        = \Storage::disk('qiniu'); <br>
 * $re          = $disk->getDriver()->persistentFop('foo/bar1.css'); <br>
 * @package zgldh\QiniuStorage\Plugins
 */
class PersistentFop extends AbstractPlugin {

    /**
     * Get the method name.
     *
     * @return string
     */
    public function getMethod()
    {
        return 'persistentFop';
    }

    public function handle($path = null, $fops = null, $pipline = null, $force = false, $notify_url = null)
    {
        return $this->filesystem->getAdapter()->persistentFop($path, $fops, $pipline, $force, $notify_url);
    }
}