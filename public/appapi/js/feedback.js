
	function file_click(e)
	{
		var n= e.attr("data-index");
	 	upload(n);
	}
	function upload(index)
	{
		$('#upload').empty();
		var input = '<input type="file" id="ipt-file1" name="image" style="display:none" accept="image/*" />';
		$('#upload').html(input);
		var iptt=document.getElementById(index);
		if(window.addEventListener){ 
			iptt.addEventListener('change',function(){
					 ajaxFileUpload(index);
					$(".shadd[data-select="+index+"]").show();
			},false);
		}
		else
		{
			iptt.attachEvent('onchange',function()
			{
				ajaxFileUpload(index);  
                $(".shadd[data-select="+index+"]").show();
			});
		}
		$('#'+index).click();
	}
	function ajaxFileUpload(img)
	{
		var id= img;
		$("."+img).animate({"width":"100%"},700,function(){
			$.ajaxFileUpload
			({
				url: '/index.php?g=Appapi&m=feedback&a=upload',
				secureuri: false,
				fileElementId: id,
				data: { },
				dataType: 'html',
				success: function(data){
					data=data.replace(/<[^>]+>/g,"");
					var str=JSON.parse(data);
					if(str.ret==200)
					{
						
						$("#thumb").val(str.data.url);
                        $("#img_file1").attr("src",str.data.url);
                        $(".shadd[data-select="+img+"]").hide();
					}
					else
					{
                        $(".shadd[data-select="+img+"]").hide();
                        layer.msg(str.msg);
					}
				},
				error: function(data) 
				{
					
					layer.msg("上传失败");

                    $(".shadd[data-select="+img+"]").hide();
                    $("img[data-index="+img+"]").attr("src","/public/appapi/images/family/no2.jpg");
				}
			  })
			return true;
		});
	}
    function all_disabled(val) {
        $('input').each(function() {
            $(this).attr('disabled', val);
        });
        $('button').each(function() {
            $(this).attr('disabled', val);
        });
    }
    function check_input() {
        var content = $('#content').val();

        if ( content != '') {
            $("#save_btn").css({"background": "#8EE2D3"});
            $("#save_btn").prop("disabled", false);
        }else{
            $("#save_btn").css({"background": "#AAA8A8"});
            $("#save_btn").prop("disabled", true);
        }
    }

    function save() {

        var content = $('#content').val();
        var thumb = $('#thumb').val();
        var token = $('#token').val();
        var uid = encodeURIComponent($('#uid').val());
        var version = encodeURIComponent($('#version').val());
        var model = encodeURIComponent($('#model').val());

        var url2 = document.referrer;
        all_disabled(true);
        $('#save_btn').html('正在提交，请稍候');
        $.ajax( {
            type: "post",
            url: "./index.php?g=Appapi&m=Feedback&a=feedbackSave",
            dataType: "json",
            cache:false,
            timeout : 30000,
            data: {
                content: content,
                version: version,
                uid: uid,
                token: token,
                thumb: thumb,
                model: model
            },
            success:function(result) {
                if (result.status == 0) {
                    layer.msg('提交成功',{},function(){
                        location.reload();
                    });
                   
                }else{
                    layer.msg(result.errormsg);
                }
                all_disabled(false);
                $('#save_btn').html('点击反馈');
            },
            error : function() {
                layer.msg('网络异常');
                all_disabled(false);
                $('#save_btn').html('点击反馈');
            }
        });
    }
$(function(){
    $("#content").css({"height":$(window).height() *0.5+"px"});
    $('#save_btn').on('click', save);	
})