$(function(){
	
	$(".tab ul li").on("click",function(){
		$(this).siblings().removeClass("on");
		$(this).addClass("on");
		$(".tab_bd").hide().eq($(this).index()).show();
	})
    
	var isbuy=0;    
  

    /* 靓号 */
	$(".liang .bd_content ul").on("click",'.liang_buy',function(){
		if(isbuy){
			return !1;
		}
		if(!uid || !token ){
			layer.msg("信息错误");
			return !1;
		}

        var _this=$(this);
		var liang_id=_this.data("id");
		var liang_state=_this.data("state");

		isbuy=1;
		$.ajax({
			url:'/index.php?g=appapi&m=equipment&a=setliang',
			data:{uid:uid,token:token,liangid:liang_id,state:liang_state},
			type:'POST',
			dataType:'json',
			success:function(data){
				isbuy=0;
				if(data.code==0){
					if(liang_state==0){
						$(".liang_buy").html("使用");
						$(".liang_buy").data("state",'0');
						
						_this.html("卸载");
						_this.data("state",'1');
					}else{
						_this.html("使用");
						_this.data("state",'0');
					}
					
					layer.msg(data.msg);
					return !1;
				}else{
					layer.msg(data.msg);
					return !1;
				}
			},
			error:function(){
				isbuy=0;
				layer.msg("操作失败");
				return !1;
			}
			
		})
	})   
    
    /* 坐骑 */
	$(".car .bd_content ul").on("click",'.car_buy',function(){
		if(isbuy){
			return !1;
		}

		if(!uid || !token ){
			layer.msg("信息错误");
			return !1;
		}

        var _this=$(this);
		var car_id=_this.data("id");
		var car_status=_this.data("status");

		isbuy=1;
		$.ajax({
			url:'/index.php?g=appapi&m=equipment&a=setcar',
			data:{uid:uid,token:token,carid:car_id,status:car_status},
			type:'POST',
			dataType:'json',
			success:function(data){
				isbuy=0;
				if(data.code==0){
					if(car_status==0){
						$(".car_buy").html("使用");
						$(".car_buy").data("status",'0');
						
						_this.html("卸载");
						_this.data("status",'1');
					}else{
						_this.html("使用");
						_this.data("status",'0');
					}
					
					
					layer.msg(data.msg);
					return !1;
				}else{
					layer.msg(data.msg);
					return !1;
				}
			},
			error:function(){
				isbuy=0;
				layer.msg("购买失败");
				return !1;
			}
			
		})
	})  
})