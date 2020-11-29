<?php

use Encore\ChunkFileUpload\Http\Controllers\ChunkFileUploadController;


Route::get('chunk-file-upload/get_qiniu_token', ChunkFileUploadController::class.'@getQiniuToken');
Route::post('chunk-file-upload/upload', ChunkFileUploadController::class.'@upload');
Route::post('chunk-file-upload/upload/mkblk/{block}', ChunkFileUploadController::class.'@uploadMkblk');
Route::post('chunk-file-upload/upload/mkfile/{file_size}/key/{file_name}', ChunkFileUploadController::class.'@mkFileByKey');
Route::post('chunk-file-upload/upload/mkfile/{file_size}', ChunkFileUploadController::class.'@mkFileRand');

//php artisan vendor:publish --provider=Encore\ChunkFileUpload\ChunkFileUploadServiceProvider
//php artisan vendor:publish --tag=chunk-file-upload 发布静态资源

