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
     * Class PrivateDownloadUrl
     * 得到私有资源下载地址 <br>
     * $disk        = \Storage::disk('qiniu'); <br>
     * $re          = $disk->getDriver()->privateDownloadUrl('foo/bar1.css'); <br>
     * @package zgldh\QiniuStorage\Plugins
     */
    class PrivateDownloadUrl extends AbstractPlugin
    {

        /**
         * Get the method name.
         *
         * @return string
         */
        public function getMethod()
        {
            return 'privateDownloadUrl';
        }

        public function handle($path = null, $settings = 'default')
        {
            $adapter = $this->filesystem->getAdapter();
            return $adapter->privateDownloadUrl($path, $settings);
        }
    }