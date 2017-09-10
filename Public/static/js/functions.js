var carrousel = $( ".carrousel" );
function preview(obj){
	var src = $(obj).attr("src");
	carrousel.find("img").attr( "src", src );
	carrousel.fadeIn( 200 );
}

$( ".carrousel" ).click( function(e){
	carrousel.find( "img" ).attr( "src", '' );
	carrousel.fadeOut( 200 );
} );
function upload(){
	var upload = "upload_bak.php";//远程上传接口
	var callbackurl = "upload_callback_func.php";//回调url
	$.app.method.upload(upload,callbackurl,function(data){
		if(data.msg && (typeof(data.msg) == 'string' || typeof(data.msg.url) == 'string')){
			var url = (typeof(data.msg) == 'string') ? data.msg : data.msg.url;
			console.log(url);
			chat.data.type = 2;
			var json = {"type": chat.data.type,"name": chat.data.name,"avatar": chat.data.avatar,"message": url,"c":'img','roomid':chat.data.crd};
			chat.wsSend(JSON.stringify(json));
			//$(".re").attr('src',data.msg.url);
		}else{
			var tip = data.err ? data.err : '上传失败';
			$.app.method.tip('提示信息', tip, 'error');
		}
	},function(filename){ 
		if(!filename.match(/\.jpg$|\.jpeg$|\.png$|\.gif$|\.bmp$/i)){
			$.app.method.tip('上传文件后缀不允许');
			return false;
		}
		return true;
	});
}
function getJsonLength(jsonData){
	var jsonLength = 0;
	for(var item in jsonData){
		if(jsonData.hasOwnProperty(item)){  
			jsonLength++;
		}  
		
	}
	return jsonLength;
}
