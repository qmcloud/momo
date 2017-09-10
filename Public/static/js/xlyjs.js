(function($){
	$.extend({ app: { method: {}, module: {} } });
	/* 消息提示 */
	$.extend($.app.method, { tip:
		function (msg) {
			alert(msg);
		}
	});
	/* 点击上传 */
	$.extend($.app.method, {'uploadOption': {}}, { upload:
		function(action, callbackurl,callback, check){
			var option = {
				action   : action,
				id       : 'app-upload-when-click-div-' + new Date().getTime(),
				onload   : false,
				dialog   : null,
				callback : callback,
				check    : check,
				method   : {
					callback: function(that){
						if(!$.app.method.uploadOption.onload) return false;
						var text = that.contentWindow.document.body.innerHTML;
						$('#' + $.app.method.uploadOption.id).remove();
						try{
							var obj = $.parseJSON(text);
						}catch(e){}
						if(!obj){
							$.app.method.tip('数据返回格式有误');
							return false;
						}
						//上次成功后执行回调函数
						if(typeof $.app.method.uploadOption.callback == 'function') return $.app.method.uploadOption.callback(obj);
					},
					submit: function(that){
						var check = true;
						//验证上传文件函数
						if(typeof $.app.method.uploadOption.check == 'function'){
							if(!$.app.method.uploadOption.check($('#' + $.app.method.uploadOption.id).find('form input[type="file"]:first').val())){
								check = false;
							}
						}
						//验证通过后直接上传
						if(check){
							$.app.method.uploadOption.onload = true;
							try{
								$(that).parent('form:first').trigger('submit');
							}catch(e){
								$('#' + $.app.method.uploadOption.id).remove();
								$.app.method.tip(e.message);
							}
						}
					}
				}
			};
	
			if(typeof option.action != 'string'){
				$.app.method.tip('未设置上传地址！');
				return false;
			}
			$.app.method.uploadOption = option;

			var html = [];
			html.push('<div id="' + $.app.method.uploadOption.id + '" style="display:block;margin:0;padding:0;width:0;height:0;overflow:hidden;">');
			html.push('<iframe onload="$.app.method.uploadOption.method.callback(this)" name="app-upload-when-click-form-submit-target-iframe" style="display:none"></iframe>');
			html.push('<form style="padding:15px 10px;text-align:center" method="post" action="' + $.app.method.uploadOption.action + '" enctype="multipart/form-data" target="app-upload-when-click-form-submit-target-iframe">');
			html.push('<input type="file" name="upload" onchange="$.app.method.uploadOption.method.submit(this)" />');
			html.push('<input type="hidden" name="callbackfunc" value="'+callbackurl+'" />');
			html.push('</form>');
			html.push('</div>');
			$(html.join('')).appendTo('body');
			//IE由于安全限制不允许直接用js选择文件并上传
			if ((navigator.userAgent.indexOf('MSIE') >= 0) && (navigator.userAgent.indexOf('Opera') < 0)){
				$.app.method.uploadOption.dialog = $('#' + $.app.method.uploadOption.id).dialog({title: '请选择文件',iconCls: 'icons-application-application_form_add',width: 280,modal: true});
			}else{
				$('#' + $.app.method.uploadOption.id).find('input[type="file"][name="upload"]:first').trigger('click');
			}
			return false;
		}
	});
})(jQuery);