#PhalApi-Image -- 图像处理

![](http://webtools.qiniudn.com/master-LOGO-20150410_50.jpg)

##前言
因为在工作中遇到了需要按照尺寸压缩上传图片,进过了一番寻找发现ThinkPhP图形处理能够满足大部分需求,
所以特地制作成拓展的方式提供出来希望,能给给为小伙伴排忧解难!

**注:特别鸣谢@麦当苗儿 <zuojiazi@vip.qq.com>**

附上:

官网地址:[http://www.phalapi.net/](http://www.phalapi.net/ "PhalApi官网")

开源中国Git地址:[http://git.oschina.net/dogstar/PhalApi/tree/release](http://git.oschina.net/dogstar/PhalApi/tree/release "开源中国Git地址")

开源中国拓展Git地址:[http://git.oschina.net/dogstar/PhalApi-Library](http://git.oschina.net/dogstar/PhalApi-Library "开源中国Git地址")


##1. 安装

配置方式非常简单只需要把拓展下载下来放入Library文件内即可,然后就可以使用如下方法进行实例

	//初始化
	$PhalApi_Image = new Image_Lite(IMAGE_GD, "图片地址");
	//以上这句话也可以写成如下 默认使用GD库
	$PhalApi_Image = new Image_Lite();
	//打开图片
	$PhalApi_Image->open('./1.jpg');

##2.基础方法

	$width  = $PhalApi_Image->width(); // 返回图片的宽度
	$height = $PhalApi_Image->height(); // 返回图片的高度
	$type   = $PhalApi_Image->type(); // 返回图片的类型
	$mime   = $PhalApi_Image->mime(); // 返回图片的mime类型
	$size   = $PhalApi_Image->size(); // 返回图片的尺寸数组 0 图片宽度 1 图片高度


##3. 压缩裁剪

图片处理最关键的一项功能就是压缩和裁剪,比如用户上传了一套图片2Mb*10张=20MB让我们直接把原图交给用户的时候这个流量几乎承担不起所以就需要使用到图片压缩以及裁剪技术(具体看业务需求)

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

##4. 图片水印

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


##5. 总结

希望此拓展能够给大家带来方便以及实用,拓展支持GIF文件处理,并且支持GD库和Imagick库可以根据需求自行选择!

注:笔者能力有限有说的不对的地方希望大家能够指出,也希望多多交流!

**官网QQ交流群:421032344  欢迎大家的加入!**