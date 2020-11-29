<?php
/**
 * Created by PhpStorm.
 * User: 猫巷
 * Email:catlane@foxmail.com
 * Date: 2019/5/28
 * Time: 2:40 PM
 */

namespace Encore\ChunkFileUpload;

use App\Models\Config;
use Encore\Admin\Form\Field;

class ChunkFileField extends Field {
    public $view = 'chunk-file-upload::chunk-file-upload';

    protected $disk;//磁盘
    protected $extensions;//后缀
    protected $mimeTypes;//文件蕾西
    protected $fileSizeLimit;//文件总大小
    protected $fileNumLimit;//上传数量
    protected $saveType;//保存格式

    public function __construct ( $column , array $arguments = [] ) {
        $this->disk          = config ( 'chunk_file_upload.default.disk' );
        $ExtensionConfig=Config::get("settings_upload");
        $this->extensions    = $ExtensionConfig['file_ext'];
        $this->mimeTypes     = 'image/*,video/*';
        $this->fileSizeLimit = $ExtensionConfig['file_size'];
        $this->fileNumLimit  = $ExtensionConfig['limit'];
        $this->saveType      = 'json';
        parent::__construct ( $column , $arguments );
    }


    public function disk ( $disk ) {
        $this->disk = $disk;
        return $this;
    }

    public function extensions ( $extensions ) {
        $this->extensions = $extensions;
        return $this;
    }

    public function mimeTypes ( $mimeTypes ) {
        $this->mimeTypes = $mimeTypes;
        return $this;
    }

    public function fileSizeLimit ( $fileSizeLimit ) {
        $this->fileSizeLimit = $fileSizeLimit;
        return $this;
    }

    public function fileNumLimit ( $fileNumLimit ) {
        $this->fileNumLimit = $fileNumLimit;
        return $this;
    }

    public function saveType ( $saveType ) {
        $this->saveType = $saveType;
        return $this;
    }

    public function render () {
        if ( ! $this->disk ) {//如果没有，就用默认的
            $driver = 'local';
        } else {
            $config = config ( 'chunk_file_upload.disks.' . $this->disk );
            if ( ! $config ) {//如果没有
                $driver = 'local';
            } else {
                $driver = $config[ 'driver' ];
            }
        }

        $name   = $this->formatName ( $this->column );
        $prefix = config ( 'admin.route.prefix' );
        $prefix = trim ( $prefix , '/' );
        $prefix = $prefix ? '/' . $prefix : '';

        $qn_area      = isset( $config[ 'qn_area' ] ) ? $config[ 'qn_area' ] : '';
        $url      = isset( $config[ 'url' ] ) ? $config[ 'url' ] : '';
        $this->script = <<<SRC
        window.chunk_file.fileNumLimit = '$this->fileNumLimit';//文件数量
        window.chunk_file.saveType = '$this->saveType';//保存格式
        window.chunk_file.fileSizeLimit = '$this->fileSizeLimit';//文件总大小
        window.chunk_file.prefix = '$prefix';//文件总大小
        window.chunk_file.area = '{$qn_area}';//文件总大小
        window.chunk_file.url = 'http://{$url}';//文件域名

        //没有这个变量
        if(typeof uploaders == 'undefined'){
            uploaders = [];
        }

        accept = [
            {
                title: 'accepts',
                extensions: '{$this->extensions}',
                mimeTypes: '{$this->mimeTypes}'
            }
        ];
		upload = chunk_file ('$name',accept,'$this->disk','$driver');
		uploaders.push(upload)
		console.log(uploaders)

SRC;

        return parent::render ();
    }
}

