//文件上传成功后操作
function att_show(serverData,file)
{
	var serverData = serverData.replace(/<div.*?<\/div>/g,'');
	var data = serverData.split(',');
	//附件aid
	var id = data[0];
	//附件地址
	var src = data[1];
	//附件类型
	var ext = data[2];
	//附件名称
	var filename = data[3];
	//上传失败，提示上传信息
	if(id == 0) {
		try{
			Wind.use("artDialog",function(){
				art.dialog({
					id:'error',
					icon: 'error',
					content: src,
					cancelVal: '确定',
					cancel: function(){
						$("#"+file.id).remove();
					}
				});
			});
		}catch(err){
			alert(src);
			$("#"+file.id).remove();
		}
		return false;
	}
	//如果是图片类型附件，则显示对应图片，否则显示附件类型图片
	var img = "";
	if(ext == 1) {
	    img = '<div class="get selected" id="aid-'+id+'"><a class="del" href="javascript:;">删除</a> <img onclick="att_cancel(this,'+id+',\'upload\')" width="87" height="98" src="'+src+'" data-id="'+id+'" data-path="'+src+'" alt="上传完成" title="'+filename+'"><input type="text" class="J_file_desc" name="flashatt['+id+'][desc]" placeholder="请输入描述" value="'+filename+'" style="width:68px"></div>';
	} else {
		img = '<div class="get selected" id="aid-'+id+'"><a class="del" href="javascript:;">删除</a> <img onclick="att_cancel(this,'+id+',\'upload\')" width="87" height="98" src="'+GV.DIMAUB+'public/images/ext/'+ext+'.png" data-id="'+id+'" data-path="'+src+'" alt="上传完成" title="'+filename+'"><input type="text" class="J_file_desc" name="flashatt['+id+'][desc]" placeholder="请输入描述" value="'+filename+'"  style="width:68px"></div>';
	}
	//设置附件标识
	$("#"+file.id).removeClass("J_empty").addClass("uploaded").html(img);
	//设置已经选定附件
	$('#att-status').append('|'+src);
	$('#att-name').append('|'+filename);
}

//上传好的附件，选中/取消选中
function att_cancel(obj,id,source){
	//图片地址
	var src = $(obj).attr("data-path");
	//上传图片文件名
	var filename = $(obj).attr("title");
	//选择状态中的数据对象
	var selected = $("#fsUploadProgress .selected");
	//检查是否已经被选中
	if($("#aid-"+id).hasClass('selected')){
		//去除选中状态
		$("#aid-"+id).removeClass("selected");
		selected = $("#fsUploadProgress .selected");
		//取得被选中图片集合
		var imgstr = $("#att-status").html();
		//计算被选中的附件长度
		var length = selected.children("img").length;
		var strs = filenames = '';
		for(var i=0;i<length;i++){
			//图片地址
			strs += '|'+selected.children("img").eq(i).attr('data-path');
			//图片文件名
			filenames += '|'+selected.children("img").eq(i).attr('title');
		}

		//放入被选中的容器
		$('#att-status').html(strs);
		$('#att-name').html(filenames);
		//取消选中
		if(source=='upload'){
			$('#att-status-del').append('|'+id);
		}
	} else {
		//增加选中状态
		$("#aid-"+id).addClass("selected");
		$('#att-status').append('|'+src);
		$('#att-name').append('|'+filename);

		var imgstr_del = $("#att-status-del").html();
		var imgstr_del_obj = selected.children("img")
		var length_del = imgstr_del_obj.length;
		var strs_del='';
		for(var i=0;i<length_del;i++){
			strs_del += '|'+imgstr_del_obj.eq(i).attr('data-id');
		}
		if(source=='upload'){
			$('#att-status-del').html(strs_del);
		}
	}
}
//文件选择对话框显示之前触发
function fileDialogStart() {
	/* I don't need to do anything here */
}
//当文件选择对话框关闭消失时，如果选择的文件成功加入上传队列，那么针对每个成功加入的文件都会触发一次该事件
function fileQueued(file) {
	if(file!= null){
		try {
			//容器
			var targetID = this.customSettings.progressTarget;
			var id = file.id;
			var name = file.name;
			$("#"+targetID).prepend('<li class="J_empty" id="'+id+'"><div class="schedule"><em>0%</em><span style="width: 0%;"></span></div></li>');
		} catch (ex) {
			this.debug(ex);
		}
	}
}
//当选择文件对话框关闭，并且所有选择文件已经处理完成（加入上传队列成功或者失败）时，此事件被触发
function fileDialogComplete(numFilesSelected, numFilesQueued)
{
	try {
		//选择后自动上传
		this.startUpload();
	} catch (ex)  {
        this.debug(ex);
	}
}
//在文件往服务端上传之前触发此事件
function uploadStart(file)
{
	return true;
}
//该事件由flash定时触发，提供三个参数分别访问上传文件对象、已上传的字节数，总共的字节数。
//因此可以在这个事件中来定时更新页面中的UI元素，以达到及时显示上传进度的效果。
function uploadProgress(file, bytesLoaded, bytesTotal)
{
	//上传进度
	var percent = Math.ceil((bytesLoaded / bytesTotal) * 100);
	var id = file.id;
	$("#"+id+" em").html(percent+"%");
	$("#"+id+" span").width(percent+"%");
}
//当文件上传的处理已经完成并且服务端返回了200的HTTP状态时，触发此事件。
function uploadSuccess(file, serverData)
{
	//文件上传完毕回调
	//serverData 服务器返回的数据
	//file 文件对象
	att_show(serverData,file);
}
//当上传队列中的一个文件完成了一个上传周期，无论是成功(uoloadSuccess触发)还是失败(uploadError触发)，此事件都会被触发
function uploadComplete(file)
{
	if (this.getStats().files_queued > 0)
	{
		 this.startUpload();
	}
}
//无论什么时候，只要上传被终止或者没有成功完成，那么该事件都将被触发
function uploadError(file, errorCode, message) {
	var msg;
	switch (errorCode)
	{
		case SWFUpload.UPLOAD_ERROR.HTTP_ERROR:
			msg = "上传错误: " + message;
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED:
			msg = "上传错误";
			break;
		case SWFUpload.UPLOAD_ERROR.IO_ERROR:
			msg = "服务器 I/O 错误";
			break;
		case SWFUpload.UPLOAD_ERROR.SECURITY_ERROR:
			msg = "服务器安全认证错误";
			break;
		case SWFUpload.UPLOAD_ERROR.FILE_VALIDATION_FAILED:
			msg = "附件安全检测失败，上传终止";
			break;
		case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
			msg = '上传取消';
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
			msg = '上传终止';
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
			msg = '单次上传文件数限制为 '+swfu.settings.file_upload_limit+' 个';
			break;
		default:
			msg = message;
			break;
		}
	alert(msg);
	$("#"+file.id).remove();
}
//当选择文件对话框关闭消失时，如果选择的文件加入到上传队列中失败，
//那么针对每个出错的文件都会触发一次该事件(此事件和fileQueued事件是二选一触发，文件添加到队列只有两种可能，成功和失败)。
function fileQueueError(file, errorCode, message)
{
	var errormsg;
	switch (errorCode) {
	case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
		errormsg = "请不要上传空文件";
		break;
	case SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED:
		errormsg = "队列文件数量超过设定值";
		break;
	case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
		errormsg = "文件尺寸超过设定值";
		break;
	case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
		errormsg = "文件类型不合法";
	default:
		errormsg = '上传错误，请与管理员联系！';
		break;
	}

	alert(errormsg);
	$("#"+file.id).remove();
}