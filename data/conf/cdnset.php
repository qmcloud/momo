<?php	return array (

    'ios'=>array(
        'codingmode' => '2',  //编码 0自动，1软编，2硬编
        'resolution' => '5',  //分辨率  
        'isauto' => '1',  //是否自适应 0否1是   
        'fps' => '20',  //帧数
        'fps_min' => '20',  //最低帧数
        'fps_max' => '30',  //最高帧数
        'gop' => '3',  //关键帧间隔 
        'bitrate' => '800',  //初始码率  kbps
        'bitrate_min' => '800',  //最低码率
        'bitrate_max' => '1200',  //最高码率
        'audiorate' => '44100',  //音频采样率  Hz
        'audiobitrate' => '48',  //音频码率 kbps
        
        'preview_fps' => '15',  //预览帧数
        'preview_resolution' => '1',  //预览分辨率
    ),
    'android'=>array(
        'codingmode' => '3',  //编码 1自动，3软编，2硬编
        'resolution' => '1',  //分辨率 
        'isauto' => '1',  //是否自适应 0否1是 
        'fps' => '20',  //帧数
        'fps_min' => '20',  //最低帧数
        'fps_max' => '30',  //最高帧数
        'gop' => '3',  //关键帧间隔 
        'bitrate' => '500',  //初始码率  kbps
        'bitrate_min' => '500',  //最低码率
        'bitrate_max' => '800',  //最高码率
        'audiorate' => '44100',  //音频采样率  Hz
        'audiobitrate' => '48',  //音频码率 kbps
        
        'preview_fps' => '15',  //预览帧数
        'preview_resolution' => '1',  //预览分辨率
    ),
    
    /* IOS分辨率 -金山版本
        1 AVCaptureSessionPreset320x240
        2 AVCaptureSessionPreset352x288
        3 AVCaptureSessionPreset640x480    //腾讯版-  360_640
        4 AVCaptureSessionPreset960x540    //腾讯版-  540_960
        5 AVCaptureSessionPreset1280x720   //腾讯版-  720_1280
        6 AVCaptureSessionPreset1920x1080
        7 AVCaptureSessionPreset3840x2160
        8 AVCaptureSessionPresetiFrame960x540
        9 AVCaptureSessionPresetiFrame1280x720

       安卓分辨率 -金山版本
        0 VIDEO_RESOLUTION_360P  //腾讯版- 360_640
        1 VIDEO_RESOLUTION_480P  //腾讯版- 540_960
        2 VIDEO_RESOLUTION_540P  //腾讯版- 720_1280
        3 VIDEO_RESOLUTION_720P
        4 VIDEO_RESOLUTION_1080P
    */
);