    $(".shad,.shad2,.shad3").css({"height":$(".img-sfz").height()*71/92+"px"});
	function file_click(e)
	{
		var n= e.attr("data-index");
	 	upload(n);
	}
	function upload(index)
	{
		$('#upload').empty();
		var input = '<input type="file" id="ipt-file1" name="image" style="display:none" accept="image/*" /><input type="file" id="ipt-file2" name="image" style="display:none" accept="image/*" /><input type="file" id="ipt-file3" name="image" style="display:none" accept="image/*" />';
		$('#upload').html(input);
		var iptt=document.getElementById(index);
		if(window.addEventListener){ 
			iptt.addEventListener('change',function(){
					 ajaxFileUpload(index);
					/*   var arr_img=new Array("/public/appapi/images/family/identity_face.png","/public/appapi/images/family/identity_back.png","/public/appapi/images/family/identity_handle.png");
					var sub=index.substr(8,1);
					$(".img-sfz[data-index="+index+"]").attr("src",arr_img[sub-1]); */
					$(".shadd[data-select="+index+"]").show();
			},false);
		}
		else
		{
			iptt.attachEvent('onchange',function()
			{
				ajaxFileUpload(index);  
				/*  var arr_img=new Array("/public/appapi/images/family/identity_face.png","/public/appapi/images/family/identity_back.png","/public/appapi/images/family/identity_handle.png");
                var sub=index.substr(8,1);
                $(".img-sfz[data-index="+index+"]").attr("src",arr_img[sub-1]); */
                $(".shadd[data-select="+index+"]").show();
			});
		}
		$('#'+index).click();
	}
	function ajaxFileUpload(img)
	{
		var id= img;
		var uid=$("#uid").val();
		$("."+img).animate({"width":"100%"},700,function(){
			$.ajaxFileUpload
			({
				url: '/index.php?g=Appapi&m=Family&a=upload',
				secureuri: false,
				fileElementId: id,
				data: { },
				dataType: 'html',
				success: function(data){
					data=data.replace(/<[^>]+>/g,"");
					var str=JSON.parse(data);
					if(str.ret==200)
					{
						
						$("#"+id+"v").val(str.data.url);
						if(id=="ipt-file1")//勋章
						{
							$("#img_file1").attr("src",str.data.url);
							$(".shadd[data-select="+img+"]").hide();
							$(".box-upload[data-index="+img+"]").show();
							$(".box-upload[data-index="+img+"] img").attr("src","/public/appapi/images/family/ok2.jpg");
						}
						else if(id=="ipt-file2")
						{
							$("#img_file2").attr("src",str.data.url);
							$(".shadd[data-select="+img+"]").hide();
							$(".box-upload[data-index="+img+"]").show();
							$(".box-upload[data-index="+img+"] img").attr("src","/public/appapi/images/family/ok2.jpg");
						}
						else if(id=="ipt-file3")
						{
							
							$("#img_file3").attr("src",str.data.url);
							$(".shadd[data-select="+img+"]").hide();
							$(".box-upload[data-index="+img+"]").show();
							$(".box-upload[data-index="+img+"] img").attr("src","/public/appapi/images/family/ok2.jpg");
						}
					}
					else
					{
		   
						if(id=="ipt-file1")//勋章
						{
							$(".shadd[data-select="+img+"]").hide();
							$(".box-upload[data-index="+img+"]").show();
							$(".box-upload[data-index="+img+"] img").attr("src","/public/appapi/images/family/no2.jpg");
						}
						else if(id=="ipt-file2")
						{
							$(".shadd[data-select="+img+"]").hide();
							$(".box-upload[data-index="+img+"]").show();
							$(".box-upload[data-index="+img+"] img").attr("src","/public/appapi/images/family/no2.jpg");
						}
						else if(id=="ipt-file3")
						{
							$(".shadd[data-select="+img+"]").hide();
							$(".box-upload[data-index="+img+"]").show();
							$(".box-upload[data-index="+img+"] img").attr("src","/public/appapi/images/family/no2.jpg");
						}
					}
				},
				error: function(data) 
				{
					
					layer.msg("上传失败");
					if(id=="ipt-file1")//勋章
					{
						$(".shadd[data-select="+img+"]").hide();
						$(".box-upload[data-index="+img+"]").show();
						$(".box-upload[data-index="+img+"] img").attr("src","/public/appapi/images/family/no2.jpg");
					}
					else if(id=="ipt-file2")
					{
						$(".shadd[data-select="+img+"]").hide();
						$(".box-upload[data-index="+img+"]").show();
						$(".box-upload[data-index="+img+"] img").attr("src","/public/appapi/images/family/no2.jpg");
					}
					else if(id=="ipt-file3")
					{
						$(".shadd[data-select="+img+"]").hide();
						$(".box-upload[data-index="+img+"]").show();
						$(".box-upload[data-index="+img+"] img").attr("src","/public/appapi/images/family/no2.jpg");
					}
				}
			  })
			return true;
		});
	}
	function apply_judge()
	{
	 	var file1v=$("#ipt-file1v").val();
		var file2v=$("#ipt-file2v").val();
		var file3v=$("#ipt-file3v").val();
		var name=$.trim($("#name").val());
		var fullname=$.trim($("#fullname").val());
		var carded=$.trim($("#carded").val());
		var reg_realName=/^(?=.*\d.*\b)/;
		var reg_identity=/^(^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$)|(^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])((\d{4})|\d{3}[Xx])$)$/;
		if(hasEmoji(name)){
			layer.msg("家族名称不能含有表情");
			return !1;
		}
		if(hasEmoji(fullname)){
			layer.msg("真实姓名不能含有表情");
			return !1;
		}
		if(hasEmoji(carded)){
			layer.msg("证件号码不能含有表情");
			return !1;
		}
		if(name=="")
		{
			layer.msg("请填写家族名称");
		}else if(fullname==""||reg_realName.test(fullname)==true)
		{
		  layer.msg("请正确填写真实姓名");
		}
		else if(carded=="" ||reg_identity.test(carded)==false)
		{
		  layer.msg("请输入正确的身份证号");
		}
		else if(file1v==""||file2v==""||file3v=="")
		{
			layer.msg("请将需要的三张照片上传完整");
		} 
		else 
		{
			apply();
		}
	}
	//apply 申请家族提交
	//各种参数apply_avatar 家族形象图 apply_badge家族勋章图标 uid申请人ID号 apply_pos身份证正面 apply_side身份证反面
	function apply()
	{
		$.ajax({
			cache: true,
			type: "POST",
			url:'/index.php?g=Appapi&m=Family&a=add',
			data:$('#apply_form').serializeArray(),// 你的formid
			dataType:'json',
			async: false,
			error: function(request)
			{
				layer.msg("审核信息提交错误！");
			},
			success: function(data)
			{
				if(data.state==0){
					layer.msg(data['msg']);
				}else{
					layer.alert('你的家族'+data['name']+'申请成功', 
					{
						skin: 'layui-layer-molv' //样式类名
						,closeBtn: 0
					}, function(){
						window.location.href="/index.php?g=Appapi&m=Family&a=home&uid="+data['uid']+"&token="+data['token'];
					});
				}
			}
		});
	}


	function examine_edit(_this,touid,type,pass)
	{
		isbuy=0;
		$.ajax({
			url:'/index.php?g=Appapi&m=Family&a=examine_edit',
			type:'POST',
			data:{uid:uid,token:token,familyid:familyid,touid:touid,type:type,pass:pass},
			dataType:'json',
			success:function(data){
				isbuy=1;
				if(data.code==0){
					layer.msg(data.msg);
					_this.parents("li").remove();
				}else{
					layer.msg(data.msg);
					return !1;
				}
			},
			error:function(){
				isbuy=1;
			}
		})
	}
	
	function signout_post(_this,touid,type,reason)
	{
		isbuy=0;
		$.ajax({
			url:'/index.php?g=Appapi&m=Family&a=signout_post',
			type:'POST',
			data:{uid:uid,token:token,familyid:familyid,touid:touid,type:type,reason:reason},
			dataType:'json',
			success:function(data){
				isbuy=1;
				if(data.code==0){
					layer.msg(data.msg);
					_this.parents("li").remove();
				}else{
					layer.msg(data.msg);
					return !1;
				}
			},
			error:function(){
				isbuy=1;
			}
		})
	}
	
	function fieldEmoji(str){
		var content=str;
		var ranges = [  
            '\ud83c[\udf00-\udfff]',  
            '\ud83d[\udc00-\ude4f]',  
            '\ud83d[\ude80-\udeff]'  
        ];  
        emojireg = content .replace(new RegExp(ranges.join('|'), 'g'), '');  
		
		return emojireg;
	}

	function hasEmoji(str){
		var ranges = [  
            '\ud83c[\udf00-\udfff]',  
            '\ud83d[\udc00-\ude4f]',  
            '\ud83d[\ude80-\udeff]'  
        ];  
		var part=new RegExp(ranges.join('|'), 'g');
       
		if(part.test(str)){
			return true;
		}else{
			return false;
		}
	}
$(function(){ 
	var isbuy=1;
	/* 家族中心 */

	$(".attended .search_clear").on("click",function(){
		$(".attended #key").val("");
	})
	$(".attended .search_btn").on("click",function(){
		if(!isbuy){
			return !1;
		}
		var key=$(".attended #key").val();
		if(hasEmoji(key)){
			layer.msg("不能含有表情");
			return !1;
		}
		if(key==''){
			layer.msg("请输入签约家族ID");
			return !1;
		}
		isbuy=0;
		$.ajax({
			url:'/index.php?g=Appapi&m=Family&a=attended_search',
			type:'POST',
			data:{uid:uid,key:key},
			dataType:'json',
			success:function(data){
				isbuy=1;
				if(data.code==0){
					var list=data.info,length=list.length,html='';
					for(var i=0;i<length;i++){
						html+='<li>';
						html+='<a href="index.php?g=Appapi&m=Family&a=detail&familyid='+list[i]['id']+'&uid='+uid+'&token='+token+'">';
						html+='	<div class="thumb">\
									<img src="'+list[i]['badge']+'">\
								</div>';
						html+='	<div class="info">\
									<p class="info-title">'+list[i]['name']+'</p>\
                                    <p class="info-des2 ellipsis">'+list[i]['briefing']+'</p>\
									<p class="info-des"><span>成员：'+list[i]['count']+'人</span><span>ID：'+list[i]['id']+'</span></p>\
								</div>';
						html+='	<div class="action">';
						// if(list[i]['isstatus']==0){
							// html+='<span class="no" data-familyid="'+list[i]['id']+'">已申请</span>'
						// }else if(list[i]['isstatus']==2){
							// html+='<span class="no" data-familyid="'+list[i]['id']+'">已加入</span>'
						// }else{
							html+='<span class="ok" data-familyid="'+list[i]['id']+'">申请</span>';
						// }
	
						html+='	</div>';
						html+='	</a>';
						html+='</li>';
					}
					$(".attended .user-list ul").html(html);
					if(length==0){
						//layer.msg('暂无相关家族');
                        html='<div class="family_empty">你搜索的家族不存在</div>';
                        $(".attended .user-list ul").html(html);
					}
				}else{
					layer.msg(data.msg);
					return !1;
				}
			},
			error:function(){
				isbuy=1;
			}
		})
	})

	$(".attended .list_title .reload").on("click",function(){
		if(!isbuy){
			return !1;
		}
		isbuy=0;
		$.ajax({
			url:'/index.php?g=Appapi&m=Family&a=attended_reload',
			type:'POST',
			data:{uid:uid},
			dataType:'json',
			success:function(data){
				isbuy=1;
				if(data.code==0){
					var list=data.info,length=list.length,html='';
					for(var i=0;i<length;i++){
						html+='<li>';
						html+='<a href="index.php?g=Appapi&m=Family&a=detail&familyid='+list[i]['id']+'&uid='+uid+'&token='+token+'">';
						html+='	<div class="thumb">\
									<img src="'+list[i]['badge']+'">\
								</div>';
						html+='	<div class="info">\
									<p class="info-title">'+list[i]['name']+'</p>\
									<p class="info-des"><span>成员：'+list[i]['count']+'人</span><span>ID：'+list[i]['id']+'</span></p>\
								</div>';
						html+='	<div class="action">';
						// if(list[i]['isstatus']==0){
							// html+='<span class="no" data-familyid="'+list[i]['id']+'">已申请</span>'
						// }else if(list[i]['isstatus']==2){
							// html+='<span class="no" data-familyid="'+list[i]['id']+'">已加入</span>'
						// }else{
							html+='<span class="ok" data-familyid="'+list[i]['id']+'">申请</span>';
						// }
	
						html+='	</div>';
						html+='	</a>';
						html+='</li>';
					}
					$(".attended .user-list ul").html(html);
				}else{
					layer.msg(data.msg);
					return !1;
				}
			},
			error:function(){
				isbuy=1;
			}
		})
	})
	
	
	/* 申请进度 撤销 */
	$(".apply_wait .revoke").on("click",function(){
		if(!isbuy){
			return !1;
		}
		if(!uid || !token ){
			layer.msg("信息错误");
			return !1;
		}
		isbuy=0;
		$.ajax({
			url:'/index.php?g=Appapi&m=Family&a=revoke',
			type:'POST',
			data:{uid:uid,token:token},
			dataType:'json',
			success:function(data){
				isbuy=1;
				if(data.code==0){
					layer.msg(data.msg,{},function(){
						window.location.reload();
					});
				}else{
					layer.msg(data.msg);
					return !1;
				}
			},
			error:function(){
				isbuy=1;
			}
		})
	})
	/* 设置家族默认分成 */
	$(".home .edit_divide").on("click",function(){
		if(!isbuy){
			return !1;
		}
		if(!uid || !token ||!familyid){
			layer.msg("信息错误");
			return !1;
		}
		var _this=$(this);
		var divide=_this.data("divide");
		layer.prompt({
            title: '家族默认分成(%)', 
            formType: 0,
            value:divide
        }, function(pass, index){
			if(hasEmoji(pass)){
				layer.msg("不能含有表情");
				return !1;
			}
			layer.close(index);
			isbuy=0;
			$.ajax({
				url:'/index.php?g=Appapi&m=Family&a=setdivide',
				type:'POST',
				data:{uid:uid,token:token,familyid:familyid,divide:pass},
				dataType:'json',
				success:function(data){
					isbuy=1;
					if(data.code==0){
						layer.msg(data.msg);
						_this.data("divide",pass);
						$("#divide_family").text(pass);
					}else{
						layer.msg(data.msg);
						return !1;
					}
				},
				error:function(){
					isbuy=1;
				}
			})
		});
	})
	
	/* 修改简介 */
	$(".setdes .setdes_post").on("click",function(){
		if(!isbuy){
			return !1;
		}
		if(!uid || !token ||!familyid){
			layer.msg("信息错误");
			return !1;
		}
		var des=$(".setdes #des").val();
		
		if(hasEmoji(des)){
			layer.msg("不能含有表情");
			return !1;
		}

		isbuy=0;
		$.ajax({
			url:'/index.php?g=Appapi&m=Family&a=setdes_post',
			type:'POST',
			data:{uid:uid,token:token,familyid:familyid,des:des},
			dataType:'json',
			success:function(data){
				isbuy=1;
				if(data.code==0){
					layer.msg(data.msg);
				}else{
					layer.msg(data.msg);
					return !1;
				}
			},
			error:function(){
				isbuy=1;
			}
		})
	})
	
	/* 签约审核 */
	$(".examine .user-list ul li .action span").on("click",function(){
		if(!isbuy){
			return !1;
		}
		if(!uid || !token ||!familyid){
			layer.msg("信息错误");
			return !1;
		}
		var _this=$(this);
		var touid=_this.data("touid");
		var type=_this.data("type");
		
		if(type=="1")
		{
			
			layer.prompt({title: '请输入拒绝理由', formType: 2}, function(pass, index){
				if(hasEmoji(pass)){
					layer.msg("不能含有表情");
					return !1;
				}
				layer.close(index);
				examine_edit(_this,touid,type,pass);
			});
		}
		else
		{
			var pass="";
			examine_edit(_this,touid,type,pass);
		}
	})
	
	/* 家族成员分成设置 */
	$(".member .user-list ul li .action span.ok").on("click",function(){
		if(!isbuy){
			return !1;
		}
		if(!uid || !token ||!familyid){
			layer.msg("信息错误");
			return !1;
		}
		var _this=$(this);
		var divide=_this.data("divide");
		var touid=_this.data("touid");
		layer.prompt({title: '设置分成(%)', formType: 0,value:divide}, function(pass, index){
			if(hasEmoji(pass)){
				layer.msg("不能含有表情");
				return !1;
			}
			layer.close(index);
			isbuy=0;
			$.ajax({
				url:'/index.php?g=Appapi&m=Family&a=member_setdivide',
				type:'POST',
				data:{uid:uid,token:token,familyid:familyid,touid:touid,divide:pass},
				dataType:'json',
				success:function(data){
					isbuy=1;
					if(data.code==0){
						layer.msg(data.msg);
						_this.data("divide",pass);
						_this.parents("li").find(".divide_family").text(pass);
					}else{
						layer.msg(data.msg);
						return !1;
					}
				},
				error:function(){
					isbuy=1;
				}
			})
		});
	})
	/* 家族成员踢出 */
	$(".member .user-list ul li .action span.no").on("click",function(){
		if(!isbuy){
			return !1;
		}
		if(!uid || !token ||!familyid){
			layer.msg("信息错误");
			return !1;
		}
		var _this=$(this);
		var touid=_this.data("touid");
		var name=_this.data("name");
		layer.confirm('要把成员'+name+'踢出家族吗？',{
            title:'提示',
            btn: ['取消','确定'] //按钮
        }, function(index){
            layer.close(index);
		}, function(index){
			layer.close(index);
			
			layer.prompt({title: '踢出理由', formType: 2}, function(pass, index){
				if(hasEmoji(pass)){
					layer.msg("不能含有表情");
					return !1;
				}
				layer.close(index);
				isbuy=0;
				$.ajax({
					url:'/index.php?g=Appapi&m=Family&a=member_del',
					type:'POST',
					data:{uid:uid,token:token,familyid:familyid,touid:touid,reason:pass},
					dataType:'json',
					success:function(data){
						isbuy=1;
						if(data.code==0){
							layer.msg(data.msg);
							_this.parents("li").remove();
						}else{
							layer.msg(data.msg);
							return !1;
						}
					},
					error:function(){
						isbuy=1;
					}
				})
			});

		});
	})
	/*  申请签约 */
	$(".detail_sign .sign").on("click",function(){
		if(!isbuy){
			return !1;
		}
		if(!uid || !token || !familyid){
			layer.msg("信息错误");
			return !1;
		}
		isbuy=0;
		$.ajax({
			url:'/index.php?g=Appapi&m=Family&a=attended_add',
			type:'POST',
			data:{uid:uid,token:token,familyid:familyid},
			dataType:'json',
			success:function(data){
				isbuy=1;
				if(data.code==0){
					layer.msg(data.msg,{},function(){
						window.location.href="/index.php?g=Appapi&m=Family&a=home&uid="+uid+"&token="+token;
					});
				}else{
					layer.msg(data.msg);
					return !1;
				}
			},
			error:function(){
				isbuy=1;
			}
		})
	})
	
	/* 申请签约 撤销 */
	$(".attended_wait .revoke").on("click",function(){
		if(!isbuy){
			return !1;
		}
		if(!uid || !token ){
			layer.msg("信息错误");
			return !1;
		}
		isbuy=0;
		$.ajax({
			url:'/index.php?g=Appapi&m=Family&a=attended_revoke',
			type:'POST',
			data:{uid:uid,token:token},
			dataType:'json',
			success:function(data){
				isbuy=1;
				if(data.code==0){
					layer.msg(data.msg,{},function(){
						window.location.reload();
					});
				}else{
					layer.msg(data.msg);
					return !1;
				}
			},
			error:function(){
				isbuy=1;
			}
		})
	})
	
	/* 解除签约 */
	$(".relieve .relievebtn").on("click",function(){
		if(!isbuy){
			return !1;
		}
		if(!uid || !token ){
			layer.msg("信息错误");
			return !1;
		}
		isbuy=0;
		$.ajax({
			url:'/index.php?g=Appapi&m=Family&a=retreat',
			type:'POST',
			data:{uid:uid,token:token},
			dataType:'json',
			success:function(data){
				isbuy=1;
				if(data.code==0){
					layer.msg(data.msg);
				}else{
					layer.msg(data.msg);
					return !1;
				}
			},
			error:function(){
				isbuy=1;
			}
		})
	})
	
	/* 退出审核 */
	$(".signout .user-list ul li .action span").on("click",function(){
		if(!isbuy){
			return !1;
		}
		if(!uid || !token ||!familyid){
			layer.msg("信息错误");
			return !1;
		}
		var _this=$(this);
		var touid=_this.data("touid");
		var name=_this.data("name");
		var type=_this.data("type");
		
		if(type==0)
		{
			layer.prompt({title: '请输入拒绝理由', formType: 2}, function(pass, index){
				if(hasEmoji(pass)){
					layer.msg("不能含有表情");
					return !1;
				}
				layer.close(index);
				signout_post(_this,touid,type,pass);
			});
		}
		else
		{
			var pass="";
			signout_post(_this,touid,type,pass);
		}

		
	})
})