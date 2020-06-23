$(function(){
	/* 复制 */
	$(".copy").on("click",function(){
		var code=$(this).data("code");
		location.href="copy://"+code;
		return !1;
	})
	
	/* 设置邀请码 */
	$(".code").on("click",function(){
		$("#code").focus();
	})
    
    var isiPad = /iPad/i.test(navigator.userAgent);
    var isiPhone = /iPhone|iPod/i.test(navigator.userAgent);
    var isAndroid = /Android/i.test(navigator.userAgent);
    var isWeixin = /MicroMessenger/i.test(navigator.userAgent);
    var isQQ = /QQ/i.test(navigator.userAgent);
    var isIOS = (isiPad || isiPhone);
    var isWeibo = /Weibo/i.test(navigator.userAgent);
    var isApp = (isAndroid || isIOS);
    if(isIOS){
        $("#code").on("keyup",function(){
            var code=$(this).val();
            var code_a=[];
                code_a=code.split('');
                console.log(code_a);
            var nums=code_a.length;
            var code_i=$(".code i");

            for(var i=0;i<6;i++){
                if(i<nums){
                    code_i.eq(i).html(code_a[i]); 
                }else{
                    code_i.eq(i).html('&nbsp;'); 
                }
                
            }
            
        })       
        
    }else{
        $("#code").on("keyup input",function(){
            var code=$(this).val();
            var code_a=[];
                code_a=code.split('');
                console.log(code_a);
            var nums=code_a.length;
            var code_i=$(".code i");

            for(var i=0;i<6;i++){
                if(i<nums){
                    code_i.eq(i).html(code_a[i]); 
                }else{
                    code_i.eq(i).html('&nbsp;'); 
                }
                
            }
            
        })
    }

	var isbuy=1;
	$(".submit").on("click",function(){
		if(!isbuy){
			return !1;
		}
		if(!uid || !token){
			layer.msg("信息错误");
			return !1;
		}

		var code = '';
		var code_i=$(".code i");
		for(var i=0;i<6;i++){
			if(code_i.eq(i).html()!=''&&code_i.eq(i).html()!='&nbsp;')
			{
				code+=code_i.eq(i).html();
			}
		}
		
		//var code=$("#code").val();


		if(code==''){
			layer.msg("请填写邀请码");
			return !1;
		}
		if(code.length<6){
			layer.msg("请填写6位邀请码");
			return !1;
		}
		isbuy=0;
		$.ajax({
			url:'/index.php?g=appapi&m=agent&a=setAgent',
			data:{uid:uid,token:token,code:code},
			type:'POST',
			dataType:'json',
			success:function(data){
				isbuy=1;
				if(data.code==0){
					layer.msg(data.msg,{},function(){
						location.reload();
					});
					
					return !1;
				}else{
					layer.msg(data.msg);
					return !1;
				}
			},
			error:function(){
				isbuy=1;
				layer.msg("设置失败");
				return !1;
			}
			
		})
	})
	
	$(".quit").on("click",function(){
		if(!isbuy){
			return !1;
		}
		if(!uid || !token){
			layer.msg("信息错误");
			return !1;
		}

		isbuy=0;
		$.ajax({
			url:'/index.php?g=appapi&m=agent&a=quit',
			data:{uid:uid,token:token},
			type:'POST',
			dataType:'json',
			success:function(data){
				isbuy=1;
				if(data.code==0){
					layer.msg(data.msg);
					setTimeout(function(){
						location.reload();
					},2000);
					
					return !1;
				}else{
					layer.msg(data.msg);
					return !1;
				}
			},
			error:function(){
				isbuy=1;
				layer.msg("退出失败");
				return !1;
			}
			
		})
	})
})