<?php

namespace App\Admin\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
class UploadController extends Controller
{
    const MAX_UPLOAD_NUM = 25;
    const UPLOAD_PATH = 'XiaoT';

    // 待优化
    function uploadImg(Request $request)
    {
        $file = $request->file("mypic");
        if (!empty($file)) {
            $len = count($file);
            if ($len > static::MAX_UPLOAD_NUM) {
                return response()->json(['ResultData' => 6, 'info' => '最多可以上传25张图片']);
            }
            $m = 0;
            $k = 0;
            for ($i = 0; $i < $len; $i++) {
                // $n 表示第几张图片
                $n = $i + 1;
                if ($file[$i]->isValid()) {
                    if (in_array(strtolower($file[$i]->extension()), ['jpeg', 'jpg', 'gif', 'gpeg', 'png'])) {
                        $picname = $file[$i]->getClientOriginalName();//获取上传原文件名
                        $ext = $file[$i]->getClientOriginalExtension();//获取上传文件的后缀名
                        // 重命名
                        $filename = time() . str_random(6) . "." . $ext;
                        $re = Storage::putFile(static::UPLOAD_PATH,$file[$i]);
                        if ($re) {
                            $newFileName = config('filesystems.disks.oss.url').'/'.$re;
                            $m = $m + 1;
                            // return response()->json(['ResultData' => 0, 'info' => '上传成功', 'newFileName' => $newFileName ]);
                        } else {
                            $k = $k + 1;
                            // return response()->json(['ResultData' => 4, 'info' => '上传失败']);
                        }
                        $msg = $m . "张图片上传成功 " . $k . "张图片上传失败<br>";
                        $return[] = ['ResultData' => 0, 'info' => $msg, 'newFileName' => $newFileName];
                    } else {
                        return response()->json(['ResultData' => 3, 'info' => '第' . $n . '张图片后缀名不合法!<br/>' . '只支持jpeg/jpg/png/gif格式']);
                    }
                } else {
                    return response()->json(['ResultData' => 1, 'info' => '第' . $n . '张图片超过最大限制!<br/>' . '图片最大支持2M']);
                }
            }

        } else {
            return response()->json(['ResultData' => 5, 'info' => '请选择文件']);
        }
        return $return;
    }

    // 上传到服务器  待优化
    function uploadImgToServer(Request $request){
        $file = $request->file("mypic");
        if (!empty($file)) {
            $len = count($file);
            foreach ($file as $key => $value) {
                $len = $key;
            }
            if ($len > static::MAX_UPLOAD_NUM) {
                return response()->json(['ResultData' => 6, 'info' => '最多可以上传25张图片']);
            }
            $m = 0;
            $k = 0;
            for ($i = 0; $i <= $len; $i++) {
                // $n 表示第几张图片
                $n = $i + 1;
                if ($file[$i]->isValid()) {
                    if (in_array(strtolower($file[$i]->extension()), ['jpeg', 'jpg', 'gif', 'gpeg', 'png'])) {
                        $picname = $file[$i]->getClientOriginalName();//获取上传原文件名
                        $ext = $file[$i]->getClientOriginalExtension();//获取上传文件的后缀名
                        // 重命名
                        $filename = time() . str_random(6) . "." . $ext;
                        Storage::putFile($filename,$file);
                        if ($file[$i]->move("uploads/images", $filename)) {
                            $newFileName = '/' . "uploads/images" . '/' . $filename;
                            $m = $m + 1;
                            // return response()->json(['ResultData' => 0, 'info' => '上传成功', 'newFileName' => $newFileName ]);
                        } else {
                            $k = $k + 1;
                            // return response()->json(['ResultData' => 4, 'info' => '上传失败']);
                        }
                        $msg = $m . "张图片上传成功 " . $k . "张图片上传失败<br>";
                        $return[] = ['ResultData' => 0, 'info' => $msg, 'newFileName' => $newFileName];
                    } else {
                        return response()->json(['ResultData' => 3, 'info' => '第' . $n . '张图片后缀名不合法!<br/>' . '只支持jpeg/jpg/png/gif格式']);
                    }
                } else {
                    return response()->json(['ResultData' => 1, 'info' => '第' . $n . '张图片超过最大限制!<br/>' . '图片最大支持2M']);
                }
            }

        } else {
            return response()->json(['ResultData' => 5, 'info' => '请选择文件']);
        }
        return $return;
    }
}
