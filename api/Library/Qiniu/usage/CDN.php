<?php
/**
 * CDN云端接口
 *
 * @author: dogstar 2015-03-17
 */

class Api_CDN extends PhalApi_Api
{
    const CODE_MISS_UPLOAD_FILE = 1;
    const CODE_FAIL_TO_UPLOAD_FILE = 2;
    const CODE_FAIL_TO_UPDATE = 3;

    public function uploadFile()
    {
    	$rs = array('code' => self::CODE_FAIL_TO_UPLOAD_FILE, 'url' => '', 'msg' => T('fail to upload file'));
    	
    	if (!isset($_FILES['file'])) {
            $rs['code'] = self::CODE_MISS_UPLOAD_FILE;
            $rs['msg'] = T('miss upload file');
    		return $rs;
    	}
    	
    	if ($_FILES["file"]["error"] > 0) {
            $rs['code'] = self::CODE_FAIL_TO_UPLOAD_FILE;
            $rs['msg'] = T('failed to upload file with error: {error}', array('error' => $_FILES['file']['error']));
    		DI()->logger->debug('failed to upload file with error: ' . $_FILES['file']['error']);
    		return $rs;
    	}
    	
    	$url = DI()->qiniu->uploadFile($_FILES['file']['tmp_name']);
    	if (!empty($url)) {
    		$rs['code'] = 0;
            $rs['url'] = $url;
            $rs['msg'] = '';
    	}
    	@unlink($_FILES['file']['tmp_name']);
    	
    	return $rs;
    }
}
