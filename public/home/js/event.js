var JsInterface={
	giftEffects:1,
	roomNumObj:$(".MR-count .online .info cite"),
	userListNum:$(".MR-online .user cite"),
	userCite:$(".MR-online .nav-tab cite"),
	chatFromSocket: function(data) {
		var data = WlTools.strTojson(data);
		var msgObject = data.msg[0];
		var msgtype = msgObject.msgtype;
		var msgaction = msgObject.action;
		var msgmethod = msgObject._method_;
		if(msgmethod=='SendMsg'){ //聊天信息
			this.sendMsg(msgObject);
		}else if(msgmethod=='SendGift'){ //赠送礼物
			this.sendGift(msgObject);
		}else if(msgmethod=='SendHorn'){ //喇叭
			this.sendHorn(msgObject);
		}else if(msgmethod=='SystemNot'||msgmethod=='ShutUpUser'){ //系统信息//禁言 踢人
			this.systemNot(msgObject);
		}else if(msgmethod=='StartEndLive'){ //开关播
			this.showEndRecommend(msgObject);
		}else if(msgmethod=='disconnect'){ //关播
			this.disconnect(msgObject);
		}else if(msgmethod=='requestFans'){ //关播
			var nums=msgObject.ct.data.info.nums;
		}else if(msgmethod=='KickUser'){ //踢人
			this.KickUser(msgObject);
		}else if(msgmethod=='SendBarrage'){ //弹幕
			this.sendBigHorn(msgObject);
		}else if(msgmethod=='light'){ //弹幕
			this.setLight(msgObject);
		}else if(msgmethod=='changeLive'){ //切换房间
			this.changeType(msgObject);
		}else if(msgmethod=='ConnectVideo'){ //用户连麦
			this.connectVideo(msgObject);
		}else if(msgmethod=='LiveConnect'){ //主播连麦
			this.liveConnect(msgObject);
		}else if(msgmethod=='LivePK'){ //主播PK
			this.livePK(msgObject);
		}
	},
	createRoom:function(create,choice){
		var str = JSON.parse(choice);
		//var data=Object.assign(str,create); //js 合并对象
		var data= $.extend(str, create); //jq 合并对象
        
		$.ajax({
			type: "GET",
			url:"./index.php?m=show&a=createRoom",
			data:data,
			success:function(data){
				var result =JSON.parse(data);
				if(result.state==0)
				{
					if(str['obs']=="1")
					{
						alertMessage("OBS推流成功");
					}
					else{
						gotoPlayVideo();
					}
					var livetype=JSON.stringify(_DATA.config.live_type);
					if(result.data!=1 && livetype.indexOf("3") >= 0){
						$("#changetype").show();  //让更改房间类型按钮显示
					}
					/* 更新直播信息 */
					$.ajax({
						url:'./index.php?m=show&a=live',
						data:{uid:_DATA.anchor.id},
						dataType:'json',
						success:function(data){
							if(data.error==0){
								Interface.startEndLive(2);
								_DATA.live=data.data;
								Rank.adddate();
							}else{
								alert(data.msg);
							}
						}
					})
				}
				else{
					alertMessage("接口请求失败"+result.msg);
				}
			},
			error:function(data){
				alertMessage("接口请求失败");
			}
		});
	},
	stopRoom:function(data){
        
        var stream=(_DATA.live && _DATA.live.stream) || 0;
        var data2={stream:stream};
		$.ajax({
			type: "POST",
			url:"/index.php?m=show&a=stopRoom",
			data:data2,
            dataType:'json',
			success:function(data){
				var result = data;
				if(result.code==0){
                    layer.msg(result.msg,{},function(){
                        location.href='/';
                    });
                    
				}else{
                    layer.msg("接口请求失败:"+result.msg);
				}
			},
			error:function(data){
				layer.msg("接口请求失败");
			}
		});
	},
	/*超管关闭房间*/
	superStopRoom:function()
	{
		setTimeout("window.location.href='/'",5000) //秒后执行
		layer.alert("该直播间涉嫌违规，已被停播", 
		{
			skin: 'layui-layer-molv' //样式类名
			,closeBtn: 0,
			shift: 5,
			icon:2
		}, function()
		{
			window.location.href="./";
		});
	},
	sendMsg:function(data){
		var msgtype = data.msgtype;
		var msgaction = data.action;
		var _method_ = data._method_;
		if(msgtype==0){
			this.enterRoom(data);
		}else if(msgtype==2){
				this.sendChat(data);
		}
	},
	sendChat: function(data){
		//html='<li><span class="time">'+data.timestamp+'</span>'
		var html='<li>';
				
				if(data.vip_type > 0){
					html+='<i class="ICON-medal vip" title="VIP"><img class="medal-img" src="/public/home/images/vip_'+data.vip_type+'.png"></i>';
				}
				
				if(data.liangname > 0){
					html+='<i class="ICON-medal" title="靓号"><img class="medal-img" src="/public/home/images/liang.png"></i>';
				}
				html+='<img class="level" src="'+_DATA.level[data.level]['thumb']+'">';
				html+='<span class="user-name" data-name="'+data.uname+'" data-id="'+data.uid+'">'+data.uname+'</span>：'+data.ct;
				html+='</li>';
		$("#LF-chat-msg-area .MR-chat .boarder ul").append(html);
		Chat.resetsH();		
	},
	setLight:function(){
		var e = $("#player-praises .bubble"),
		t = e.width(),
		r = e.height(),
		i = 32,
		s = 26,
		o = 80,
		u = ["FF5D31", "FF7043", "FF9800", "F9A825", "F57F17", "FFCA28"],
		a = '<svg viewBox="-1 -1 27 27"><path class="svgpath" style="fill:$fill$;stroke: #FFF; stroke-width: 1px;" d="M11.29,2C7-2.4,0,1,0,7.09c0,4.4,4.06,7.53,7.1,9.9,2.11,1.63,3.21,2.41,4,3a1.72,1.72,0,0,0,2.12,0c0.79-.64,1.88-1.44,4-3,3.09-2.32,7.1-5.55,7.1-9.94,0-6-7-9.45-11.29-5.07A1.15,1.15,0,0,1,11.29,2Z"/></svg>',
		f = function() {
				if (e.find("svg").length > o) return;
				var n = u[Math.floor(Math.random() * u.length)],
				r = $(a.replace("$fill$", "#" + n));
				this.startx = t / 2 - 10,
				this.pos = Math.random() * Math.PI,
				this.hz = Math.random() * 20 + 10,
				this.zf = Math.random() * 15 + 10,
				this.opacityStart = Math.random() * 10 + 10,
				this.y = 0,
				this.$el = r,
				this.setStyle(),
				e.append(r),
				this.run()
		};
		f.prototype.setStyle = function() {
				var e = this.startx + Math.sin(this.pos + this.y / this.hz) * this.zf,
				t = 1 - Math.max((this.y - this.opacityStart) / (r - this.opacityStart), 0),
				n = Math.min(this.y * 2 / r * (i - s) + s, i);
				this.$el.css({
						left: e,
						bottom: this.y,
						opacity: t
				}).width(n).height(n)
		},
		f.prototype.run = function() {
				var e = this,
				t = Math.random() * 20 + 10,
				n = $.now(),
				i = setInterval(function() {
						var s = $.now();
						e.y += Math.round((s - n) / t),
						n = s,
						e.setStyle(),
						e.y >= r && (e.$el.remove(), clearTimeout(i))
				},
				t)
		}
		new f;
	},
	enterRoom:function(data){
		var html='<li class="enter">欢迎<span class="all-name"><span class="user-name" data-name="'+data.ct.user_nicename+'" data-id="'+data.ct.id+'">'+data.ct.user_nicename+'</span></span>进入频道</li>';
		$("#LF-chat-msg-area .MR-chat .boarder ul").append(html);
		Chat.resetsH();	
		if(data.ct.car_id>0){
			this.carExecuteQueue(data.ct.car_swf,data.ct.car_swftime,data.ct.car_words);
		}

		// 请求用户列表
		User.getOnline();
	},
	disconnect:function(data){
	},
	sendHorn:function(data){
		var action=data.ct.action;
		if(action=='sendsmallhorn'){
			this.sendSmallHorn(data);
		}else if(action=='sendbighorn'){
			this.sendBigHorn(data);
		}
	},
	sendSmallHorn:function(data){
		
	},
	sendBigHorn:function(data){
		var html='<a class="notice-horn" href="/'+data.touid+'" target="_blank">\
				<span class="name">'+data.uname+'</span>\
				<span class="mid">：</span>\
				<span class="say">'+data.ct.content+'</span>\
				<span class="link">['+_DATA.anchor.user_nicename+'的直播频道]</span>\
			</a>';
			$(".MR-msg-notice .msg-content").html(html);
			$(".MR-msg-notice").show();
			setTimeout(function(){
				$(".MR-msg-notice").hide();
				$(".MR-msg-notice .msg-content").html('');
			},5000)
	},
	showEndRecommend:function(data){
		var msgmethod = data.action;
		if(msgmethod==18)
		{
			Video.endRecommend();
		}
		else
		{
			Video.statRecommend();
		}
	},
	KickUser:function(data)
	{
		this.systemNot(data);
		if(data.touid==_DATA.user.id)
		{
			setTimeout("window.location.href='/'",5000) //秒后执行
			layer.alert("你已经被踢出房间", 
			{
				skin: 'layui-layer-molv' //样式类名
				,closeBtn: 0,
				shift: 5,
				icon:2
			}, function()
			{
				window.location.href="./";
			});
		}
		
	},
	systemNot:function(data){
		var html='<li><span class="system_a">直播间消息：</span><span class="system_name">'+data.ct+'</span></li>';
		$("#LF-chat-msg-area .MR-chat .boarder ul").append(html);
		$('#LF-chat-msg-area .MR-chat .boarder').scrollTop( $('#LF-chat-msg-area .MR-chat .boarder')[0].scrollHeight );
		/* Chat.resetsH(); */
	},
	sendGift:function(data){
		var roomnum=data.roomnum;
		var anchorid=_DATA.anchor.id;
        if(roomnum!=anchorid){
            return !1;
        }
		var html='<li>';
				if(data.vip_type > 0){
					html+='<i class="ICON-medal vip" title="VIP"><img class="medal-img" src="/public/home/images/vip_'+data.vip_type+'.png"></i>';
				}
				
				if(data.liangname > 0){
					html+='<i class="ICON-medal" title="靓号"><img class="medal-img" src="/public/home/images/liang.png"></i>';
				}
				html+='<img class="level" src="'+_DATA.level[data.level]['thumb']+'">';
				html+='<span class="user-name" data-name="'+data.uname+'" data-id="'+data.uid+'">'+data.uname+'</span>';
				html+='<i class="mlr-5">赠送</i>';
				html+=data.ct.giftname+'<img src="'+data.ct.gifticon+'">';
				data.ct.giftcount>1? html+='('+data.ct.giftname+'*'+data.ct.giftcount+')':'';
				html+='</li>';
		$(".msg-gift .MR-chat .boarder ul").append(html);
		$('.MR-msg .MR-chat .boarder').scrollTop( $('.MR-msg .MR-chat .boarder')[0].scrollHeight );
        if(data.ct.type==1 && data.ct.swftype==1){
            data.ct.swf=data.ct.gifticon;
        }
		window.HJ_PopBox.gift(data);

		Rank.adddate(); //刷新排行榜
		
	},
	giftExecuteQueue: function(data){//执行队列
		var giftId = data.ct.giftid;
		var giftinfo=_DATA.gift[giftId];
		var runTime=0,type=0;
		//记录礼物信息
		if(giftinfo['swf']!=''&& giftinfo['swf']!=null && giftinfo['swftime']!='' ){
			runTime=  giftinfo['swftime'] *1000;
			type=1;
		}else if(data.ct.giftcount>1){
			runTime=5*1000;
		}else{
			return !1;
		}
		
		var giftQueueItem = [];
			giftQueueItem['time'] = Date.parse(new Date());
			giftQueueItem['data'] = data;
			giftQueueItem['type'] = type;
			giftQueueItem['giftPlayTime'] = 0;//Date.parse(new Date());
			giftQueueItem['runTime'] = runTime;
		
		giftQueue.unshift(giftQueueItem);

		if(giftPlayState==0)//如果队列未在执行创建一个队列
		{
			giftPlayState = 1;
			this.giftQueueStart();
		}else if(giftPlayState==2){
			//等待队列结束
			var queueStart = this.giftQueueStart;
			var interID = setInterval(function(){
				if(giftPlayState == 0)
				{
					clearInterval(interID);
					giftPlayState = 1;
					queueStart();
				}
			},10);
		}else{
			//console.log("队列正在执行，等待执行中");
		}
	
	},	
	giftQueueStart: function(){
		//获取到执行时间
		var data = giftQueue.pop();
		if(typeof(data)=="undefined") return 0;
		if(data['type']==1){
			/* Flash */
			this.giftShowswf(data['data']);
		}else{
			/* 普通礼物 */
			this.giftShowFlash(data['data']);
		}
		//判断下一个 有没有  什么时间放
		//当前一个 播放完之后检测 是否 有下一个  有 继续播放  没有 标注队列 状态为 0
		setTimeout(function(){
			if(giftQueue.length!=0){
				JsInterface.giftQueueStart();
			}else{
				giftPlayState = 0;//准备停止队列
			}
		},data['runTime'])
	},
	giftShowFlash: function(data) { //礼物展示
		var data= data.ct;
		if (this.giftEffects == 0) {
			return 0;
		}
		var giftIcon = data.gifticon;
		var giftcount = data.giftcount;
		if (giftcount >= 3344){
			var effectId = 9;
		}else if (giftcount >= 1314) {
			var effectId = 8;
		} else if (giftcount >= 520) {
			var effectId = 7;
		} else if (giftcount >= 188) {
			var effectId = 5;
		} else if (giftcount >= 99) {
			var effectId = 3;
		} else if (giftcount >= 66) {
			var effectId = 2;
		} else if (giftcount >= 11) {
			var effectId = 0;
		} else if (giftcount > 1) {
			var effectId = 0;
		} else {
			var effectId = -1;
		}
   
		//-1一个 0三角形 1不显示 2六字形 3嘴形 4元宝 5心形 7 ILOVEYOU 8一生一世 9海枯石烂
			if(giftcount>1){
				// 一次 多个礼物赠送的展示
				$('#LF-gift-container').css({
					"width": "672px",
					"height": "353px"
				});
		
				var aa=parseInt(Math.random()*10000);
				swfobject.getObjectById("LF-gift-flash").playEffect(giftIcon, effectId, 200,aa);
				setTimeout(
					function() {
						swfobject.getObjectById("LF-gift-flash").clearDuoEffect(aa);		
						$('#LF-gift-container').css({
							"width": "1px",
							"height": "1px"
						});
					}, 5000
				);
			}
	},
	giftShowswf: function(data) { //有swf 礼物展示
		var data= data.ct;
		var giftId = data["giftid"];
		var giftinfo=_DATA.gift[giftId]
		if (this.giftEffects == 0) {
			return 0;
		}
		var giftIcon = giftinfo['swf'];
		var effectId = -2;

		$('#LF-gift-container').css({
			"width": "672px",
			"height": "353px"
		});

		swfobject.getObjectById("LF-gift-flash").playEffect(giftIcon, effectId, 200);
		setTimeout(
			function() {
				swfobject.getObjectById("LF-gift-flash").clearEffect();
				swfobject.getObjectById("LF-gift-flash").playEffect("", "", 200);
				$('#LF-gift-container').css({
					"width": "1px",
					"height": "1px"
				});
			}, giftinfo['swftime'] * 1000
		);
	},		
	changeType:function(data){
		clearTimeout(charge_interval);
		
		var issuper = (_DATA.user && _DATA.user.issuper)|| 0;
		if(issuper){
			return !1;
		}
		//var type=data.type;
		var type=3;
		var type_val=data.type_val;
		var type_msg='';
		var liveuid=_DATA.anchor.id;
		var stream=_DATA.live.stream;
		var uid=(_DATA.user && _DATA.user.id)|| 0;
		_DATA.live.type=type; 
		_DATA.live.type_val=type_val;
		if(liveuid==uid){
			return !1;
		}
		if(uid==0){
			$("#LF_login").click();
			$(".js_login_pop .js_close").hide();
			var t=setTimeout("window.location.href='/'",20000);
			return !1;
		}
		if(type==2){
			type_msg='当前直播间为收费直播间<br>\
						(付费模式：门票价格'+type_val+_DATA.config.name_coin+')';
			liveType.charge(type_msg,liveuid,stream,type_val,0);
		}else if(type==3){
			type_msg='当前直播间为收费直播间<br>\
						(付费模式：1分钟/'+type_val+_DATA.config.name_coin+')';
			liveType.timecharge(type_msg,liveuid,stream,type_val,0);
		}else if(type==4){
			type_msg='当前直播间为收费直播间<br>\
						您可以选择以下两种方式来支付';
			liveType.bothcharge(type_msg,liveuid,stream,{fee_2:type_val,fee_3:type_val},0);
		}		
	},
	carQueueStart: function(){
		//获取到执行时间
		var data =carQueue.pop();
		var _this =this;
		if(typeof(data)=="undefined") return 0;
		
		this.carShowFlash(data);
		//判断下一个 有没有  什么时间放
		//当前一个 播放完之后检测 是否 有下一个  有 继续播放  没有 标注队列 状态为 0
		setTimeout(function(){
			if(carQueue.length>0){
				_this.carQueueStart();
			}else{
				carPlayState = 0;//准备停止队列
			}
		},data['runTime'])
	},
	carExecuteQueue: function(car,time,words){//执行队列
		var carinfo = {'swf':car,'long':time,'words':words};
		//记录坐骑信息
		if(carinfo && carinfo.swf){
			var carQueueItem = new Array();
			carQueueItem['time'] = Date.parse(new Date());
			carQueueItem['data'] = carinfo;
			carQueueItem['giftPlayTime'] = 0;//Date.parse(new Date());
			carQueueItem['runTime'] = time*1000;

			carQueue.unshift(carQueueItem);
		}else{
			return !1;
		}

		if(carPlayState==0){
			carPlayState = 1;
			this.carQueueStart();
		}else if(carPlayState==2){
			//等待队列结束
			var _this = this;
			var interID = setInterval(function(){
				if(carPlayState == 0)
				{
					clearInterval(interID);
					carPlayState = 1;
					_this.carQueueStart();
				}
			},10);
		}else{
			//console.log("队列正在执行，等待执行中");
		}
	},
	carShowFlash: function(data) { //坐骑展示

		if (this.giftEffects == 0) {
			return 0;
		}
		var carswf = data.data.swf;
		var effectId = -2;
		var screen_type=1;
		if(screen_type==1){
			$('#LF-enter-fx').css({
				"width": "600px",
				"height": "500px",
				"visibility": "visible",
			});
		}else{
			$('#LF-enter-fx').css({
				"width": "360px",
				"height": "288px"
			});
		}
		
		if(carswf.indexOf(".swf")>0){
			swfobject.getObjectById("LF-enter-flash").playEffect(carswf, effectId, 200);
			setTimeout(function() {
					swfobject.getObjectById("LF-enter-flash").clearEffect();			
					$('#LF-enter-fx').css({
						"width": "1px",
						"height": "1px",
					});				
				}, data.runTime
			);
		}else{
			$("#LF-enter-flash img").attr("src",carswf);
			setTimeout(function() {			
					$('#LF-enter-fx').css({
						"width": "1px",
						"height": "1px",
						"visibility": "hidden",
					});				
				}, data.runTime
			);
			
		}
		

	},
    connectVideo:function(data){
        
    },
    liveConnect:function(data){
        if(msgaction==1){
            var msg = '{"retcode":"000000","retmsg":"ok","msg":[{"_method_":"LiveConnect","action":"8","msgtype":"0","level":"'+_DATA.user.level+'","uid":"'+_DATA.user.uid+'","uname":"'+_DATA.user.user_nicename+'","uhead":"'+_DATA.user.avatar+'","vip_type":"'+ _DATA.user.vip.type +'","liangname":"'+ _DATA.user.liang.name+'","usertype":"'+ _DATA.usertype+'","guard_type":"'+ _DATA.guard_type+'","pkuid":"'+ data.uid+'"}]}'; 
            Socket.emitData('broadcast',msg);
        }
        
    },
    livePK:function(data){
        
    }
}