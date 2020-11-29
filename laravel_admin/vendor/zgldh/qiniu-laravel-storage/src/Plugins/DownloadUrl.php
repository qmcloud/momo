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
     * Class DownloadUrl
     * 得到公有资源下载地址 <br>
     * $disk        = \Storage::disk('qiniu'); <br>
     * $re          = $disk->getDriver()->downloadUrl('foo/bar1.css'); <br>
     * @package zgldh\QiniuStorage\Plugins
     */
    class DownloadUrl extends AbstractPlugin
    {

        /**
         * Get the method name.
         *
         * @return string
         */
        public function getMethod()
        {
            return 'downloadUrl';
        }

        public function handle($path = null, $domainType = 'default')
        {
            $adapter = $this->filesystem->getAdapter();
            return $adapter->downloadUrl($path, $domainType);
        }
    }