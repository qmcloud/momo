/**************************************************************************
***                             Easemob WebIm Js SDK                    ***
***                             v1.1.1                                  ***
**************************************************************************/
/**
 * Module1: Utility
 * Module2: EmMessage
 * Module3: Message
 * Module4: Connection
 */

;(function ( window, undefined ) {

    if ( typeof Strophe === 'undefined' ) {
        throw 'need Strophe';
    }

    var Easemob = Easemob || {};
    Easemob.im = Easemob.im || {};
    Easemob.im.version = "1.1.1";

    var https = location.protocol === 'https:';

    window.URL = window.URL || window.webkitURL || window.mozURL || window.msURL;

	Strophe.Websocket.prototype._closeSocket = function () {
		var me = this;
        if ( me.socket ) {
			setTimeout(function () {
				try {
					me.socket.close();
				} catch ( e ) {}
			}, 0);
		} else {
			me.socket = null;
		}
	}

    /**
     * Module1: Utility
     */
    var Utils = (function () {
        
        var _createStandardXHR = function () {
            try {
                return new window.XMLHttpRequest();
            } catch ( e ) {
                return false;
            }
        };
        
        var _createActiveXHR = function () {
            try {
                return new window.ActiveXObject( "Microsoft.XMLHTTP" );
            } catch ( e ) {
                return false;
            }
        };

        if ( window.XDomainRequest ) {
            XDomainRequest.prototype.oldsend = XDomainRequest.prototype.send;
            XDomainRequest.prototype.send = function () {
                XDomainRequest.prototype.oldsend.apply(this, arguments);
                this.readyState = 2;
            };
        }

        Strophe.Request.prototype._newXHR = function () {
            var xhr =  Utils.xmlrequest(true);
            if ( xhr.overrideMimeType ) {
                xhr.overrideMimeType("text/xml");
            }
            xhr.onreadystatechange = this.func.stropheBind(null, this);
            return xhr;
        };

		var _xmlrequest = function ( crossDomain ) {
			crossDomain = crossDomain || true;
			var temp = _createStandardXHR () || _createActiveXHR();

			if ( "withCredentials" in temp ) {
				return temp;
			}
			if ( !crossDomain ) {
				return temp;
			}
			if ( typeof window.XDomainRequest === 'undefined' ) {
				return temp;
			}
			var xhr = new XDomainRequest();
			xhr.readyState = 0;
			xhr.status = 100;
			xhr.onreadystatechange = EMPTYFN;
			xhr.onload = function () {
				xhr.readyState = 4;
				xhr.status = 200;

				var xmlDoc = new ActiveXObject("Microsoft.XMLDOM");
				xmlDoc.async = "false";
				xmlDoc.loadXML(xhr.responseText);
				xhr.responseXML = xmlDoc;
				xhr.response = xhr.responseText;
				xhr.onreadystatechange();
			};
			xhr.ontimeout = xhr.onerror = function () {
				xhr.readyState = 4;
				xhr.status = 500;
				xhr.onreadystatechange();
			};
			return xhr;
		};

		var _hasFlash = (function () {
			if ( 'ActiveXObject' in window ) {
				try {
					return new ActiveXObject('ShockwaveFlash.ShockwaveFlash');
				} catch ( ex ) {
					return 0;
				}
			} else {
				if ( navigator.plugins && navigator.plugins.length > 0 ) {
					return navigator.plugins["Shockwave Flash"];
				}
			}
			return 0;
		}());

		var _tmpUtilXHR  = _xmlrequest(),
			_hasFormData = typeof FormData !== 'undefined',
            _hasBlob = typeof Blob !== 'undefined',
			_isCanSetRequestHeader = _tmpUtilXHR.setRequestHeader || false,
			_hasOverrideMimeType = _tmpUtilXHR.overrideMimeType || false,
			_isCanUploadFileAsync = _isCanSetRequestHeader && _hasFormData,
			_isCanUploadFile = _isCanUploadFileAsync || _hasFlash,
            _isCanDownLoadFile = _isCanSetRequestHeader && (_hasBlob || _hasOverrideMimeType);

        return {
            hasFormData: _hasFormData

            , hasBlob: _hasBlob

            , isCanSetRequestHeader: _isCanSetRequestHeader

            , hasOverrideMimeType: _hasOverrideMimeType

            , isCanUploadFileAsync: _isCanUploadFileAsync

            , isCanUploadFile: _isCanUploadFile

            , isCanDownLoadFile: _isCanDownLoadFile

			, isSupportWss: (function () {
				var notSupportList = [
					//1:qq broswser X5 core
					/MQQBrowser[\/]5([.]\d+)?\sTBS/

					//2:etc.
					//...
				];

				if ( !window.WebSocket ) {
					return false;
				}

				var ua = window.navigator.userAgent;
				for ( var i = 0, l = notSupportList.length; i < l; i++ ) {
					if ( notSupportList[i].test(ua) ) {
						return false;
					}
				}
				return true; 
			}())

            , stringify: function ( json ) {
                if ( typeof JSON !== 'undefined' && JSON.stringify ) {
                    return JSON.stringify(json);
                } else {
                    var s = '',
                        arr = [];

                    var iterate = function ( json ) {
                        var isArr = false;

                        if ( Object.prototype.toString.call(json) === '[object Array]' ) {
                            arr.push(']', '[');
                            isArr = true;
                        } else if ( Object.prototype.toString.call(json) === '[object Object]' ) {
                            arr.push('}', '{');
                        }

                        for ( var o in json ) {
                            if ( Object.prototype.toString.call(json[o]) === '[object Null]' ) {
                                json[o] = 'null';
                            } else if ( Object.prototype.toString.call(json[o]) === '[object Undefined]' ) {
                                json[o] = 'undefined';
                            }

                            if ( json[o] && typeof json[o] === 'object' ) {
                                s += ',' + (isArr ? '' : '"' + o + '":' + (isArr ? '"' : '')) + iterate(json[o]) + '';
                            } else {
                                s += ',"' + (isArr ? '' : o + '":"') + json[o] + '"';
                            }
                        }
                
                        if ( s != '' ) {
                            s = s.slice(1);
                        }

                        return arr.pop() + s + arr.pop();
                    }
                    return iterate(json);
                }
            }

            , registerUser: function ( options ) {
                var orgName = options.orgName || '';
                var appName = options.appName || '';
                var appKey = options.appKey || '';

                if ( !orgName && !appName && appKey ) {
                    var devInfos = appKey.split('#');
                    if ( devInfos.length === 2 ) {
                        orgName = devInfos[0];
                        appName = devInfos[1];
                    }
                }
                if ( !orgName && !appName ) {
                    options.error({
                        type: EASEMOB_IM_RESISTERUSER_ERROR
                        , msg: '没有指定开发者信息'
                    });
                    return;
                }

				var https = options.https || https;
                var apiUrl = options.apiUrl || (https ? 'https' : 'http') + '://a1.easemob.com';
                var restUrl = apiUrl + '/' + orgName + '/' + appName + '/users';

                var userjson = {
                    username: options.username
                    , password: options.password
                    , nickname: options.nickname || ''
                };

                var userinfo = Utils.stringify(userjson);
                var options = {
                    url: restUrl
                    , dataType: 'json'
                    , data: userinfo
                    , success: options.success || EMPTYFN
                    , error: options.error || EMPTYFN
                };
                return Utils.ajax(options);
            }

            , login2UserGrid: function ( options ) {
                options = options || {};

                var appKey = options.appKey || '';
                var devInfos = appKey.split('#');
                if ( devInfos.length !== 2 ) {
                    error({
                        type: EASEMOB_IM_CONNCTION_OPEN_USERGRID_ERROR
                        , msg: '请指定正确的开发者信息(appKey)'
                    });
                    return false;
                }

                var orgName = devInfos[0];
                var appName = devInfos[1];
                if ( !orgName ) {
                    error({
                        type: EASEMOB_IM_CONNCTION_OPEN_USERGRID_ERROR
                        , msg: '请指定正确的开发者信息(appKey)'
                    });
                    return false;
                }
                if ( !appName ) {
                    error({
                        type: EASEMOB_IM_CONNCTION_OPEN_USERGRID_ERROR
                        , msg: '请指定正确的开发者信息(appKey)'
                    });
                    return false;
                }

				var https = https || options.https;
                var suc = options.success || EMPTYFN;
                var error = options.error || EMPTYFN;
                var user = options.user || '';
                var pwd = options.pwd || '';

                var apiUrl = options.apiUrl || (https ? 'https' : 'http') + '://a1.easemob.com';

                var loginJson = {
                    grant_type: 'password'
                    , username: user
                    , password: pwd
                };
                var loginfo = Utils.stringify(loginJson);

                var options = {
                    url: apiUrl + "/" + orgName + "/" + appName + "/token"
                    , dataType: 'json'
                    , data: loginfo
                    , success: suc || EMPTYFN
                    , error: error || EMPTYFN
                };
                return Utils.ajax(options);
            }
            , getFileUrl: function ( fileInputId ) {
                var uri = {
                    url: ''
                    , filename: ''
                    , filetype: ''
                    , data: ''
                };

				var fileObj = document.getElementById(fileInputId);

                if ( !Utils.isCanUploadFileAsync || !fileObj ) {
                    return uri;
                }

                if ( window.URL.createObjectURL ) {
                    var fileItems = fileObj.files;
                    if (fileItems.length > 0) {
                        var u = fileItems.item(0);
                        uri.data = u;
                        uri.url = window.URL.createObjectURL(u);
                        uri.filename = u.name || '';
                    }
                } else { // IE
                    var u = document.getElementById(fileInputId).value;
                    uri.url = u;
                    var pos1 = u.lastIndexOf('/');
                    var pos2 = u.lastIndexOf('\\');
                    var pos = Math.max(pos1, pos2)
                    if (pos < 0)
                        uri.filename = u;
                    else
                        uri.filename = u.substring(pos + 1);
                }
                var index = uri.filename.lastIndexOf(".");
                if ( index != -1 ) {
                    uri.filetype = uri.filename.substring(index+1).toLowerCase();
                }
                return uri;
            }

            , getFileSizeFn: function ( fileInputId ) {
                var file = document.getElementById(fileInputId)
                var fileSize = 0;
                if ( file ) {
                    if ( file.files ) {
                        if ( file.files.length > 0 ) {
                            fileSize = file.files[0].size;
                        }
                    } else if ( file.select && 'ActiveXObject' in window ) {
                        file.select();
                        var fileobject = new ActiveXObject ("Scripting.FileSystemObject");
                        var file = fileobject.GetFile (file.value);
                        fileSize = file.Size;
                    }
                }
                return fileSize;
            }

            , hasFlash: _hasFlash

            , trim: function ( str ) {

                str = typeof str === 'string' ? str : '';

                return str.trim
                    ? str.trim()
                    : str.replace(/^\s|\s$/g, '');
            }

            , parseEmotions: function ( msg ) {
                if ( typeof Easemob.im.EMOTIONS === 'undefined' || typeof Easemob.im.EMOTIONS.map === 'undefined' ) {
                    return msg;
                } else {
                    var emotion = Easemob.im.EMOTIONS,
                        reg = null;

                    msg = msg.replace(/&amp;/g, '&');
                    msg = msg.replace(/&#39;/g, '\'');
                    msg = msg.replace(/&lt;/g, '<');

                    for ( var face in emotion.map ) {
                        if ( emotion.map.hasOwnProperty(face) ) {
                            while ( msg.indexOf(face) > -1 ) {
                                msg = msg.replace(face, '<img class="em-emotion" src="' + emotion.path + emotion.map[face] + '" alt="表情">');
                            }
                        }
                    }
                    return msg;
                }
            }

            , parseLink: function ( msg ) {
                var reg = /(https?\:\/\/|www\.)([a-zA-Z0-9-]+(\.[a-zA-Z0-9]+)+)(\:[0-9]{2,4})?\/?((\.[:_0-9a-zA-Z-]+)|[:_0-9a-zA-Z-]*\/?)*\??[:_#@*&%0-9a-zA-Z-/=]*/gm;
                var res = msg.match(reg);
                var src = res && res[0] ? res[0] : ''; 
                if ( res && res.length ) {
                    var prefix = /^https?:\/\//.test(src);
                    msg = msg.replace(reg
                        , "<a href='" 
                            + (prefix 
                                ? src 
                                : '\/\/' + src) 
                            + "' target='_blank'>" 
                            + src 
                            + "</a>");
                }
                return msg;
            }

            , parseJSON: function ( data ) {

                if ( window.JSON && window.JSON.parse ) {
                    return window.JSON.parse(data + "");
                }

                var requireNonComma,
                    depth = null,
                    str = Utils.trim(data + "");

                return str && !Utils.trim(
                    str.replace(/(,)|(\[|{)|(}|])|"(?:[^"\\\r\n]|\\["\\\/bfnrt]|\\u[\da-fA-F]{4})*"\s*:?|true|false|null|-?(?!0\d)\d+(?:\.\d+|)(?:[eE][+-]?\d+|)/g
                    , function ( token, comma, open, close ) {

                        if ( requireNonComma && comma ) {
                            depth = 0;
                        }

                        if ( depth === 0 ) {
                            return token;
                        }

                        requireNonComma = open || comma;
                        depth += !close - !open;
                        return "";
                    })
                )
                ? (Function("return " + str))()
                : (Function("Invalid JSON: " + data))();
            }
            
            , parseUploadResponse: function ( response ) {
                return response.indexOf('callback') > -1 ? //lte ie9
                    response.slice(9, -1) : response;
            }
            
            , parseDownloadResponse: function ( response ) {
                return ((response && response.type && response.type === 'application/json') 
                    || 0 > Object.prototype.toString.call(response).indexOf('Blob')) ? 
                        this.url+'?token=' : window.URL.createObjectURL(response);
            }
            , uploadFile: function ( options ) {
                options = options || {};
                options.onFileUploadProgress = options.onFileUploadProgress || EMPTYFN;
                options.onFileUploadComplete = options.onFileUploadComplete || EMPTYFN;
                options.onFileUploadError = options.onFileUploadError || EMPTYFN;
                options.onFileUploadCanceled = options.onFileUploadCanceled || EMPTYFN;
                var acc = options.accessToken || this.context.accessToken;
                if (!acc) {
                    options.onFileUploadError({
                        type: EASEMOB_IM_UPLOADFILE_NO_LOGIN
                        , msg: '用户未登录到usergrid服务器,无法使用文件上传功能'
                        , id: options.id
                    });
                    return;
                }

                orgName = options.orgName || this.context.orgName || '';
                appName = options.appName || this.context.appName || '';
                appKey = options.appKey || this.context.appKey || '';
                if(!orgName && !appName && appKey){
                    var devInfos = appKey.split('#');
                    if(devInfos.length==2){
                        orgName = devInfos[0];
                        appName = devInfos[1];
                    }
                }
                if ( !orgName && !appName ) {
                    options.onFileUploadError({
                        type: EASEMOB_IM_UPLOADFILE_ERROR
                        , msg: '没有指定开发者信息'
                        , id: options.id
                    });
                    return;
                }

                var apiUrl = options.apiUrl || 'http://a1.easemob.com';
                var uploadUrl = apiUrl + '/' + orgName + '/' + appName + '/chatfiles';

                if ( !Utils.isCanUploadFileAsync ) {
                    if ( Utils.hasFlash && typeof options.flashUpload === 'function' ) {
                        options.flashUpload && options.flashUpload(uploadUrl, options); 
                    } else {
                        this.onError({
                            type : EASEMOB_IM_UPLOADFILE_BROWSER_ERROR
                            , msg : '当前浏览器不支持异步上传！'
                        });
                    }
                    return;
                }

                var fileSize = options.file.data ? options.file.data.size : undefined;
                if ( fileSize > EASEMOB_IM_FILESIZE_LIMIT ) {
                    options.onFileUploadError({
                        type: EASEMOB_IM_UPLOADFILE_ERROR
                        , msg: '上传文件超过服务器大小限制（10M）'
                        , id: options.id
                    });
                    return ;
                } else if ( fileSize <= 0 ) {
                    options.onFileUploadError({
                        type: EASEMOB_IM_UPLOADFILE_ERROR
                        , msg: '上传文件大小为0'
                        , id: options.id
                    });
                    return ;
                }

                var xhr = Utils.xmlrequest();
                var onError = function ( e ) {
                    options.onFileUploadError({
                        type: EASEMOB_IM_UPLOADFILE_ERROR
                        , msg: '上传文件失败'
                        , id: options.id
                        , xhr: xhr
                    });
                }
                if ( xhr.upload ) {
                    xhr.upload.addEventListener("progress",options.onFileUploadProgress, false);
                }
                if ( xhr.addEventListener ) {
                    xhr.addEventListener("abort", options.onFileUploadCanceled, false);
                    xhr.addEventListener("load", function ( e ) {
                        try {
                            var json = Utils.parseJSON(xhr.responseText);
                            options.onFileUploadComplete(json);
                        } catch ( e ) {
                            options.onFileUploadError({
                                type: EASEMOB_IM_UPLOADFILE_ERROR
                                , msg: '上传文件失败,服务端返回值值不正确'
                                , data: xhr.responseText
                                , id: options.id
                                , xhr: xhr
                            });
                        }
                    }, false);
                    xhr.addEventListener("error", onError, false);
                } else if ( xhr.onreadystatechange ) {
                    xhr.onreadystatechange = function () {
                        if ( xhr.readyState === 4 ) {
                            if ( ajax.status === 200 ) {
                                try {
                                    var json = Utils.parseJSON(xhr.responseText);
                                    options.onFileUploadComplete(json);
                                } catch ( e ) {
                                    options.onFileUploadError({
                                        type: EASEMOB_IM_UPLOADFILE_ERROR
                                        , msg: '上传文件失败,服务端返回值不正确'
                                        , data: xhr.responseText
                                        , id: options.id
                                        , xhr: xhr
                                    });
                                }
                            } else {
                                options.onFileUploadError({
									type: EASEMOB_IM_UPLOADFILE_ERROR
									, msg: '上传文件失败,服务端返回异常'
									, data: xhr.responseText
									, id: options.id
									, xhr: xhr
								});
                            }
                        } else {
                            xhr.abort();
                            options.onFileUploadCanceled();
                        }
                    }
                }

                xhr.open("POST", uploadUrl);

                xhr.setRequestHeader('restrict-access', 'true');
                xhr.setRequestHeader('Accept', '*/*');//android qq browser has some problem at this attr
                xhr.setRequestHeader('Authorization', 'Bearer ' + acc);

                var formData = new FormData();
                formData.append("file", options.file.data);
                xhr.send(formData);
            }

            , downloadFn: function ( options ) {
                options.onFileDownloadComplete = options.onFileDownloadComplete || EMPTYFN;
                options.onFileDownloadError = options.onFileDownloadError || EMPTYFN;
                
                var accessToken = options.accessToken || '';
                if ( !accessToken ) {
                    options.onFileDownloadError({
                        type: EASEMOB_IM_DOWNLOADFILE_NO_LOGIN
                        , msg: '用户未登录到usergrid服务器,无法使用文件下载功能'
                        , id: options.id
                    });
                    return;
                }

                var onError = function ( e ) {
                    options.onFileDownloadError({
                        type: EASEMOB_IM_DOWNLOADFILE_ERROR
                        , msg: '下载文件失败'
                        , id: options.id
                        , xhr: xhr
                    });
                }
                if ( !Utils.isCanDownLoadFile ) {
                    options.onFileDownloadComplete();
                    return;
                }
                var xhr = Utils.xmlrequest();
                if ( "addEventListener" in xhr ) {
                    xhr.addEventListener("load", function ( e ) {
                        options.onFileDownloadComplete(xhr.response,xhr);
                    }, false);
                    xhr.addEventListener("error", onError, false);
                } else if ( "onreadystatechange" in xhr ) {
                    xhr.onreadystatechange = function () {
                        if ( xhr.readyState === 4 ) {
                            if ( ajax.status === 200 ) {
                                options.onFileDownloadComplete(xhr.response,xhr);
                            } else {
								options.onFileDownloadError({
									type: EASEMOB_IM_DOWNLOADFILE_ERROR
									, msg: '下载文件失败,服务端返回异常'
									, id: options.id
									, xhr: xhr
								});
                            }
                        } else {
                            xhr.abort();
                            options.onFileDownloadError({
                                type: EASEMOB_IM_DOWNLOADFILE_ERROR
                                , msg: '错误的下载状态,退出下载'
                                , id: options.id
                                , xhr: xhr
                            });
                        }
                    }
                }

                var method = options.method || 'GET';
                var resType = options.responseType || 'blob';
                var mimeType = options.mimeType || "text/plain; charset=x-user-defined";
                xhr.open(method, options.url);
                if ( typeof Blob !== 'undefined' ) {
                    xhr.responseType = resType;
                } else {
                    xhr.overrideMimeType(mimeType);
                }

                var innerHeaer = {
                    'X-Requested-With': 'XMLHttpRequest'
                    , 'Accept': 'application/octet-stream'
                    , 'share-secret': options.secret
                    , 'Authorization': 'Bearer ' + accessToken
                };
                var headers = options.headers || {};
                for ( var key in headers ) {
                    innerHeaer[key] = headers[key];
                }
                for ( var key in innerHeaer ) {
                    if ( innerHeaer[key] ) {
                        xhr.setRequestHeader(key, innerHeaer[key]);
                    }
                }
                xhr.send(null);
            }

            , parseTextMessage: function ( message, faces ) {
                if ( typeof message !== 'string' ) {
                    conn.onError({
                        type: EASEMOB_IM_MESSAGE_REC_TEXT_ERROR
                        , msg: '不合法的消息内容格式，请检查发送消息内容！'
                    });
                    return;
                }
                if ( Object.prototype.toString.call(faces) !== '[object Object]' ) {
                    return {
                        isemotion: false
                        , body: [
                            {
                                type: "txt"
                                , data: message
                            }
                        ]
                    };
                }

                var receiveMsg = message;
                var emessage = [];
                var expr = /\[[^[\]]{2,3}\]/mg;
                var emotions = receiveMsg.match(expr);

                if ( !emotions || emotions.length < 1 ){
                    return {
                        isemotion: false
                        , body: [
                            {
                                type: "txt"
                                , data: message
                            }
                        ]
                    };
                }
                var isemotion = false;
                for ( var i = 0; i < emotions.length; i++ ) {
                    var tmsg = receiveMsg.substring(0, receiveMsg.indexOf(emotions[i])),
						existEmotion = Easemob.im.EMOTIONS.map[emotions[i]];

                    if ( tmsg ) {
                        emessage.push({
                            type: "txt"
                            , data: tmsg
                        });
                    }
					if ( !existEmotion ) {
                        emessage.push({
                            type: "txt"
                            , data: emotions[i]
                        });
						continue;
                    }
                    var emotion = Easemob.im.EMOTIONS.map ? Easemob.im.EMOTIONS.path + existEmotion : null;

                    if ( emotion ) {
                        isemotion = true;
                        emessage.push({
                            type: 'emotion'
                            , data: emotion
                        });
                    } else {
                        emessage.push({
                            type: 'txt'
                            , data: emotions[i]
                        });
                    }
                    var restMsgIndex = receiveMsg.indexOf(emotions[i]) + emotions[i].length;
                    receiveMsg = receiveMsg.substring(restMsgIndex);
                }
                if ( receiveMsg ) {
                    emessage.push({
                        type: 'txt'
                        , data: receiveMsg
                    });
                }
                if ( isemotion ) {
                    return {
                        isemotion: isemotion
                        , body: emessage
                    };
                }
                return {
                    isemotion: false
                    , body: [
                        {
                            type: "txt"
                            , data: message
                        }
                    ]
                };
            }

            , xmlrequest: _xmlrequest

            , ajax: function ( options ) {
                var dataType = options.dataType || 'text';
                var suc = options.success || EMPTYFN;
                var error = options.error || EMPTYFN;
                var xhr = Utils.xmlrequest();

                xhr.onreadystatechange = function () {
                    if ( xhr.readyState === 4 ) {
                        var status = xhr.status || 0;
                        if ( status === 200 ) {
                            if ( dataType === 'text' ) {
                                suc(xhr.responseText,xhr);
                                return;
                            }
                            if ( dataType === 'json' ) {
                                try {
                                    var json = Utils.parseJSON(xhr.responseText);
                                    suc(json,xhr);
                                } catch ( e ) {
                                    error(xhr.responseText,xhr,"错误的数据,无法转换为json");
                                }
                                return;
                            }
                            if ( dataType === 'xml' ) {
                                if ( xhr.responseXML && xhr.responseXML.documentElement ) {
                                    suc(xhr.responseXML.documentElement,xhr);
                                } else {
                                    error(xhr.responseText,xhr,"浏览器不支持ajax返回xml对象");
                                }
                                return;
                            }
                            suc(xhr.response || xhr.responseText,xhr);
                            return;
                        } else {
                            if ( dataType === 'json' ) {
                                try {
                                    var json = Utils.parseJSON(xhr.responseText);
                                    error(json,xhr,"服务器返回错误信息");
                                } catch ( e ) {
                                    error(xhr.responseText,xhr,"服务器返回错误信息");
                                }
                                return;
                            }
                            if ( dataType === 'xml' ) {
                                if ( xhr.responseXML && xhr.responseXML.documentElement ) {
                                    error(xhr.responseXML.documentElement,xhr,"服务器返回错误信息");
                                } else {
                                    error(xhr.responseText,xhr,"服务器返回错误信息");
                                }
                                return;
                            }
                            error(xhr.responseText,xhr,"服务器返回错误信息");
                            return;
                        }
                    }
                    if ( xhr.readyState === 0 ) {
                        error(xhr.responseText,xhr,"服务器异常");
                    }
                };

                if ( options.responseType ) {
                    if ( xhr.responseType ) {
                        xhr.responseType = options.responseType;
                    } else {
                        error('', xhr, "当前浏览器不支持设置响应类型");
                        return null;
                    }
                }
                if ( options.mimeType ) {
                    if ( Utils.hasOverrideMimeType ) {
                        xhr.overrideMimeType(options.mimeType);
                    } else {
                        error('', xhr, "当前浏览器不支持设置mimeType");
                        return null;
                    }
                }

                var type = options.type || "POST",
					data = options.data || null,
					tempData = '';

				if ( type.toLowerCase() === 'get' && data ) {
					for ( var o in data ) {
						if ( data.hasOwnProperty(o) ) {
							tempData += o + '=' + data[o] + '&';
						}
					}
					tempData = tempData ? tempData.slice(0, -1) : tempData;
					options.url += (options.url.indexOf('?') > 0 ? '&' : '?') + (tempData ? tempData + '&' : tempData) + '_v=' + new Date().getTime();
					data = null, tempData = null;
				}
                xhr.open(type, options.url);

                if ( Utils.isCanSetRequestHeader ) {
                    var headers = options.headers || {};
                    for ( var key in headers ) {
                        if ( headers.hasOwnProperty(key) ) {
                            xhr.setRequestHeader(key, headers[key]);
                        }
                    }
                }

                xhr.send(data);
                return xhr;
            }
        };
    }());



    /**
     * Module2: EmMessage: Various types of messages
     */
    var EmMessage = function ( type, id ) {
        if ( !this instanceof EmMessage ) {
            return new EmMessage(type);
        }

        this._msg = {};

        if ( typeof EmMessage[type] === 'function' ) {
			EmMessage[type].prototype.setGroup = this.setGroup;
            this._msg = new EmMessage[type](id);
        }
        return this._msg;
    }
    EmMessage.prototype.setGroup = function ( group ) {
        this.body.group = group;
    }



    /**
     *  Module3: Message
     */
    var _msgHash = {};
    var Message = function ( message ) {

        if( !this instanceof Message ) {
            return new Message(message, conn);
        }
        
        this.msg = message;
    }

    Message.prototype.send = function ( conn ) {
        var me = this;

        var _send = function ( message ) {

            message.ext = message.ext || {};
            message.ext.weichat = message.ext.weichat || {};
            message.ext.weichat.originType = message.ext.weichat.originType || 'webim';

            var json = {
                from: conn.context.userId || ''
                , to: message.to
                , bodies: [message.body]
                , ext: message.ext || {}
            };

            var jsonstr = Utils.stringify(json);
            var dom = $msg({
                type: message.group || 'chat'
                , to: message.toJid
                , id: message.id
                , xmlns: "jabber:client"
            }).c("body").t(jsonstr);

			if ( message.roomType ) {
				dom.up().c("roomtype", { xmlns: "easemob:x:roomtype", type: "chatroom" });
			}

            conn.retry && setTimeout(function () {
                if ( _msgHash[message.id] ) {
                    if ( typeof _msgHash[message.id].timeout === 'undefined' ) {
                        _msgHash[message.id].timeout = 2;
                    }
                    if ( _msgHash[message.id].timeout === 0 ) {
                        _msgHash[message.id].timeout = 2;
                        _msgHash[message.id].msg.fail instanceof Function 
                        && _msgHash[message.id].msg.fail(message.id);
                    } else {
                        _msgHash[message.id].timeout -= 1;
                        _send(message);
                    }
                }
            }, 20000);
            conn.sendCommand(dom.tree(), message.id);
        }


        if ( me.msg.file ) {
            if ( me.msg.body && me.msg.body.url ) {//only send msg
                _send(me.msg);
                return;
            }
            var _tmpComplete = me.msg.onFileUploadComplete;
            var _complete = function ( data ) {

                if ( data.entities[0]['file-metadata'] ) {
                    var file_len = data.entities[0]['file-metadata']['content-length'];
                    me.msg.file_length = file_len;
                    me.msg.filetype = data.entities[0]['file-metadata']['content-type'];
                    if ( file_len > 204800 ) {
                        me.msg.thumbnail = true;
                    }
                }
                
                me.msg.body = {
                    type: me.msg.type || 'file'
                    , url: data.uri + '/' + data.entities[0]['uuid']
                    , secret: data.entities[0]['share-secret']
                    , filename: me.msg.file.filename || me.msg.filename
                    , size: {
                        width: me.msg.width || 0
                        , height: me.msg.height || 0
                    }
					, length: me.msg.file_length || 0
                    , file_length: me.msg.file_length || 0
                    , filetype: me.msg.filetype
                }

                _send(me.msg);
                _tmpComplete instanceof Function && _tmpComplete(data, me.msg.id);
            };

            me.msg.onFileUploadComplete = _complete;
            Utils.uploadFile.call(conn, me.msg);
        } else {
            me.msg.body = {
                type: me.msg.type === 'chat' ? 'txt' : me.msg.type
                , msg: me.msg.msg 
            };
			if ( me.msg.type === 'cmd' ) {
				me.msg.body.action = me.msg.action;
			} else if ( me.msg.type === 'loc' ) {
				me.msg.body.addr = me.msg.addr;
                me.msg.body.lat = me.msg.lat;
                me.msg.body.lng = me.msg.lng;
			}

            _send(me.msg);
        }
    }
        
    

    /*
     * Module4: Connection
     */
    var Connection = (function () {

		var _networkSt;
		var _listenNetwork = function ( onlineCallback, offlineCallback ) {

			if ( window.addEventListener ) {
				window.addEventListener('online', onlineCallback);
				window.addEventListener('offline', offlineCallback);

			} else if ( window.attachEvent ) {
				if ( document.body ) {
					document.body.attachEvent('onoffline', offlineCallback);
					document.body.attachEvent('onoffline', offlineCallback);
				} else {
					window.attachEvent('load', function () {
						document.body.attachEvent('onoffline', offlineCallback);
						document.body.attachEvent('onoffline', offlineCallback);
					});
				}
			} else {
				/*var onlineTmp = window.ononline;
				var offlineTmp = window.onoffline;

				window.attachEvent('ononline', function () {
					try {
						typeof onlineTmp === 'function' && onlineTmp();
					} catch ( e ) {}
					onlineCallback();
				});
				window.attachEvent('onoffline', function () {
					try {
						typeof offlineTmp === 'function' && offlineTmp();
					} catch ( e ) {}
					offlineCallback();
				});*/
			}
		};

        var _parseRoomFn = function ( result ) {
            var rooms = [];
            var items = result.getElementsByTagName("item");
            if ( items ) {
                for ( var i = 0; i < items.length; i++ ) {
                    var item = items[i];
                    var roomJid = item.getAttribute('jid');
                    var tmp = roomJid.split("@")[0];
                    var room = {
                        jid: roomJid
                        , name: item.getAttribute('name')
                        , roomId: tmp.split('_')[1]
                    };
                    rooms.push(room);
                }
            }
            return rooms;
        }
            
        var _parseRoomOccupantsFn = function ( result ) {
            var occupants = [];
            var items = result.getElementsByTagName("item");
            if ( items ) {
                for ( var i = 0; i < items.length; i++ ) {
                    var item = items[i];
                    var room = {
                        jid: item.getAttribute('jid')
                        , name: item.getAttribute('name')
                    };
                    occupants.push(room);
                }
            }
            return occupants;
        }

        var _parseResponseMessage = function ( msginfo ) {
            var parseMsgData = { errorMsg: true, data: [] };

            var msgBodies = msginfo.getElementsByTagName("body");
            if ( msgBodies ) {
                for ( var i = 0; i < msgBodies.length; i++ ) {
                    var msgBody = msgBodies[i];
                    var childNodes = msgBody.childNodes;
                    if ( childNodes && childNodes.length > 0 ) {
                        var childNode = msgBody.childNodes[0];
                        if ( childNode.nodeType == Strophe.ElementType.TEXT ) {
                            var jsondata = childNode.wholeText ||childNode.nodeValue;
                            jsondata = jsondata.replace('\n','<br>');
                            try {
                                var data = eval("(" + jsondata + ")");
                                parseMsgData.errorMsg = false;
                                parseMsgData.data = [data];
                            } catch ( e ) {}
                        }
                    }
                }

                var delayTags = msginfo.getElementsByTagName("delay");
                if ( delayTags && delayTags.length > 0 ) {
                    var delayTag = delayTags[0];
                    var delayMsgTime = delayTag.getAttribute("stamp");
                    if ( delayMsgTime ) {
                        parseMsgData.delayTimeStamp = delayMsgTime;
                    }
                }
            } else {
                var childrens = msginfo.childNodes;
                if ( childrens && childrens.length>0 ) {
                    var child = msginfo.childNodes[0];
                    if ( child.nodeType == Strophe.ElementType.TEXT ) {
                        try {
                            var data = eval("("+child.nodeValue+")");
                            parseMsgData.errorMsg = false;
                            parseMsgData.data = [data];
                        } catch ( e ) {}
                    }
                }
            }
            return parseMsgData;
        }

        var _parseNameFromJidFn = function ( jid, domain ) {
            domain = domain || "";
            var tempstr = jid;
            var findex = tempstr.indexOf("_");

            if ( findex !== -1 ) {
                tempstr = tempstr.substring(findex+1);
            }
            var atindex = tempstr.indexOf("@" + domain);
            if ( atindex !== -1 ) {
                tempstr = tempstr.substring(0,atindex);
            }
            return tempstr;
        }

        var _parseFriendFn = function ( queryTag ) {
            var rouster = [];
            var items = queryTag.getElementsByTagName("item");
            if ( items ) {
                for( var i = 0; i < items.length; i++ ) {
                    var item = items[i];
                    var jid = item.getAttribute('jid');
                    if ( !jid ) {
                        continue;
                    }
                    var subscription = item.getAttribute('subscription');
                    var friend = {
                        subscription: subscription
                        , jid: jid
                    };
                    var ask = item.getAttribute('ask');
                    if ( ask ) {
                        friend.ask = ask;
                    }
                    var name = item.getAttribute('name');
                    if ( name ) {
                        friend.name = name;
                    } else {
                        var n = _parseNameFromJidFn(jid);
                        friend.name = n;
                    }
                    var groups = [];
                    Strophe.forEachChild(item, 'group',function ( group ) {
                        groups.push(Strophe.getText(group));
                    });
                    friend.groups = groups;
                    rouster.push(friend);
                }
            }
            return rouster;
        }

        var _dologin2IM = function ( options, conn ) {
            var accessToken = options.access_token || '';
            if ( accessToken == '' ) {
                var loginfo = Utils.stringify(options);
                conn.onError({
                    type: EASEMOB_IM_CONNCTION_OPEN_USERGRID_ERROR
                    , msg: "登录失败," + loginfo
                    , data: options
                    , xhr: xhr
                });
                return;
            }
            conn.context.accessToken = options.access_token;
            conn.context.accessTokenExpires = options.expires_in;
            var stropheConn = null;
            if ( conn.isOpening() && conn.context.stropheConn ) {
                stropheConn = conn.context.stropheConn;
            } else if ( conn.isOpened() && conn.context.stropheConn ) {
                return;
            } else {
                stropheConn = new Strophe.Connection(conn.url, {
                    inactivity: conn.inactivity
                    , maxRetries: conn.maxRetries
                    , pollingTime: conn.pollingTime
                });
            }

            var callback = function ( status, msg ) {
                _login2ImCallback(status,msg,conn);
            };

            conn.context.stropheConn = stropheConn;
            if ( conn.route ) {
                stropheConn.connect(conn.context.jid,"$t$" + accessToken,callback,conn.wait,conn.hold,conn.route);
            } else {
                stropheConn.connect(conn.context.jid,"$t$" + accessToken,callback,conn.wait,conn.hold);
            }
        };

        var _parseMessageType = function ( msginfo ) {
            var msgtype = 'normal';
            var receiveinfo = msginfo.getElementsByTagName("received");
            if ( receiveinfo && receiveinfo.length > 0 && receiveinfo[0].namespaceURI === "urn:xmpp:receipts" ) {
                msgtype = 'received';
            } else {
                var inviteinfo =  msginfo.getElementsByTagName("invite");
                if ( inviteinfo && inviteinfo.length > 0 ) {
                    msgtype = 'invite';
                }
            }
            return msgtype;
        };

		var _handleQueueMessage = function ( conn ) {
			for ( var i in _msgHash ) {
				if ( _msgHash.hasOwnProperty(i) ) {
					_msgHash[i].send(conn);
				}
			}
		};

        var _login2ImCallback = function ( status, msg, conn ) {
            if ( status == Strophe.Status.CONNFAIL ) {
                conn.onError({
                    type: EASEMOB_IM_CONNCTION_SERVER_CLOSE_ERROR
                    , msg: msg
                    , reconnect: true
                });
            } else if ( status == Strophe.Status.ATTACHED || status == Strophe.Status.CONNECTED ) {
                var handleMessage = function ( msginfo ) {
                    var type = _parseMessageType(msginfo);

                    if ( 'received' === type ) {
                        conn.handleReceivedMessage(msginfo);
                        return true;
                    } else if ( 'invite' === type ) {
                        conn.handleInviteMessage(msginfo);
                        return true;
                    } else {
                        conn.handleMessage(msginfo);
                        return true;
                    }
                };
                var handlePresence = function ( msginfo ) {
                    conn.handlePresence(msginfo);
                    return true;
                };
                var handlePing = function ( msginfo ) {
                    conn.handlePing(msginfo);
                    return true;
                };
                var handleIq = function ( msginfo ) {
                    conn.handleIq(msginfo);
                    return true;
                };

                conn.addHandler(handleMessage, null, 'message', null, null,  null);
                conn.addHandler(handlePresence, null, 'presence', null, null,  null);
                conn.addHandler(handlePing, "urn:xmpp:ping", 'iq', "get", null,  null);
                conn.addHandler(handleIq, "jabber:iq:roster", 'iq', "set", null,  null);

                conn.context.status = STATUS_OPENED;

                var supportRecMessage = [
                   EASEMOB_IM_MESSAGE_REC_TEXT,
                   EASEMOB_IM_MESSAGE_REC_EMOTION ];

                if ( Utils.isCanDownLoadFile ) {
                    supportRecMessage.push(EASEMOB_IM_MESSAGE_REC_PHOTO);
                    supportRecMessage.push(EASEMOB_IM_MESSAGE_REC_AUDIO_FILE);
                }
                var supportSedMessage = [ EASEMOB_IM_MESSAGE_SED_TEXT ];
                if ( Utils.isCanUploadFile ) {
                    supportSedMessage.push(EASEMOB_IM_MESSAGE_REC_PHOTO);
                    supportSedMessage.push(EASEMOB_IM_MESSAGE_REC_AUDIO_FILE);
                }
                conn.notifyVersion();
				conn.retry && _handleQueueMessage(conn);
                conn.onOpened({
                    canReceive: supportRecMessage
                    , canSend: supportSedMessage
                    , accessToken: conn.context.accessToken
                });
                conn.heartBeat();
            } else if ( status == Strophe.Status.DISCONNECTING ) {
                if ( conn.isOpened() ) {// 不是主动关闭
                    conn.stopHeartBeat();
                    conn.context.status = STATUS_CLOSING;
                    conn.onError({
                        type: EASEMOB_IM_CONNCTION_SERVER_CLOSE_ERROR
                        , msg: msg
                        , reconnect: true
                    });
                }
            } else if ( status == Strophe.Status.DISCONNECTED ) {
                conn.context.status = STATUS_CLOSED;
                conn.clear();
                conn.onClosed();
            } else if ( status == Strophe.Status.AUTHFAIL ) {
                conn.onError({
                    type: EASEMOB_IM_CONNCTION_AUTH_ERROR
                    , msg: '登录失败,请输入正确的用户名和密码'
                });
                conn.clear();
            } else if ( status == Strophe.Status.ERROR ) {
                conn.onError({
                    type: EASEMOB_IM_CONNCTION_SERVER_ERROR
                    , msg: msg || '服务器异常'
                });
            }
        };

        var _getJid = function ( options, conn ) {
            var jid = options.toJid || '';

            if ( jid === '' ) {
                var appKey = conn.context.appKey || '';
                var toJid = appKey + "_" + options.to + "@" + conn.domain;

                if ( options.resource ) {
                    toJid = toJid + "/" + options.resource;
                }
                jid = toJid;
            }
            return jid;
        };

        var _innerCheck = function ( options, conn ) {
            options = options || {};

            if ( options.user == '' ) {
                conn.onError({
                    type: EASEMOB_IM_CONNCTION_USER_NOT_ASSIGN_ERROR
                    , msg: '未指定用户'
                });
                return false;
            }

            var user = options.user || '';
            var appKey = options.appKey || '';
            var devInfos = appKey.split('#');

            if ( devInfos.length !== 2 ) {
                conn.onError({
                    type: EASEMOB_IM_CONNCTION_OPEN_ERROR
                    , msg: '请指定正确的开发者信息(appKey)'
                });
                return false;
            }
            var orgName = devInfos[0];
            var appName = devInfos[1];

            if ( !orgName ) {
                conn.onError({
                    type: EASEMOB_IM_CONNCTION_OPEN_ERROR
                    , msg: '请指定正确的开发者信息(appKey)'
                });
                return false;
            }
            if ( !appName ) {
                conn.onError({
                    type: EASEMOB_IM_CONNCTION_OPEN_ERROR
                    , msg: '请指定正确的开发者信息(appKey)'
                });
                return false;
            }
            
            var jid = appKey + "_" + user.toLowerCase() + "@" + conn.domain,
				resource = options.resource || 'webim';

			if ( conn.multiResources ) {
				resource += user + new Date().getTime() + Math.floor(Math.random().toFixed(6) * 1000000);
			}

            conn.context.jid = jid + '/' + resource;/*jid: {appkey}_{username}@domain/resource*/
            conn.context.userId = user;
            conn.context.appKey = appKey;
            conn.context.appName = appName;
            conn.context.orgName = orgName;
            
            return true;
        }

		var _getXmppUrl = function ( baseUrl, https ) {
			if ( /^(ws|http)s?:\/\/?/.test(baseUrl) ) {
				return baseUrl;
			}

			var url = {
				prefix: 'http',
				base: '://' + (baseUrl || 'im-api.easemob.com'),
				suffix: '/http-bind/'
			};

			if ( https && Utils.isSupportWss ) {
				url.prefix = 'wss';
				url.suffix = '/ws/';
            } else {
                if ( https ) {
					url.prefix = 'https';
				} else if ( window.WebSocket ) {
					url.prefix = 'ws';
					url.suffix = '/ws/';
				}   
            }

			return url.prefix + url.base + url.suffix;
		};

        //class
        var connection = function ( options ) {
            if ( !this instanceof Connection ) {
                return new Connection(options);
            }

            var options = options || {};

			this.multiResources = options.multiResources || false;
            this.wait = options.wait || 30;
            this.retry = options.retry || false;
            this.https = options.https || https;
			this.url = _getXmppUrl(options.url, this.https);
            this.hold = options.hold || 1;
            this.route = options.route || null;
            this.domain = options.domain || "easemob.com";
            this.inactivity = options.inactivity || 30;
            this.heartBeatWait = options.heartBeatWait || 60000;
            this.maxRetries = options.maxRetries || 5;
            this.pollingTime = options.pollingTime || 800;
            this.stropheConn = false;
            this.context = { status: STATUS_INIT };
        };

        connection.prototype.listen = function ( options ) {
			options.url && (this.url = _getXmppUrl(options.url, this.https));//just compatible
            this.onOpened = options.onOpened || EMPTYFN;
            this.onClosed = options.onClosed || EMPTYFN;
            this.onTextMessage = options.onTextMessage || EMPTYFN;
            this.onEmotionMessage = options.onEmotionMessage || EMPTYFN;
            this.onPictureMessage = options.onPictureMessage || EMPTYFN;
            this.onAudioMessage = options.onAudioMessage || EMPTYFN;
            this.onVideoMessage = options.onVideoMessage || EMPTYFN;
            this.onFileMessage = options.onFileMessage || EMPTYFN;
            this.onLocationMessage = options.onLocationMessage || EMPTYFN;
            this.onCmdMessage = options.onCmdMessage || EMPTYFN;
            this.onPresence = options.onPresence || EMPTYFN;
            this.onRoster = options.onRoster || EMPTYFN;
            this.onError = options.onError || EMPTYFN;
            this.onReceivedMessage = options.onReceivedMessage || EMPTYFN;
            this.onInviteMessage = options.onInviteMessage || EMPTYFN;
            this.onOffline = options.onOffline || EMPTYFN;
            this.onOnline = options.onOnline || EMPTYFN;

			_listenNetwork(this.onOnline, this.onOffline);
        }

        connection.prototype.heartBeat = function () {
            var me = this;

            if ( me.heartBeatID ) {
                return;
            }

            var options = {
                to : me.domain,
                type : "normal"
            };
            me.heartBeatID = setInterval(function () {
                me.sendHeartBeatMessage(options);
            }, me.heartBeatWait);
        };

        connection.prototype.sendHeartBeatMessage = function ( options ) {
            var json = {},
                jsonstr = Utils.stringify(json),
                dom = $msg({
                    to : options.to,
                    type : options.type,
                    id : this.getUniqueId(),
                    xmlns : "jabber:client"
                }).c("body").t(jsonstr);

            this.sendCommand(dom.tree());
        };

        connection.prototype.stopHeartBeat = function () {
            this.heartBeatID = clearInterval(this.heartBeatID);
        };


        connection.prototype.sendReceiptsMessage = function ( options ) {
            var dom = $msg({
                from: this.context.jid || ''
                , to: "easemob.com"
                , id: options.id || ''
            }).c("received",{
                xmlns: "urn:xmpp:receipts"
                , id: options.id || ''
            });
            this.sendCommand(dom.tree());
        };

        connection.prototype.open = function ( options ) {
            var pass = _innerCheck(options,this);

            if ( !pass ) {
                return;
            }
            
            var conn = this;

            if ( conn.isOpening() || conn.isOpened() ) {
                return;
            }

            if ( options.accessToken ) {
                options.access_token = options.accessToken;
                _dologin2IM(options,conn);
            } else {
                var apiUrl = options.apiUrl || (this.https ? 'https' : 'http') + '://a1.easemob.com';
                var userId = this.context.userId;
                var pwd = options.pwd || '';
                var appName = this.context.appName;
                var orgName = this.context.orgName;

                var suc = function ( data, xhr ) {
                    conn.context.status = STATUS_DOLOGIN_IM;
                    _dologin2IM(data,conn);
                };
                var error = function ( res, xhr, msg ) {
                    conn.clear();
                    if ( res.error && res.error_description ) {
                        conn.onError({
                            type: EASEMOB_IM_CONNCTION_OPEN_USERGRID_ERROR
                            , msg: "登录失败,"+res.error_description
                            , data: res
                            , xhr: xhr
                        });
                    } else {
                        conn.onError({
                            type: EASEMOB_IM_CONNCTION_OPEN_USERGRID_ERROR
                            , msg: "登录失败"
                            , data: res
                            , xhr: xhr
                        });
                    }
                };
                this.context.status = STATUS_DOLOGIN_USERGRID;

                var loginJson = {
                    grant_type: 'password'
                    , username: userId
                    , password: pwd
                };
                var loginfo = Utils.stringify(loginJson);

                var options = {
                    url: apiUrl + "/" + orgName + "/" + appName + "/token"
                    , dataType: 'json'
                    , data: loginfo
                    , success: suc || EMPTYFN
                    , error: error || EMPTYFN
                };
                Utils.ajax(options);
            }

        };

        connection.prototype.attach = function ( options ) {
            var pass = _innerCheck(options, this);

            if ( !pass ) {
                return;
            }

            options = options || {};

            var accessToken = options.accessToken || '';
            if ( accessToken == '' ) {
                this.onError({
                    type: EASEMOB_IM_CONNCTION_ATTACH_USERGRID_ERROR
                    , msg: '未指定用户的accessToken'
                });
                return;
            }

            var sid = options.sid || '';
            if ( sid === '') {
                this.onError({
                    type: EASEMOB_IM_CONNCTION_ATTACH_ERROR
                    , msg: '未指定用户的会话信息'
                });
                return;
            }

            var rid = options.rid || '';
            if ( rid === '') {
                this.onError({
                    type: EASEMOB_IM_CONNCTION_ATTACH_ERROR
                    , msg: '未指定用户的消息id'
                });
                return;
            }

            var stropheConn = new Strophe.Connection(this.url, {
                inactivity: this.inactivity,
                maxRetries: this.maxRetries,
                pollingTime: this.pollingTime
            });

            this.context.accessToken = accessToken;
            this.context.stropheConn = stropheConn;
            this.context.status = STATUS_DOLOGIN_IM;

            var conn = this;
            var callback = function ( status, msg ) {
                _login2ImCallback(status,msg,conn);
            };

            var jid = this.context.jid;
            var wait = this.wait;
            var hold = this.hold;
            var wind = this.wind || 5;
            stropheConn.attach(jid, sid, rid, callback, wait, hold, wind);
        };

        connection.prototype.close = function () {
            var status = this.context.status;
            if ( status == STATUS_INIT ) {
                return;
            }

            if ( this.isClosed() || this.isClosing() ) {
                return;
            }
            this.stopHeartBeat();
            this.context.status = STATUS_CLOSING;
            this.context.stropheConn.disconnect();
        };

        // see stropheConn.addHandler
        connection.prototype.addHandler = function ( handler, ns, name, type, id, from, options ) {
            this.context.stropheConn.addHandler(handler, ns, name, type, id, from, options);
        };

        connection.prototype.notifyVersion = function ( suc, fail ) {
            var jid = _getJid({},this);
            var dom = $iq({
				from: this.context.jid || ''
				, to: this.domain
				, type: "result"
            })
			.c("query", { xmlns: "jabber:iq:version" })
			.c("name")
			.t("easemob")
			.up()
			.c("version")
			.t(Easemob.im.version)
			.up()
			.c("os")
			.t("webim");

            suc = suc || EMPTYFN;
            error = fail || this.onError;
            var failFn = function ( ele ) {
                error({
                    type: EASEMOB_IM_CONNCTION_NOTIFYVERSION_ERROR
                    , msg: '发送版本信息给服务器时失败'
                    , data: ele
                });
            };
            this.context.stropheConn.sendIQ(dom.tree(), suc, failFn);
            return;
        };

        connection.prototype.handlePresence = function ( msginfo ) {
            if ( this.isClosed() ) {
                return;
            }
            //TODO: maybe we need add precense ack?
            //var id = msginfo.getAttribute('id') || '';
            //this.sendReceiptsMessage({
            //    id: id
            //});

            var from = msginfo.getAttribute('from') || '';
            var to = msginfo.getAttribute('to') || '';
            var type = msginfo.getAttribute('type') || '';
            var presence_type = msginfo.getAttribute('presence_type') || '';
            var fromUser = _parseNameFromJidFn(from);
            var toUser = _parseNameFromJidFn(to);
            var info = {
                from: fromUser
                , to: toUser
                , fromJid: from
                , toJid: to
                , type: type
				, chatroom: msginfo.getElementsByTagName('roomtype').length ? true : false
            };

            var showTags = msginfo.getElementsByTagName("show");
            if ( showTags && showTags.length > 0 ) {
                var showTag = showTags[0];
                info.show = Strophe.getText(showTag);
            }
            var statusTags = msginfo.getElementsByTagName("status");
            if ( statusTags && statusTags.length > 0 ) {
                var statusTag = statusTags[0];
                info.status = Strophe.getText(statusTag);
                info.code = statusTag.getAttribute('code');
            }

            var priorityTags = msginfo.getElementsByTagName("priority");
            if ( priorityTags && priorityTags.length > 0 ) {
                var priorityTag = priorityTags[0];
                info.priority  = Strophe.getText(priorityTag);
            }

			var error = msginfo.getElementsByTagName("error");
            if ( error && error.length > 0 ) {
                var error = error[0];
                info.error = {
					code: error.getAttribute('code')
				};
            }

			var destroy = msginfo.getElementsByTagName("destroy");
            if ( destroy && destroy.length > 0 ) {
                var destroy = destroy[0];
                info.destroy = true;

				var reason = destroy.getElementsByTagName("reason");
				if ( reason && reason.length > 0 ) {
					info.reason = Strophe.getText(reason[0]);
				}
            }

			if ( info.chatroom ) {
				var reflectUser = from.slice(from.lastIndexOf('/') + 1);

				if ( reflectUser === this.context.userId ) {
					if ( info.type === '' && !info.code ) {
						info.type = 'joinChatRoomSuccess';
					} else if ( presence_type === 'unavailable' || info.type === 'unavailable' ) {
						if ( !info.status ) {//web正常退出
							info.type = 'leaveChatRoom';
						} else if ( info.code == 110 ) {//app先退或被管理员踢
							info.type = 'leaveChatRoom';
						} else if ( info.error && info.error.code == 406 ) {//聊天室人已满，无法加入
							info.type = 'joinChatRoomFailed';
						}
					}
				}
			} else if ( presence_type === 'unavailable' || type === 'unavailable' ) {//聊天室被删除没有roomtype, 需要区分群组被踢和解散
				if ( info.destroy ) {//群组和聊天室被删除
					info.type = 'deleteGroupChat';
				} else if ( info.code == 307 || info.code == 321 ) {//群组被踢
					info.type = 'leaveGroup';
				}
			}
			
            this.onPresence(info,msginfo);
        };

        connection.prototype.handlePing = function ( e ) {
            if ( this.isClosed() ) {
                return;
            }
            var id = e.getAttribute('id');
            var from = e.getAttribute('from');
            var to = e.getAttribute('to');
            var dom = $iq({
                from: to
                , to: from
                , id: id
                , type: 'result'
            });
            this.sendCommand(dom.tree());
        };

        connection.prototype.handleIq = function ( e ) {
            var id = e.getAttribute('id');
            var from = e.getAttribute('from') || '';
            var name = _parseNameFromJidFn(from);
            var curJid = this.context.jid;
            var curUser = this.context.userId;

            /*if ( !from || from === curJid ) {
                return true;
			}*/

            var iqresult = $iq({ type: 'result', id: id, from: curJid });
            this.sendCommand(iqresult.tree());

            var msgBodies = e.getElementsByTagName("query");
            if ( msgBodies&&msgBodies.length > 0 ) {
                var queryTag = msgBodies[0];
                var rouster = _parseFriendFn(queryTag);
                this.onRoster(rouster);
            }
            return true;
        };

        connection.prototype.handleMessage = function ( msginfo ) {
            if ( this.isClosed() ) {
                return;
            }

            var id = msginfo.getAttribute('id') || '';
            this.sendReceiptsMessage({
                id: id
            });
            var parseMsgData = _parseResponseMessage(msginfo);
            if ( parseMsgData.errorMsg ) {
                this.handlePresence(msginfo);
                return;
            }
            var msgDatas = parseMsgData.data;
            for ( var i in msgDatas ) {
				if ( !msgDatas.hasOwnProperty(i) ) {
					continue;
				}
                var msg = msgDatas[i];
				if ( !msg.from || !msg.to ) {
					continue;
				}

                var from = msg.from.toLowerCase();
                var too = msg.to.toLowerCase();
                var extmsg = msg.ext || {};
				var chattype = '';
				var typeEl = msginfo.getElementsByTagName("roomtype");
				if ( typeEl.length ) {
					chattype = typeEl[0].getAttribute('type') || 'chat';
				} else {
					chattype = msginfo.getAttribute('type') || 'chat';
				}
                
                var msgBodies = msg.bodies;
                if ( !msgBodies || msgBodies.length == 0 ) {
                    continue;
                }
                var msgBody = msg.bodies[0];
                var type = msgBody.type;
                if ( "txt" === type ) {
                    var receiveMsg = msgBody.msg;
                    var emotionsbody = Utils.parseTextMessage(receiveMsg, Easemob.im.EMOTIONS);
                    if ( emotionsbody.isemotion ) {
                        this.onEmotionMessage({
                            id: id
                            , type: chattype
                            , from: from
                            , to: too
                            , delay: parseMsgData.delayTimeStamp
                            , data: emotionsbody.body
                            , ext: extmsg
                        });
                    } else {
                        this.onTextMessage({
                            id: id
                            , type: chattype
                            , from: from
                            , to: too
                            , delay: parseMsgData.delayTimeStamp
                            , data: receiveMsg
                            , ext: extmsg
                        });
                    }
                } else if ( "img" === type ) {
                    var rwidth = 0;
                    var rheight = 0;
                    if ( msgBody.size ) {
                        rwidth = msgBody.size.width;
                        rheight = msgBody.size.height;
                    }
                    var msg = {
                        id: id
                        , type: chattype
                        , from: from
                        , to: too
                        , url: msgBody.url
                        , secret: msgBody.secret
                        , filename: msgBody.filename
                        , thumb: msgBody.thumb
                        , thumb_secret: msgBody.thumb_secret
                        , file_length: msgBody.file_length || ''
                        , width: rwidth
                        , height: rheight
                        , filetype: msgBody.filetype || ''
                        , accessToken: this.context.accessToken || ''
                        , ext: extmsg
                        , delay: parseMsgData.delayTimeStamp
                    };
                    this.onPictureMessage(msg);
                } else if ( "audio" === type ) {
                    this.onAudioMessage({
                        id: id
                        , type: chattype
                        , from: from
                        , to: too
                        , url: msgBody.url
                        , secret: msgBody.secret
                        , filename: msgBody.filename
                        , length: msgBody.length || ''
                        , file_length: msgBody.file_length || ''
                        , filetype: msgBody.filetype || ''
                        , accessToken: this.context.accessToken || ''
                        , ext: extmsg
                        , delay: parseMsgData.delayTimeStamp
                    });
                } else if ( "file" === type ) {
                    this.onFileMessage({
                        id: id
                        , type: chattype
                        , from: from
                        , to: too
                        , url: msgBody.url
                        , secret: msgBody.secret
                        , filename: msgBody.filename
                        , file_length: msgBody.file_length
                        , accessToken: this.context.accessToken || ''
                        , ext: extmsg
                        , delay: parseMsgData.delayTimeStamp
                    });
                } else if ( "loc" === type ) {
                    this.onLocationMessage({
                        id: id
                        , type: chattype
                        , from: from
                        , to: too
                        , addr: msgBody.addr
                        , lat: msgBody.lat
                        , lng: msgBody.lng
                        , ext: extmsg
                        , delay: parseMsgData.delayTimeStamp
                    });
                } else if ( "video" === type ) {
                    this.onVideoMessage({
                        id: id
                        , type: chattype
                        , from: from
                        , to: too
                        , url: msgBody.url
                        , secret: msgBody.secret
                        , filename: msgBody.filename
                        , file_length: msgBody.file_length
                        , accessToken: this.context.accessToken || ''
                        , ext: extmsg
                        , delay: parseMsgData.delayTimeStamp
                    });
                } else if ( "cmd" === type ) {
                    this.onCmdMessage({
                        id: id
                        , from: from
                        , to: too
                        , action: msgBody.action
                        , ext: extmsg
                        , delay: parseMsgData.delayTimeStamp
                    });
                }
            }
        };

        connection.prototype.handleReceivedMessage = function ( message ) {
            this.onReceivedMessage(message);

            var rcv = message.getElementsByTagName('received'),
                id,
                mid;

            if ( rcv.length > 0 ) {
                if ( rcv[0].childNodes && rcv[0].childNodes.length > 0 ) {
                    id = rcv[0].childNodes[0].nodeValue;
                } else {
                    id = rcv[0].innerHTML || rcv[0].innerText;
                }
                mid = rcv[0].getAttribute('mid');
            }
            
            if ( _msgHash[id] ) {
                _msgHash[id].msg.success instanceof Function && _msgHash[id].msg.success(id, mid);
                delete _msgHash[id];
            }
        };

        connection.prototype.handleInviteMessage = function ( message ) {
            var form = null;
            var invitemsg = message.getElementsByTagName('invite');
            var id = message.getAttribute('id') || '';
            this.sendReceiptsMessage({
                id: id
            });

            if ( invitemsg && invitemsg.length > 0 ) {
                var fromJid = invitemsg[0].getAttribute('from');
                form = _parseNameFromJidFn(fromJid);
            }
            var xmsg = message.getElementsByTagName('x');
            var roomid = null;
            if ( xmsg && xmsg.length > 0 ) {
                for ( var i = 0; i < xmsg.length; i++ ) {
                    if ( 'jabber:x:conference' === xmsg[i].namespaceURI ) {
                        var roomjid = xmsg[i].getAttribute('jid');
                        roomid = _parseNameFromJidFn(roomjid);
                    }
                }
            }
            this.onInviteMessage({
                type: 'invite'
                , from: form
                , roomid: roomid
            });
        };

        connection.prototype.sendCommand = function ( dom, id ) {
            if ( this.isOpened() ) {
                this.context.stropheConn.send(dom);
            } else {
                this.onError({
                    type : EASEMOB_IM_CONNCTION_OPEN_ERROR,
                    msg : '连接还未建立,请先登录或等待登录处理完毕'
                    , reconnect: true
                });
            }
        };

        connection.prototype.getUniqueId = function ( prefix ) {
            var cdate = new Date();
            var offdate = new Date(2010,1,1);
			var offset = cdate.getTime()-offdate.getTime();
			var hexd = parseInt(offset).toString(16);

            if ( typeof prefix === 'string' || typeof prefix === 'number' ) {
                return prefix + '_' + hexd;
            } else {
                return 'WEBIM_' + hexd;
            }
        };
        
        connection.prototype.send = function ( message ) {
            if ( Object.prototype.toString.call(message) === '[object Object]' ) {
                var appKey = this.context.appKey || '';
                var toJid = appKey + "_" + message.to + "@" + this.domain;

                if ( message.group ) {
                    toJid = appKey + "_" + message.to + '@conference.' + this.domain;
                }
                if ( message.resource ) {
                    toJid = toJid + "/" + message.resource;
                }

                message.toJid = toJid;
                message.id = message.id || this.getUniqueId();
                _msgHash[message.id] = new Message(message);
                _msgHash[message.id].send(this);
            } else if ( typeof message === 'string' ) {
                _msgHash[message] && _msgHash[message].send(this);
            }
        }

        connection.prototype.addRoster = function ( options ) {
            var jid = _getJid(options, this);
            var name = options.name || '';
            var groups = options.groups || '';

            var iq = $iq({type: 'set'});
            iq.c("query", {xmlns:'jabber:iq:roster'});
            iq.c("item", {jid: jid, name: name});

            if ( groups ) {
                for ( var i = 0; i < groups.length; i++ ) {
                    iq.c('group').t(groups[i]).up();
                }
            }
            var suc = options.success || EMPTYFN;
            var error = options.error || EMPTYFN;
            this.context.stropheConn.sendIQ(iq.tree(),suc,error);
        };

        connection.prototype.removeRoster = function ( options ) {
            var jid = _getJid(options,this);
            var iq = $iq({ type: 'set' }).c('query', { xmlns: "jabber:iq:roster" }).c('item', { jid: jid, subscription: "remove" });

            var suc = options.success || EMPTYFN;
            var error = options.error || EMPTYFN;
            this.context.stropheConn.sendIQ(iq,suc,error);
        };

        connection.prototype.getRoster = function ( options ) {
            var conn = this;
            var dom  = $iq({
                type: 'get'
            }).c('query', { xmlns: 'jabber:iq:roster' });

            options = options || {};
            suc = options.success || this.onRoster;
            var completeFn = function ( ele ) {
                var rouster = [];
                var msgBodies = ele.getElementsByTagName("query");
                if ( msgBodies&&msgBodies.length > 0 ) {
                    var queryTag = msgBodies[0];
                    rouster = _parseFriendFn(queryTag);
                }
                suc(rouster,ele);
            };
            error = options.error || this.onError;
            var failFn = function ( ele ) {
                error({
                    type: EASEMOB_IM_CONNCTION_GETROSTER_ERROR
                    , msg: '获取联系人信息失败'
                    , data: ele
                });
            };
            if ( this.isOpened() ) {
                this.context.stropheConn.sendIQ(dom.tree(),completeFn,failFn);
            } else {
                error({
                    type: EASEMOB_IM_CONNCTION_OPEN_ERROR
                    , msg: '连接还未建立,请先登录或等待登录处理完毕'
                });
            }
        };

        connection.prototype.subscribe = function ( options ) {
            var jid = _getJid(options, this);
            var pres = $pres({ to: jid, type: "subscribe" });
            if ( options.message ) {
                pres.c("status").t(options.message).up();
            }
            if ( options.nick ) {
                pres.c('nick', { 'xmlns': "http://jabber.org/protocol/nick" }).t(options.nick);
            }
            this.sendCommand(pres.tree());
        };

        connection.prototype.subscribed = function ( options ) {
            var jid = _getJid(options,this);
            var pres = $pres({to: jid, type: "subscribed"});

            if ( options.message ) {
                pres.c("status").t(options.message).up();
            }
            this.sendCommand(pres.tree());
        };

        connection.prototype.unsubscribe = function ( options ) {
            var jid = _getJid(options,this);
            var pres = $pres({to: jid, type: "unsubscribe"});

            if ( options.message ) {
                pres.c("status").t(options.message);
            }
            this.sendCommand(pres.tree());
        };

        connection.prototype.unsubscribed = function ( options ) {
            var jid = _getJid(options,this);
            var pres = $pres({ to: jid, type: "unsubscribed" });

            if ( options.message ) {
                pres.c("status").t(options.message).up();
            }
            this.sendCommand(pres.tree());
         };

        connection.prototype.createRoom = function ( options ) {
            var suc =options.success || EMPTYFN;
            var err =  options.error || EMPTYFN;
            var roomiq;

            roomiq = $iq({
                to: options.rooomName,
                type: "set"
            })
			.c("query", { xmlns: Strophe.NS.MUC_OWNER })
			.c("x", { xmlns: "jabber:x:data", type: "submit" });

            return this.context.stropheConn.sendIQ(roomiq.tree(), suc, err);
        };

        connection.prototype.join = function ( options ) {
            var roomJid = this.context.appKey + "_" + options.roomId + '@conference.' + this.domain;
            var room_nick = roomJid + "/" + this.context.userId;
            var suc = options.success || EMPTYFN;
            var err = options.error || EMPTYFN;
            var errorFn = function ( ele ) {
                err({
                    type: EASEMOB_IM_CONNCTION_JOINROOM_ERROR
                    , msg: '加入房间失败'
                    , data: ele
                });
            };
            var iq = $pres({
                from: this.context.jid,
                to: room_nick
            })
			.c("x", { xmlns: Strophe.NS.MUC });

            this.context.stropheConn.sendIQ(iq.tree(), suc, errorFn);
        };

        connection.prototype.listRooms = function ( options ) {
            var iq = $iq({
              to: options.server||'conference.' + this.domain,
              from: this.context.jid,
              type: "get"
            })
			.c("query", { xmlns: Strophe.NS.DISCO_ITEMS });

            var suc =options.success || EMPTYFN;
            var completeFn = function ( result ) {
                var rooms = [];
                rooms = _parseRoomFn(result);
                suc(rooms);
            }
            var err =  options.error || EMPTYFN;
            var errorFn = function ( ele ) {
                err({
                    type: EASEMOB_IM_CONNCTION_GETROOM_ERROR
                    , msg: '获取群组列表失败'
                    , data: ele
                });
            }
            this.context.stropheConn.sendIQ(iq.tree(), completeFn, errorFn);
        };

        connection.prototype.queryRoomMember = function ( options ) {
            var domain = this.domain;
            var members = [];
            var iq= $iq({
			  to: this.context.appKey + "_" + options.roomId + '@conference.' + this.domain
			  , type: 'get'
			})
			.c('query', { xmlns: Strophe.NS.MUC+'#admin' })
			.c('item', { affiliation: 'member' });

            var suc =options.success || EMPTYFN;
            var completeFn = function ( result ) {
                var items = result.getElementsByTagName('item');

                if ( items ) {
                    for ( var i = 0; i < items.length; i++ ) {
                        var item = items[i];
                        var mem = {
							jid: item.getAttribute('jid')
							, affiliation: 'member'
						};
                        members.push(mem);
                    }
                }
                suc(members);
            };
            var err =  options.error || EMPTYFN;
            var errorFn = function ( ele ) {
                err({
                    type: EASEMOB_IM_CONNCTION_GETROOMMEMBER_ERROR
                    , msg: '获取群组成员列表失败'
                    , data: ele
                });
            };
            this.context.stropheConn.sendIQ(iq.tree(), completeFn, errorFn);
        };

        connection.prototype.queryRoomInfo = function ( options ) {
            var domain = this.domain;
            var iq= $iq({
			  to:  this.context.appKey+"_"+options.roomId+'@conference.' + domain,
			  type: "get"
			}).c("query", { xmlns: Strophe.NS.DISCO_INFO });

            var suc =options.success || EMPTYFN;
            var members = [];
            var completeFn = function ( result ) {
                var fields = result.getElementsByTagName('field');
                if ( fields ) {
                    for ( var i = 0; i < fields.length; i++ ) {
                        var field = fields[i];
                        if ( field.getAttribute('label') === 'owner' ) {
                            var mem = {
                                jid: (field.textContent || field.text) + "@" + domain
                                , affiliation: 'owner'
                            };
                            members.push(mem);
                        }
                    }
                }
                suc(members);
            };
            var err =  options.error || EMPTYFN;
            var errorFn = function ( ele ) {
                err({
                    type: EASEMOB_IM_CONNCTION_GETROOMINFO_ERROR
                    , msg: '获取群组信息失败'
                    , data: ele
                });
            };
            this.context.stropheConn.sendIQ(iq.tree(), completeFn, errorFn);
        };

        connection.prototype.queryRoomOccupants = function ( options ) {
            var suc =options.success || EMPTYFN;
            var completeFn = function ( result ) {
                var occupants = [];
                occupants = _parseRoomOccupantsFn(result);
                suc(occupants);
            }
            var err =  options.error || EMPTYFN;
            var errorFn = function ( ele ) {
                err({
                    type: EASEMOB_IM_CONNCTION_GETROOMOCCUPANTS_ERROR
                    , msg: '获取群组出席者列表失败'
                    , data: ele
                });
            };
            var attrs = {
              xmlns: Strophe.NS.DISCO_ITEMS
            };
            var info = $iq({
              from: this.context.jid
              , to: this.context.appKey + "_" + options.roomId + '@conference.' + this.domain
              , type: 'get'
            }).c('query', attrs);
            this.context.stropheConn.sendIQ(info.tree(), completeFn, errorFn);
        };

        connection.prototype.setUserSig = function ( desc ) {
            var dom = $pres({ xmlns: 'jabber:client' });
            desc = desc || "";
            dom.c("status").t(desc);
            this.sendCommand(dom.tree());
        };

        connection.prototype.setPresence = function ( type, status ) {
            var dom = $pres({ xmlns: 'jabber:client' });
            if ( type ) {
                if ( status ) {
                    dom.c("show").t(type);
                    dom.up().c("status").t(status);
                } else {
                    dom.c("show").t(type);
                }
            }
            this.sendCommand(dom.tree());
        };

        connection.prototype.getPresence = function () {
            var dom = $pres({ xmlns: 'jabber:client' });
            var conn = this;
            this.sendCommand(dom.tree());
        };

        connection.prototype.ping = function ( options ) {
            options = options || {};
            var jid = _getJid(options,this);

            var dom = $iq({
                from: this.context.jid || ''
                , to: jid
                , type: "get"
            }).c("ping", { xmlns: "urn:xmpp:ping" });

            suc = options.success || EMPTYFN;
            error = options.error || this.onError;
            var failFn = function ( ele ) {
                error({
                    type: EASEMOB_IM_CONNCTION_PING_ERROR
                    , msg: 'ping失败'
                    , data: ele
                });
            };
            if ( this.isOpened() ) {
                this.context.stropheConn.sendIQ(dom.tree(),suc,failFn);
            } else {
                error({
                    type: EASEMOB_IM_CONNCTION_OPEN_ERROR
                    , msg: '连接还未建立,请先登录或等待登录处理完毕'
                });
            }
            return;
        };

        connection.prototype.isOpened = function () {
            return this.context.status == STATUS_OPENED;
        };

        connection.prototype.isOpening = function () {
            var status = this.context.status;
            return status == STATUS_DOLOGIN_USERGRID || status == STATUS_DOLOGIN_IM;
        };

        connection.prototype.isClosing = function () {
            return this.context.status == STATUS_CLOSING;
        };

        connection.prototype.isClosed = function () {
            return this.context.status == STATUS_CLOSED;
        };

        connection.prototype.clear = function () {
            var key = this.context.appKey;
            this.context = {
                status: STATUS_INIT
                , appKey: key
            };
        };

		//rooms list
		connection.prototype.getChatRooms = function ( options ) {

			if ( !Utils.isCanSetRequestHeader ) {
				conn.onError({
					type: EASEMOB_IM_CONNCTION_AUTH_ERROR
					, msg: "当前浏览器不支持聊天室功能"
				});
				return;
			}

            var conn = this,
				token = options.accessToken || this.context.accessToken;

            if ( token ) {
                var apiUrl = options.apiUrl || (this.https ? 'https' : 'http') + '://a1.easemob.com';
                var appName = this.context.appName;
                var orgName = this.context.orgName;

				if ( !appName || !orgName ) {
					conn.onError({
						type: EASEMOB_IM_CONNCTION_AUTH_ERROR
						, msg: "token无效"
						, data: null 
					});
					return;
				}

                var suc = function ( data, xhr ) {
					typeof options.success === 'function' && options.success(data);
                };

                var error = function ( res, xhr, msg ) {
                    if ( res.error && res.error_description ) {
                        conn.onError({
                            type: EASEMOB_IM_LOAD_CHATROOM_ERROR
                            , msg: "获取聊天室失败," + res.error_description
                            , data: res
                            , xhr: xhr
                        });
                    }
                };

                var opts = {
                    url: apiUrl + "/" + orgName + "/" + appName + "/chatrooms"
                    , dataType: 'json'
					, type: 'get'
					, headers: {Authorization: 'Bearer ' + token}
                    , success: suc || EMPTYFN
                    , error: error || EMPTYFN
                };
                Utils.ajax(opts);
            } else {
				conn.onError({
					type: EASEMOB_IM_CONNCTION_AUTH_ERROR
					, msg: "token无效"
					, data: null 
				});               
            }

        };

		connection.prototype.joinChatRoom = function ( options ) {
            var roomJid = this.context.appKey + "_" + options.roomId + '@conference.' + this.domain;
            var room_nick = roomJid + "/" + this.context.userId;
            var suc = options.success || EMPTYFN;
            var err = options.error || EMPTYFN;
            var errorFn = function ( ele ) {
                err({
                    type: EASEMOB_IM_CONNCTION_JOINROOM_ERROR
                    , msg: '加入聊天室失败'
                    , data: ele
                });
            };

			var iq = $pres({
                from: this.context.jid,
                to: room_nick
            })
			.c("x", { xmlns: Strophe.NS.MUC + '#user' })
			.c('item', { affiliation: 'member', role: 'participant' })
			.up().up()
			.c("roomtype", { xmlns: "easemob:x:roomtype", type: "chatroom" });

            this.context.stropheConn.sendIQ(iq.tree(), suc, errorFn);
        };

		connection.prototype.quitChatRoom = function ( options ) {
			var roomJid = this.context.appKey + "_" + options.roomId + '@conference.' + this.domain;
            var room_nick = roomJid + "/" + this.context.userId;
            var suc = options.success || EMPTYFN;
            var err = options.error || EMPTYFN;
            var errorFn = function ( ele ) {
                err({
                    type: EASEMOB_IM_CONNCTION_JOINROOM_ERROR
                    , msg: '退出房间失败'
                    , data: ele
                });
            };
            var iq = $pres({
                from: this.context.jid,
                to: room_nick,
				type: 'unavailable'
            })
			.c("x", { xmlns: Strophe.NS.MUC + '#user' })
			.c('item', { affiliation: 'none', role: 'none' })
			.up().up()
			.c("roomtype", { xmlns: "easemob:x:roomtype", type: "chatroom" });

            this.context.stropheConn.sendIQ(iq.tree(), suc, errorFn);
        };

        return connection;
    }());



    /*
     * CONST     
    */
    var EMPTYFN = function() {};

    tempIndex = 0;
    EASEMOB_IM_CONNCTION_USER_NOT_ASSIGN_ERROR = tempIndex++;
    EASEMOB_IM_CONNCTION_OPEN_ERROR = tempIndex++;
    EASEMOB_IM_CONNCTION_AUTH_ERROR = tempIndex++;
    EASEMOB_IM_CONNCTION_OPEN_USERGRID_ERROR = tempIndex++;
    EASEMOB_IM_CONNCTION_ATTACH_ERROR = tempIndex++;
    EASEMOB_IM_CONNCTION_ATTACH_USERGRID_ERROR = tempIndex++;
    EASEMOB_IM_CONNCTION_REOPEN_ERROR = tempIndex++;
    EASEMOB_IM_CONNCTION_SERVER_CLOSE_ERROR = tempIndex++;
    EASEMOB_IM_CONNCTION_SERVER_ERROR = tempIndex++;
    EASEMOB_IM_CONNCTION_IQ_ERROR = tempIndex++;
    EASEMOB_IM_CONNCTION_PING_ERROR = tempIndex++;
    EASEMOB_IM_CONNCTION_NOTIFYVERSION_ERROR = tempIndex++;
    EASEMOB_IM_CONNCTION_GETROSTER_ERROR = tempIndex++;
    EASEMOB_IM_CONNCTION_CROSSDOMAIN_ERROR = tempIndex++;
    EASEMOB_IM_CONNCTION_LISTENING_OUTOF_MAXRETRIES = tempIndex++;
    EASEMOB_IM_CONNCTION_RECEIVEMSG_CONTENTERROR = tempIndex++;
    EASEMOB_IM_CONNCTION_JOINROOM_ERROR = tempIndex++;
    EASEMOB_IM_CONNCTION_GETROOM_ERROR = tempIndex++;
    EASEMOB_IM_CONNCTION_GETROOMINFO_ERROR = tempIndex++;
    EASEMOB_IM_CONNCTION_GETROOMMEMBER_ERROR = tempIndex++;
    EASEMOB_IM_CONNCTION_GETROOMOCCUPANTS_ERROR = tempIndex++;
    
    EASEMOB_IM_UPLOADFILE_BROWSER_ERROR = tempIndex++;
    EASEMOB_IM_UPLOADFILE_ERROR = tempIndex++;
    EASEMOB_IM_UPLOADFILE_NO_LOGIN = tempIndex++;
    EASEMOB_IM_UPLOADFILE_NO_FILE = tempIndex++;
    EASEMOB_IM_DOWNLOADFILE_ERROR = tempIndex++;
    EASEMOB_IM_DOWNLOADFILE_NO_LOGIN = tempIndex++;
    EASEMOB_IM_DOWNLOADFILE_BROWSER_ERROR = tempIndex++;

    EASEMOB_IM_RESISTERUSER_ERROR = tempIndex++;

	EASEMOB_IM_LOAD_CHATROOM_ERROR = tempIndex++;
	EASEMOB_IM_JOIN_CHATROOM_ERROR = tempIndex++;
	EASEMOB_IM_QUIT_CHATROOM_ERROR = tempIndex++;

    tempIndex = 0;
    EASEMOB_IM_MESSAGE_REC_TEXT = tempIndex++;
    EASEMOB_IM_MESSAGE_REC_TEXT_ERROR = tempIndex++;
    EASEMOB_IM_MESSAGE_REC_EMOTION = tempIndex++;
    EASEMOB_IM_MESSAGE_REC_PHOTO = tempIndex++;
    EASEMOB_IM_MESSAGE_REC_AUDIO = tempIndex++;
    EASEMOB_IM_MESSAGE_REC_AUDIO_FILE = tempIndex++;
    EASEMOB_IM_MESSAGE_REC_VEDIO = tempIndex++;
    EASEMOB_IM_MESSAGE_REC_VEDIO_FILE = tempIndex++;
    EASEMOB_IM_MESSAGE_REC_FILE = tempIndex++;

    EASEMOB_IM_MESSAGE_SED_TEXT = tempIndex++;
    EASEMOB_IM_MESSAGE_SED_EMOTION = tempIndex++;
    EASEMOB_IM_MESSAGE_SED_PHOTO = tempIndex++;
    EASEMOB_IM_MESSAGE_SED_AUDIO = tempIndex++;
    EASEMOB_IM_MESSAGE_SED_AUDIO_FILE = tempIndex++;
    EASEMOB_IM_MESSAGE_SED_VEDIO = tempIndex++;
    EASEMOB_IM_MESSAGE_SED_VEDIO_FILE = tempIndex++;
    EASEMOB_IM_MESSAGE_SED_FILE = tempIndex++;
    EASEMOB_IM_FILESIZE_LIMIT = 10485760;


    tempIndex = 0;
    var STATUS_INIT = tempIndex++;
    var STATUS_DOLOGIN_USERGRID = tempIndex++;
    var STATUS_DOLOGIN_IM = tempIndex++;
    var STATUS_OPENED = tempIndex++;
    var STATUS_CLOSING = tempIndex++;
    var STATUS_CLOSED = tempIndex++;

    delete tempIndex;


    Easemob.im.Connection = Connection;
    Easemob.im.EmMessage = EmMessage;
    Easemob.im.Helper = Easemob.im.Utils = Utils;
    window.Easemob = Easemob;

}(window, undefined));
