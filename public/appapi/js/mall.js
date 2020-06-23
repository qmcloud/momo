$(function(){
	
	$(".tab ul li").on("click",function(){
		$(this).siblings().removeClass("on");
		$(this).addClass("on");
		$(".tab_bd").hide().eq($(this).index()).show();
	})
    
	var isbuy=0;    
	/* vip购买 */
	$(".vip_length ul li").on("click",function(){
		$(this).siblings().removeClass("on");
		$(this).addClass("on");
        
        var coin=$(this).data("coin");
        $("#vip_total_coin").text(coin);
	})
	
    
	$(".vip_button").on("click",function(){
		layer.open({
			type: 1,
			offset: 'b',
			title: false,
			closeBtn: 0,
			anim:2,
			area: '100%',
			skin: 'layui-layer-nobg', //没有背景色
			shadeClose: true,
			content: $('.vip_buy_body')
		});
	})    
    

	$(".vip_submit_bd_r").on("click",function(){
		if(isbuy){
			return !1;
		}
		if(!uid || !token ){
			layer.msg("信息错误");
			return !1;
		}

        var vip_select=$(".vip_length li.on");
		var vip_id=vip_select.data("id");
		var vip_coin=vip_select.data("coin");
		var vip_length=vip_select.data("length");

		layer.confirm('您将花费'+vip_coin+name_coin+'，'+vip_txt+vip_length+'VIP会员', {
			title:'提示',
			btn: ['取消','确定'] //按钮
		}, function(index){
            layer.close(index);
		}, function(){
			isbuy=1;
			$.ajax({
				url:'/index.php?g=appapi&m=mall&a=buyvip',
				data:{uid:uid,token:token,vipid:vip_id},
				type:'POST',
				dataType:'json',
				success:function(data){
					isbuy=0;
					if(data.code==0){
						$("#user_coin").html(data.info.coin);
						$("#vip_endtime").html(data.info.endtime);
                        $(".vip_end").show();
                        
                        $(".vip_button").text('续费会员');
                        $(".vip_length_t").text('续费时长');
                        $(".vip_submit_bd_r_l").text('立即续费');
                        
                        vip_txt='续费';

						layer.msg(data.msg,{},function(){
							//layer.closeAll();
						});
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
		});
	})    

    /* 靓号 */
    var page_liang=2; 
    var isscroll_liang=true; 

    var scroll_liang=$(".liang");
    scroll_liang.scroll(function(){  
            var srollPos = scroll_liang.scrollTop();    //滚动条距顶部距离(页面超出窗口的高度)  		
            var totalheight = parseFloat(scroll_liang.height()) + parseFloat(srollPos);  
            if(($(document).height()-150) <= totalheight  && isscroll_liang) {  
                    isscroll_liang=false;
                    getliangmore()
            }  
    }); 

    function getliangmore(){
        $.ajax({
            url:'/index.php?g=appapi&m=mall&a=getliangmore',
            data:{'p':page_liang},
            type:'post',
            dataType:'json',
            success:function(data){
                if(data.info.nums>0){
                    var nums=data.info.nums;
                    var list=data.info.list;
                    var html='';
                    for(var i=0;i<nums;i++){
                        var v=list[i];
                        html+='<li>\
                           <div class="liang_id">ID:'+v['name']+'</div>\
                           <div class="liang_coin">'+v['coin_date']+'</div>\
                           <div class="liang_buy" data-id="'+v['id']+'" data-name="'+v['name']+'" data-coin="'+v['coin_date']+'" >购买</div>\
                        </li>';
                    }
                    
                    $(".liang .bd_content ul").append(html);
                }
                
                if(data.isscroll==1){
                    page_liang++;
                    isscroll_liang=true;
                }
            }
        })        
    }
        
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
		var liang_coin=_this.data("coin");
		var liang_name=_this.data("name");

		layer.confirm('您将花费'+liang_coin+'，购买靓号ID:'+liang_name, {
			title:'提示',
			btn: ['取消','确定'] //按钮
		}, function(index){
            layer.close(index);
		}, function(){
			isbuy=1;
			$.ajax({
				url:'/index.php?g=appapi&m=mall&a=buyliang',
				data:{uid:uid,token:token,liangid:liang_id},
				type:'POST',
				dataType:'json',
				success:function(data){
					isbuy=0;
					if(data.code==0){
						$("#user_coin").html(data.info.coin);
                        _this.parent("li").remove();
						layer.msg(data.msg,{},function(){
							//layer.closeAll();
						});
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
		});
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
		var car_coin=_this.data("coin");
		var car_name=_this.data("name");

		layer.confirm('您将花费'+car_coin+name_coin+'，购买坐骑'+car_name, {
			title:'提示',
			btn: ['取消','确定'] //按钮
		}, function(index){
            layer.close(index);
		}, function(){
			isbuy=1;
			$.ajax({
				url:'/index.php?g=appapi&m=mall&a=buycar',
				data:{uid:uid,token:token,carid:car_id},
				type:'POST',
				dataType:'json',
				success:function(data){
					isbuy=0;
					if(data.code==0){
						$("#user_coin").html(data.info.coin);
						layer.msg(data.msg,{},function(){
							//layer.closeAll();
						});
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
		});
	})  
})