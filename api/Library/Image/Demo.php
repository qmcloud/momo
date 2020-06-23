<?php

//支持Gd库和Imagick库，包括对GIf图像处理的支持。

//初始化
$PhalApi_Image = new Image_Lite(IMAGE_GD, "图片地址");
//以上这句话也可以写成如下 默认使用GD库
$PhalApi_Image = new Image_Lite();
//打开图片
$PhalApi_Image->open('./1.jpg');

$width  = $PhalApi_Image->width(); // 返回图片的宽度
$height = $PhalApi_Image->height(); // 返回图片的高度
$type   = $PhalApi_Image->type(); // 返回图片的类型
$mime   = $PhalApi_Image->mime(); // 返回图片的mime类型
$size   = $PhalApi_Image->size(); // 返回图片的尺寸数组 0 图片宽度 1 图片高度


/**
 * 可以支持其他类型的缩略图生成，设置包括下列常量或者对应的数字：
 * IMAGE_THUMB_SCALING      //常量，标识缩略图等比例缩放类型
 * IMAGE_THUMB_FILLED       //常量，标识缩略图缩放后填充类型
 * IMAGE_THUMB_CENTER       //常量，标识缩略图居中裁剪类型
 * IMAGE_THUMB_NORTHWEST    //常量，标识缩略图左上角裁剪类型
 * IMAGE_THUMB_SOUTHEAST    //常量，标识缩略图右下角裁剪类型
 * IMAGE_THUMB_FIXED        //常量，标识缩略图固定尺寸缩放类型
 */

// 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.jpg
$PhalApi_Image->thumb(150, 150, IMAGE_THUMB_SCALING);
$PhalApi_Image->save("thumb.jpg");

//将图片裁剪为400x400并保存为corp.jpg
$PhalApi_Image->crop(400, 400)->save('./crop.jpg');

//将图片裁剪为400x400并保存为corp.jpg  从（100，30）开始裁剪
$PhalApi_Image->crop(400, 400, 100, 30)->save('./crop.jpg');


/**
 * water方法的第二个参数表示水印的位置，可以传入下列常量或者对应的数字：
 * IMAGE_WATER_NORTHWEST =   1 ; //左上角水印
 * IMAGE_WATER_NORTH     =   2 ; //上居中水印
 * IMAGE_WATER_NORTHEAST =   3 ; //右上角水印
 * IMAGE_WATER_WEST      =   4 ; //左居中水印
 * IMAGE_WATER_CENTER    =   5 ; //居中水印
 * IMAGE_WATER_EAST      =   6 ; //右居中水印
 * IMAGE_WATER_SOUTHWEST =   7 ; //左下角水印
 * IMAGE_WATER_SOUTH     =   8 ; //下居中水印
 * IMAGE_WATER_SOUTHEAST =   9 ; //右下角水印
 */

//添加图片水印
$PhalApi_Image->open('./1.jpg');
//将图片裁剪为440x440并保存为corp.jpg
$PhalApi_Image->crop(440, 440)->save('./crop.jpg');
// 给裁剪后的图片添加图片水印（水印文件位于./logo.png），位置为右下角，保存为water.gif
$PhalApi_Image->water('./logo.png')->save("water.gif");
// 给原图添加水印并保存为water_o.gif（需要重新打开原图）
$PhalApi_Image->open('./1.jpg')->water('./logo.png')->save("water_o.gif");

//还可以支持水印图片的透明度（0~100，默认值是80），例如：
// 在图片左上角添加水印（水印文件位于./logo.png） 水印图片的透明度为50 并保存为water.jpg
$PhalApi_Image->open('./1.jpg')->water('./logo.png', IMAGE_WATER_NORTHWEST, 50)->save("water.jpg");

//也可以支持给图片添加文字水印（假设在入口文件的同级目录下存在1.ttf字体文件），例如：
// 在图片右下角添加水印文字 ThinkPHP 并保存为new.jpg
$PhalApi_Image->open('./1.jpg')->text('ThinkPHP', './1.ttf', 20, '#000000', IMAGE_WATER_SOUTHEAST)->save("new.jpg");


