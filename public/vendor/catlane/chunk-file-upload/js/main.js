function chunk_file ( name , accept , disk , driver ) {
    var initVal = $ ( '#' + name + '-savedpath' ).val ();
    initVal = initVal ? true : false;
    var $wrap = $ ( '#uploader' + name ) ,
        // 图片容器
        $queue = $ ( '<ul class="filelist"></ul>' )
            .appendTo ( $wrap.find ( '#queueList' + name ) ) ,

        // 状态栏，包括进度和控制按钮
        $statusBar = $wrap.find ( '.statusBar' ) ,

        // 文件总体选择信息。
        $info = $statusBar.find ( '.info' ) ,

        // 上传按钮
        $upload = $wrap.find ( '.uploadBtn' ) ,

        // 没选择文件之前的内容。
        $placeHolder = $wrap.find ( '.placeholder' ) ,

        $progress = $statusBar.find ( '.progress' ).hide () ,
        // 添加的文件数量
        fileCount = 0 ,

        // 添加的文件总大小
        fileSize = 0 ,

        // 优化retina, 在retina下这个值是2
        ratio = window.devicePixelRatio || 1 ,

        // 缩略图大小
        thumbnailWidth = 110 * ratio ,
        thumbnailHeight = 110 * ratio ,

        // 可能有pedding, ready, uploading, confirm, done.
        state = 'pedding' ,

        // 所有文件的进度信息，key为file id
        percentages = {} ,
        // 判断浏览器是否支持图片的base64
        isSupportBase64 = (function () {
            var data = new Image ();
            var support = true;
            data.onload = data.onerror = function () {
                if ( this.width != 1 || this.height != 1 ) {
                    support = false;
                }
            }
            data.src = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==";
            return support;
        }) () ,

        // 检测是否已经安装flash，检测flash的版本
        flashVersion = (function () {
            var version;

            try {
                version = navigator.plugins['Shockwave Flash'];
                version = version.description;
            } catch ( ex ) {
                try {
                    version = new ActiveXObject ( 'ShockwaveFlash.ShockwaveFlash' )
                        .GetVariable ( '$version' );
                } catch ( ex2 ) {
                    version = '0.0';
                }
            }
            version = version.match ( /\d+/g );
            return parseFloat ( version[0] + '.' + version[1] , 10 );
        }) () ,

        supportTransition = (function () {
            var s = document.createElement ( 'p' ).style ,
                r = 'transition' in s ||
                    'WebkitTransition' in s ||
                    'MozTransition' in s ||
                    'msTransition' in s ||
                    'OTransition' in s;
            s = null;
            return r;
        }) () ,

        // WebUploader实例
        uploader;

    if ( !WebUploader.Uploader.support ( 'flash' ) && WebUploader.browser.ie ) {

        // flash 安装了但是版本过低。
        if ( flashVersion ) {
            (function ( container ) {
                window['expressinstallcallback'] = function ( state ) {
                    switch ( state ) {
                        case 'Download.Cancelled':
                            swal ( '您取消了更新！' , '' , 'error' ).then ( function () {
                            } );
                            break;

                        case 'Download.Failed':
                            swal ( '安装失败！' , '' , 'error' ).then ( function () {
                            } );
                            break;

                        default:
                            swal ( '安装已成功，请刷新页面！' , '' , 'error' ).then ( function () {
                            } );
                            break;
                    }
                    delete window['expressinstallcallback'];
                };

                var swf = './expressInstall.swf';
                // insert flash object
                var html = '<object type="application/' +
                    'x-shockwave-flash" data="' + swf + '" ';

                if ( WebUploader.browser.ie ) {
                    html += 'classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" ';
                }

                html += 'width="100%" height="100%" style="outline:0">' +
                    '<param name="movie" value="' + swf + '" />' +
                    '<param name="wmode" value="transparent" />' +
                    '<param name="allowscriptaccess" value="always" />' +
                    '</object>';

                container.html ( html );

            }) ( $wrap );

            // 压根就没有安转。
        } else {
            $wrap.html ( '<a href="http://www.adobe.com/go/getflashplayer" target="_blank" border="0"><img alt="get flash player" src="http://www.adobe.com/macromedia/style_guide/images/160x41_Get_Flash_Player.jpg" /></a>' );
        }

        return;
    } else if ( !WebUploader.Uploader.support () ) {
        swal ( 'Web Uploader 不支持您的浏览器！' , '' , 'error' ).then ( function () {
        } );
        return;
    }

    var options = {
        tokenUrl: window.chunk_file.prefix + '/' + "chunk-file-upload/get_qiniu_token" ,
        mockToken: false ,
        hash: false ,
        disk: disk
    };
    if ( driver == 'local' ) {//本地
        options.host = window.chunk_file.prefix + '/' + 'chunk-file-upload/upload';
    } else if ( driver = 'qiniu' ) {
        options.host = window.chunk_file.area;

    }


    var uploader = WebUploader.create ( {
        swf: "Uploader.swf" ,
        server: options.host ,
        pick: {
            id: '#filePicker' + name ,
            label: '点击选择文件'
        } ,
        duplicate: true ,
        resize: false ,
        dnd: "#dndArea" + name ,
        paste: document.body ,
        disableGlobalDnd: true ,
        thumb: {
            width: 100
            , height: 100
            , quality: 70
            , allowMagnify: true
            , crop: true
        } ,
        accept: accept ,
        compress: false ,
        prepareNextFile: true ,
        chunked: true ,
        chunkSize: 4 * 1024 * 1024 ,
        threads: 5 ,
        fileNumLimit: window.chunk_file.fileNumLimit ,
        fileSizeLimit: window.chunk_file.fileSizeLimit ,
        // duplicate: false
    } );



    var token;
    var m = new Map ();

    // 添加“添加文件”的按钮，
    if ( window.chunk_file.fileNumLimit <= 1 ) {
        uploader.addButton ( {
            id: '#filePicker2' + name ,
            label: '重新选择'
        } );
    } else {
        uploader.addButton ( {
            id: '#filePicker2' + name ,
            label: '继续选择'
        } );
    }


    //只能单传图片
    uploader.on ( "beforeFileQueued" , function ( file ) {
        if ( window.chunk_file.fileNumLimit <= 1 ) {//单传
            if ( $ ( 'ul.filelist li' ).length ) {
                var file_id = $ ( 'ul.filelist' ).children ( 'li' ).attr ( 'id' );
                uploader.removeFile ( file_id );
            }
        }

    } );
    //错误提示
    uploader.on ( "error" , function ( type , handler ) {
        switch ( type ) {
            case 'Q_EXCEED_NUM_LIMIT':
                swal ( '上传文件总数量不能超过' + uploader.options.fileNumLimit + '个' , '' , 'error' ).then ( function () {
                } );
                break;

            case 'Q_EXCEED_SIZE_LIMIT':
                swal ( '上传文件大小不能超过' + uploader.options.fileSizeLimit + "KB" , '' , 'error' ).then ( function () {
                } );
                break;

            case 'Q_TYPE_DENIED':
                swal ( '上传文件类型不被允许' , '' , 'error' ).then ( function () {
                } );
                break;
        }
    } );

    //当文件被加入队列以后触发。
    uploader.on ( "fileQueued" , function ( file ) {
        fileCount++;
        fileSize += file.size;

        var ctx = new Array ();
        m.set ( file.name , ctx );

        if ( fileCount === 1 ) {
            $placeHolder.addClass ( 'element-invisible' );
            $statusBar.show ();
        }

        addFile ( file );
        setState ( 'ready' );
        updateTotalProgress ();
    } );


    uploader.on ( "uploadStart" , function ( file ) {
        if ( driver == 'qiniu' ) {//七牛，传递七牛key
            if ( !options.mockToken ) {
                GetToken ( options.tokenUrl , file );
            } else {
                uploader.options.formData = {
                    token: options.mockTokenValue
                };
                token = options.mockTokenValue;
            }
        } else {//laravel传递laravelkey
            uploader.options.formData._token = $ ( 'meta[name="csrf-token"]' ).attr ( 'content' );
        }


        var name = Date.parse ( new Date () ) + Math.random ().toString ( 36 ).substr ( 2 ) + '.' + file.source.ext;
        uploader.options.formData.key = name;
        uploader.options.formData.disk = options.disk;
    } );


    uploader.on ( "uploadBeforeSend" , function ( block , data , headers ) {

        if ( parseInt ( block.file.size ) <= parseInt ( uploader.options.chunkSize ) ) {
            uploader.options.chunked = false;
        } else {
            uploader.options.chunked = true;
            headers['Authorization'] = 'UpToken ' + token;
            headers['Content-Type'] = 'application/octet-stream';
            block.transport.options.server = options.host + "/mkblk/" + (block.end - block.start);
            block.transport.options.sendAsBinary = true;
            block.transport.options.formData = false;
        }
    } );

    uploader.onUploadProgress = function ( file , percentage ) {
        var $li = $ ( '#' + file.id ) ,
            $percent = $li.find ( '.progress span' );

        $percent.css ( 'width' , percentage * 100 + '%' );
        percentages[file.id][1] = percentage;
        updateTotalProgress ();
    };

    uploader.on ( "uploadAccept" , function ( block , ret ) {
        //ctx[block.chunk] = ret.ctx;
        m.get ( block.file.name )[block.chunk] = ret.ctx;
    } );

    uploader.on ( "uploadSuccess" , function ( file , res ) {
        //成功之后，给li加上key值
        $ ( '#' + file.id ).attr ( 'dataSrc' , res.key );

        if ( parseInt ( file.size ) <= parseInt ( uploader.options.chunkSize ) ) {//大于就分片，小于就完毕
            UploadComplete ( file , res );
        } else {
            MakeFile ( m.get ( file.name ) , file , options.hash );
        }

    } );
    uploader.on ( 'all' , function ( type ) {
        var stats;
        switch ( type ) {
            case 'uploadFinished'://都结束了
                $ ( '.form-horizontal button[type="submit"]' ).attr ( 'disabled' , false ).css ( { "cursor": "pointer" } ).attr ( 'title' , '' );
                setState ( 'confirm' );
                break;

            case 'startUpload'://开始上传
                $ ( '.form-horizontal button[type="submit"]' ).attr ( 'disabled' , true ).css ( { "cursor": " no-drop" } ).attr ( 'title' , '上传文件中...' );
                setState ( 'uploading' );
                break;

            case 'stopUpload'://暂停上传
                $ ( '.form-horizontal button[type="submit"]' ).attr ( 'disabled' , true ).css ( { "cursor": " no-drop" } ).attr ( 'title' , '上传文件暂停...' );
                setState ( 'paused' );
                break;

        }
    } );

    uploader.on ( "fileDequeued" , function ( file , res ) {
        fileCount--;
        fileSize -= file.size;

        if ( !fileCount ) {
            setState ( 'pedding' );
        }

        removeFile ( file );
        updateTotalProgress ();

    } );


    $upload.on ( 'click' , function () {
        if ( $ ( this ).hasClass ( 'disabled' ) ) {
            return false;
        }


        if ( state === 'ready' ) {
            uploader.upload ();
        } else if ( state === 'paused' ) {
            uploader.upload ();
        } else if ( state === 'uploading' ) {
            uploader.stop ( true );
        }
    } );

    $info.on ( 'click' , '.retry' , function () {
        uploader.retry ();
    } );

    $info.on ( 'click' , '.ignore' , function () {
        swal ( '已忽略' , '' , 'error' ).then ( function () {
        } );
    } );

    $upload.addClass ( 'state-' + state );

    function GetToken ( tokenUrl , file ) {
        $.ajax ( {
            async: false ,
            type: 'get' ,
            url: tokenUrl ,
            dataType: 'json' ,
            data: {
                disk: disk
            } ,
            success: function ( res ) {
                if ( res.code ) {
                    token = res.uptoken;
                    if ( options.hash ) {
                        uploader.options.formData = {
                            token: token ,
                        }
                    } else {
                        uploader.options.formData = {
                            token: token ,
                            key: file.name
                        }
                    }
                } else {
                    swal ( res.msg , '' , 'error' ).then ( function () {
                    } );
                    throw '获取token错误';
                }
            }
        } );
    }

    function MakeFile ( ctx , file , hash ) {
        var b = ctx.join ( "," );

        if ( hash ) {//key固定
            $.ajax ( {
                headers: {
                    'X-CSRF-TOKEN': $ ( 'meta[name="csrf-token"]' ).attr ( 'content' ) ,
                    'file-id': file.id ,
                    'file-ext': file.source.ext ,
                    'disk': disk
                } ,
                type: 'POST' ,
                url: options.host + '/mkfile/' + file.size ,
                data: b ,
                contentType: "text/plain" ,
                contentLength: b.length ,
                beforeSend: function ( XMLHttpRequest ) {
                    XMLHttpRequest.setRequestHeader ( "Authorization" , 'UpToken ' + token );

                } ,
                success: function ( res ) {
                    UploadComplete ( file , res );
                }
            } );
        } else {//key不固定
            $.ajax ( {
                headers: {
                    'X-CSRF-TOKEN': $ ( 'meta[name="csrf-token"]' ).attr ( 'content' ) ,
                    'file-id': file.id ,
                    'file-ext': file.source.ext ,
                    'disk': disk
                } ,
                type: 'POST' ,
                url: options.host + '/mkfile/' + file.size + '/key/' + URLSafeBase64Encode ( uploader.options.formData.key ) ,
                data: b ,
                contentType: "text/plain" ,
                contentLength: b.length ,
                beforeSend: function ( XMLHttpRequest ) {
                    XMLHttpRequest.setRequestHeader ( "Authorization" , 'UpToken ' + token );

                } ,
                success: function ( res ) {
                    UploadComplete ( file , res );
                }
            } );
        }
    }

    function UploadComplete ( file , res ) {
        if ( window.chunk_file.fileNumLimit <= 1 ) {
            $ ( '#' + name + '-savedpath' ).val ( window.chunk_file.url + '/' + res.key );
        } else {
            if ( window.chunk_file.saveType == 'json' ) {//为json类型

                if ( initVal ) {//如果初始有数据
                    $ ( '#' + name + '-savedpath' ).val ( '' );
                    initVal = false;//然后置false
                }
                //先拿出来
                var data = $ ( '#' + name + '-savedpath' ).val ();
                if ( !data ) {//为空
                    data = new Array ();
                    data.push ( res.key );

                } else {
                    try {
                        data = JSON.parse ( data );
                    } catch ( e ) {
                        data = new Array ();
                    }

                    data.push ( res.key );
                }
                $ ( '#' + name + '-savedpath' ).val ( JSON.stringify ( data ) );

            }

        }

        ctx = new Array ();
        uploader.options.chunked = true;
    }

    function URLSafeBase64Decode ( data ) {
        data = data.replace ( /_/g , '/' ).replace ( /-/g , '+' );
        var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
        var o1 , o2 , o3 , h1 , h2 , h3 , h4 , bits , i = 0 ,
            ac = 0 ,
            dec = "" ,
            tmp_arr = [];

        if ( !data ) {
            return data;
        }

        data += '';

        do { // unpack four hexets into three octets using index points in b64
            h1 = b64.indexOf ( data.charAt ( i++ ) );
            h2 = b64.indexOf ( data.charAt ( i++ ) );
            h3 = b64.indexOf ( data.charAt ( i++ ) );
            h4 = b64.indexOf ( data.charAt ( i++ ) );

            bits = h1 << 18 | h2 << 12 | h3 << 6 | h4;

            o1 = bits >> 16 & 0xff;
            o2 = bits >> 8 & 0xff;
            o3 = bits & 0xff;

            if ( h3 === 64 ) {
                tmp_arr[ac++] = String.fromCharCode ( o1 );
            } else if ( h4 === 64 ) {
                tmp_arr[ac++] = String.fromCharCode ( o1 , o2 );
            } else {
                tmp_arr[ac++] = String.fromCharCode ( o1 , o2 , o3 );
            }
        } while ( i < data.length );

        dec = tmp_arr.join ( '' );

        return dec;
    }

    function utf8_encode ( argString ) {

        if ( argString === null || typeof argString === 'undefined' ) {
            return '';
        }

        var string = (argString + ''); // .replace(/\r\n/g, '\n').replace(/\r/g, '\n');
        var utftext = '' ,
            start , end , stringl = 0;

        start = end = 0;
        stringl = string.length;
        for ( var n = 0 ; n < stringl ; n++ ) {
            var c1 = string.charCodeAt ( n );
            var enc = null;

            if ( c1 < 128 ) {
                end++;
            } else if ( c1 > 127 && c1 < 2048 ) {
                enc = String.fromCharCode (
                    (c1 >> 6) | 192 , (c1 & 63) | 128
                );
            } else if ( c1 & 0xF800 ^ 0xD800 > 0 ) {
                enc = String.fromCharCode (
                    (c1 >> 12) | 224 , ((c1 >> 6) & 63) | 128 , (c1 & 63) | 128
                );
            } else { // surrogate pairs
                if ( c1 & 0xFC00 ^ 0xD800 > 0 ) {
                    throw new RangeError ( 'Unmatched trail surrogate at ' + n );
                }
                var c2 = string.charCodeAt ( ++n );
                if ( c2 & 0xFC00 ^ 0xDC00 > 0 ) {
                    throw new RangeError ( 'Unmatched lead surrogate at ' + (n - 1) );
                }
                c1 = ((c1 & 0x3FF) << 10) + (c2 & 0x3FF) + 0x10000;
                enc = String.fromCharCode (
                    (c1 >> 18) | 240 , ((c1 >> 12) & 63) | 128 , ((c1 >> 6) & 63) | 128 , (c1 & 63) | 128
                );
            }
            if ( enc !== null ) {
                if ( end > start ) {
                    utftext += string.slice ( start , end );
                }
                utftext += enc;
                start = end = n + 1;
            }
        }

        if ( end > start ) {
            utftext += string.slice ( start , stringl );
        }

        return utftext;
    }

    function URLSafeBase64Encode ( data ) {
        var b64 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';
        var o1 , o2 , o3 , h1 , h2 , h3 , h4 , bits , i = 0 ,
            ac = 0 ,
            enc = '' ,
            tmp_arr = [];

        if ( !data ) {
            return data;
        }

        data = utf8_encode ( data + '' );

        do { // pack three octets into four hexets
            o1 = data.charCodeAt ( i++ );
            o2 = data.charCodeAt ( i++ );
            o3 = data.charCodeAt ( i++ );

            bits = o1 << 16 | o2 << 8 | o3;

            h1 = bits >> 18 & 0x3f;
            h2 = bits >> 12 & 0x3f;
            h3 = bits >> 6 & 0x3f;
            h4 = bits & 0x3f;

            // use hexets to index into b64, and append result to encoded string
            tmp_arr[ac++] = b64.charAt ( h1 ) + b64.charAt ( h2 ) + b64.charAt ( h3 ) + b64.charAt ( h4 );
        } while ( i < data.length );

        enc = tmp_arr.join ( '' );

        switch ( data.length % 3 ) {
            case 1:
                enc = enc.slice ( 0 , -2 ) + '==';
                break;
            case 2:
                enc = enc.slice ( 0 , -1 ) + '=';
                break;
        }

        return enc.replace ( /\//g , '_' ).replace ( /\+/g , '-' );
    }


    // 当有文件添加进来时执行，负责view的创建
    function addFile ( file ) {
        var $li = $ ( '<li id="' + file.id + '">' +
            '<p class="title">' + file.name + '</p>' +
            '<p class="imgWrap"></p>' +
            '<p class="progress"><span></span></p>' +
            '</li>' ) ,

            $btns = $ ( '<div class="file-panel">' +
                '<span class="cancel">删除</span>' +
                '<span class="rotateRight">向右旋转</span>' +
                '<span class="rotateLeft">向左旋转</span></div>' ).appendTo ( $li ) ,
            $prgress = $li.find ( 'p.progress span' ) ,
            $wrap = $li.find ( 'p.imgWrap' ) ,
            $info = $ ( '<p class="error"></p>' ) ,

            showError = function ( code ) {
                switch ( code ) {
                    case 'exceed_size':
                        text = '文件大小超出';
                        break;

                    case 'interrupt':
                        text = '上传暂停';
                        break;

                    default:
                        text = '上传失败，请重试';
                        break;
                }

                $info.text ( text ).appendTo ( $li );
            };

        if ( file.getStatus () === 'invalid' ) {
            showError ( file.statusText );
        } else {
            // @todo lazyload
            $wrap.text ( '预览中' );
            uploader.makeThumb ( file , function ( error , src ) {
                var img;

                if ( error ) {
                    $wrap.text ( '不能预览' );
                    return;
                }

                if ( isSupportBase64 ) {
                    img = $ ( '<img src="' + src + '">' );
                    $wrap.empty ().append ( img );
                } else {
                    $wrap.text ( "预览出错" );
                }
            } , thumbnailWidth , thumbnailHeight );

            percentages[file.id] = [file.size , 0];
            file.rotation = 0;
        }

        file.on ( 'statuschange' , function ( cur , prev ) {
            if ( prev === 'progress' ) {
                $prgress.hide ().width ( 0 );
            } else if ( prev === 'queued' ) {
                // $li.off ( 'mouseenter mouseleave' );
                // $btns.remove ();//暂时不删除

            }

            // 成功
            if ( cur === 'error' || cur === 'invalid' ) {
                showError ( file.statusText );
                percentages[file.id][1] = 1;
            } else if ( cur === 'interrupt' ) {
                showError ( 'interrupt' );
            } else if ( cur === 'queued' ) {
                percentages[file.id][1] = 0;
            } else if ( cur === 'progress' ) {
                $info.remove ();
                $prgress.css ( 'display' , 'block' );
            } else if ( cur === 'complete' ) {
                //这是成功了
                $li.append ( '<span class="success"></span>' );
            }

            $li.removeClass ( 'state-' + prev ).addClass ( 'state-' + cur );
        } );

        $li.on ( 'mouseenter' , function () {
            $btns.stop ().animate ( { height: 30 } );
        } );

        $li.on ( 'mouseleave' , function () {
            $btns.stop ().animate ( { height: 0 } );
        } );

        $btns.on ( 'click' , 'span' , function () {
            var index = $ ( this ).index () ,
                deg;

            switch ( index ) {
                case 0:
                    uploader.removeFile ( file );
                    return;

                case 1:
                    file.rotation += 90;
                    break;

                case 2:
                    file.rotation -= 90;
                    break;
            }

            if ( supportTransition ) {
                deg = 'rotate(' + file.rotation + 'deg)';
                $wrap.css ( {
                    '-webkit-transform': deg ,
                    '-mos-transform': deg ,
                    '-o-transform': deg ,
                    'transform': deg
                } );
            } else {
                $wrap.css ( 'filter' , 'progid:DXImageTransform.Microsoft.BasicImage(rotation=' + (~~((file.rotation / 90) % 4 + 4) % 4) + ')' );
            }


        } );

        $li.appendTo ( $queue );
    }

    // 负责view的销毁
    function removeFile ( file ) {
        var $li = $ ( '#' + file.id );

        delete percentages[file.id];
        updateTotalProgress ();
        $li.off ().find ( '.file-panel' ).off ().end ().remove ();

        //然后删除数组文件
        var dataSrc = $li.attr ( 'dataSrc' );
        var data = $ ( '#' + name + '-savedpath' ).val ();
        if ( data ) {//不为空
            try {
                data = JSON.parse ( data );
            } catch ( e ) {
                data = new Array ();
            }
            for ( var i = 0 ; i < data.length ; i++ ) {
                if ( dataSrc == data[i] ) {
                    data.splice ( i , 1 );
                }
            }
            if ( !data.length ) {
                data = '';
            } else {
                data = JSON.stringify ( data );
            }
            $ ( '#' + name + '-savedpath' ).val ( data );
        }
    }

    function setState ( val ) {
        var file , stats;

        if ( val === state ) {
            return;
        }

        $upload.removeClass ( 'state-' + state );
        $upload.addClass ( 'state-' + val );
        state = val;

        switch ( state ) {
            case 'pedding':
                $placeHolder.removeClass ( 'element-invisible' );
                $queue.hide ();
                $statusBar.addClass ( 'element-invisible' );
                uploader.refresh ();
                break;

            case 'ready':
                $placeHolder.addClass ( 'element-invisible' );
                $ ( '#filePicker2' + name ).removeClass ( 'element-invisible' );
                $queue.show ();
                $statusBar.removeClass ( 'element-invisible' );
                uploader.refresh ();
                break;

            case 'uploading':
                $ ( '#filePicker2' + name ).addClass ( 'element-invisible' );
                $progress.show ();
                $upload.text ( '暂停上传' );
                break;

            case 'paused':
                $progress.show ();
                $upload.text ( '继续上传' );
                break;

            case 'confirm':
                $progress.hide ();
                $ ( '#filePicker2' + name ).removeClass ( 'element-invisible' );
                $upload.text ( '开始上传' );

                stats = uploader.getStats ();
                if ( stats.successNum && !stats.uploadFailNum ) {
                    setState ( 'finish' );
                    return;
                }
                break;
            case 'finish':
                stats = uploader.getStats ();
                if ( stats.successNum ) {
                    swal ( '上传成功' , '' , 'success' ).then ( function () {
                    } );
                } else {
                    // 没有成功的图片，重设
                    state = 'done';
                    location.reload ();
                }
                break;
        }


        updateStatus ();
    }

    function updateStatus () {
        var text = '' , stats;

        if ( state === 'ready' ) {
            text = '选中' + fileCount + '张图片，共' +
                WebUploader.formatSize ( fileSize ) + '。';
        } else if ( state === 'confirm' ) {
            stats = uploader.getStats ();
            if ( stats.uploadFailNum ) {
                text = '已成功上传' + stats.successNum + '张照片至XX相册，' +
                    stats.uploadFailNum + '张照片上传失败，<a class="retry" href="#">重新上传</a>失败图片或<a class="ignore" href="#">忽略</a>'
            }

        } else {
            stats = uploader.getStats ();
            text = '共' + fileCount + '张（' +
                WebUploader.formatSize ( fileSize ) +
                '），已上传' + stats.successNum + '张';

            if ( stats.uploadFailNum ) {
                text += '，失败' + stats.uploadFailNum + '张';
            }
        }

        $info.html ( text );
    }

    function updateTotalProgress () {
        var loaded = 0 ,
            total = 0 ,
            spans = $progress.children () ,
            percent;

        $.each ( percentages , function ( k , v ) {
            total += v[0];
            loaded += v[0] * v[1];
        } );

        percent = total ? loaded / total : 0;


        spans.eq ( 0 ).text ( Math.round ( percent * 100 ) + '%' );
        spans.eq ( 1 ).css ( 'width' , Math.round ( percent * 100 ) + '%' );
        updateStatus ();
    }

    $ ( '#' + name + '-savedpath' ).css ( 'cursor' , 'pointer' )
    $ ( document ).on ( 'click' , '#' + name + '-savedpath' , function () {
        window.open ( $ ( this ).val () );
    } );
    return uploader;

}
