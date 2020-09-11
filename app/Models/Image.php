<?php

namespace App\Models;

use App\Models\Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Mockery\Exception;
use function GuzzleHttp\Psr7\str;
use zgldh\QiniuStorage\QiniuStorage;


class Image extends Model
{
    protected $table = 'images';

    public function author()
    {
        return $this->belongsTo(User::class, 'uploader');
    }

    public function setImageAttribute($image)
    {
        if (is_array($image)) {
            $this->attributes['image'] = json_encode($image);
        }
    }

    public function getImageAttribute($image)
    {
        return json_decode($image, true);
    }

    protected function upload_img($file = [])
    {
        // 此时 $this->upload如果成功就返回文件名不成功返回false
        //读取配置判断是存储类型
        $DBTYPEConfig = Config::get("settings_db.dbtype");
        if ($DBTYPEConfig == "Qiniu") {
            //七牛上传
            $fileName = $this->uploadQN($file);
        } else if ($DBTYPEConfig == "Local") {
            //本地上传
            $fileName = $this->uploadBD($file);
        }
        return $fileName;

    }

    /**
     * 上传文件到本地
     * @param Request $request [description]
     * @return [type]                            [description]
     * @author
     * @date   2020-9-09T16:58:37+0800
     */
    public function uploadBD($file, $disk = 'public')
    {
        try {
            // 1.是否上传成功
            if (!$file->isValid()) {
                return false;
            }
            // 2.是否符合文件类型 getClientOriginalExtension 获得文件后缀名
            $fileExtension = $file->getClientOriginalExtension();
            $ExtensionConfig = Config::get("settings_upload");
            if (!in_array($fileExtension, $ExtensionConfig['image_ext'])) {
                return false;
            }

            // 3.判断大小是否符合 2M
            $tmpFile = $file->getRealPath();
            if ($ExtensionConfig['image_size'] != 0) {
                if (((filesize($tmpFile) / 1024) / 1024) >= $ExtensionConfig['image_size']) {
                    return false;
                }
            }

            // 4.是否是通过http请求表单提交的文件
            if (!is_uploaded_file($tmpFile)) {
                return false;
            }

            // 5.每天一个文件夹,分开存储, 生成一个随机文件名
            $fileName = 'images/' . date('Y_m_d') . '/' . md5(time()) . mt_rand(0, 9999) . '.' . $fileExtension;
            /*
             * 通过映射到 public/images 方可显示 使用命令 php artisan storage:link
             *
             * 我自己在执行这个命令的时候报了个错symlink() has been disabled for security reasons.
             *
             * 这是因为symlink()这个函数被禁用了，打开php.ini文件，搜索disable_functions
             * */
            if (Storage::disk($disk)->put($fileName, file_get_contents($tmpFile))) {
                $url = $_SERVER["SERVER_NAME"] . Storage::url($fileName);
                if (strstr($url, "http") || strstr($url, "https")) {
                    return $url;
                } else {
                    return "http://" . $url;
                }

            }
        } catch (Exception $e) {

        }

    }

    /**
     * 上传文件到七牛
     * @param Request $request [description]
     * @return [type]                            [description]
     * @author
     * @date   2020-9-09T16:58:37+0800
     */
    public function uploadQN($file)
    {
        // 1.是否上传成功
        if (!$file->isValid()) {
            return false;
        }
        // 初始化
        // 需要填写你的 Access Key 和 Secret Key
        /*$accessKey = Config::get('settings_db.qn_accessKey');
        $secretKey = Config::get('settings_db.qn_secretKey');
        // 构建鉴权对象
        $auth = new Auth($accessKey, $secretKey);
        // 要上传的空间
        $bucket = Config::get('settings_db.bucket');
        // 生成上传 Token
        $token = $auth->uploadToken($bucket);
        // 要上传文件的本地路径
        $filePath = $file->getRealPath();
        // 上传到七牛后保存的文件名
        $date = time();
        $docname=Config::get('settings_db.qn_name');
        $key = $docname.'/'.$date.'.'.$file->getClientOriginalExtension();
        // 初始化 UploadManager 对象并进行文件的上传。
        $uploadMgr = new UploadManager();
        // 调用 UploadManager 的 putFile 方法进行文件的上传。
        list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
        if ($err !== null) {
            return response()->json(['ResultData'=>'失败','info'=>'失败']);
        } else {
            return redirect('/'.$docname);
        }*/
        $disk = QiniuStorage::disk('qiniu');
        $fileName = md5($file->getClientOriginalName() . time() . rand()) . '.' . $file->getClientOriginalExtension();
        $bool = $disk->put(getConfigs('settings_db.disk') . $fileName, file_get_contents($file->getRealPath()));
        if ($bool) {
            $path = $disk->downloadUrl(getConfigs('settings_db.disk') . $fileName);
        } else {
            $path = '';
        }

        return $path;
    }
}
