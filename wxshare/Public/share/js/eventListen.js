/*客户端socket.io接收与发送*/
//连接socket服务器
var enterChat = 0;


var Socket = {
    nodejsInit: function () {
        this.inituser();
    },
    inituser: function () {
        $.ajax({
        	type:"get",
        	url:"/wxshare/index.php/Share/setNodeInfo",
			data:{liveuid:to_uid},
			dataType:'json',
        	success:function(data){
        		if(data.error==0){
					socket.emit('conn', {uid:data.userinfo.id,roomnum: to_uid,stream:room_id,nickname: data.userinfo.user_nicename,equipment: 'pc',token:data.userinfo.token});
                    if(User){
                        User.guard_type=data.userinfo.guard_type;
                        User.usertype=data.userinfo.usertype;
                    }
					/* setInterval(function () {
						if (enterChat != 1) {
							// $("#chat_hall").append("<font color='red'>正在连接服务器......</font><br>");
							//socket.emit('conn', {uid: window.YB_JS_CONF.loginUserInfo.id,roomnum: window._DATA.author.id,nickname: window.YB_JS_CONF.loginUserInfo.user_nicename,equipment: 'pc',token:window.YB_JS_CONF.loginUserInfo.token});
						}
					}, 2000); */							 	 
				}else{
					console.log("信息初始化失败，请刷新");
				}
        	}
        });
    },
    //==========node改====================emitData===========================================
    emitData: function (event, msg) {
        socket.emit(event, msg);
    }
    //==========node改====================emitData===========================================
}

/*客户端广播接收broadcasting*/

socket.on('broadcastingListen', function (data) {
	//console.log(data);
	for(var i in data){
		JsInterface.chatFromSocket(data[i]);
	} 
});

//==========node改====================conn===========================================
socket.on('conn', function (data) {  
	if(data[0]=='ok'){
		enterChat = 1;
		$(".chat-tool").show();
	}
		
		console.log("conn:"+data);
		//JsInterface.plusRoomNums(1);
});
//==========node改====================conn===========================================


var JsInterface={
	giftEffects:1,
	roomNumObj:$(".MR-count .online .info cite"),
	userListNum:$(".MR-online .user cite"),
	chatFromSocket: function(data) {
		if(data=='stopplay'){
			$("#videoPlay").hide();   
			$("#play").remove();       
			$("#state").show();
			$("#top_box").hide();
			$(".jw-preview").show();
			return !1;
		}
		var data = JSON.parse(data);
		var msgObject = data.msg[0];
		var msgtype = msgObject.msgtype;
		var msgaction = msgObject.action;
		var msgmethod = msgObject._method_;
		if(msgmethod=='SendMsg'){ //聊天信息
			this.sendMsg(msgObject);
		}else if(msgmethod=='SendGift'){ //赠送礼物
			this.sendGift(msgObject);
		}else if(msgmethod=='BuyKeeper'){ //购买守护
			//this.buyKeeper(msgObject);
		}else if(msgmethod=='SendHorn'){ //喇叭
			this.sendHorn(msgObject);
		}else if(msgmethod=='SystemNot'){ //系统信息
			this.systemNot(msgObject);
		}else if(msgmethod=='StartEndLive'){ //开关播
			this.showEndRecommend(msgObject);
		}else if(msgmethod=='disconnect'){ //关播
			//this.disconnect(msgObject);
		}else if(msgmethod=='requestFans'){ //关播
			//var nums=msgObject.ct.data.info.nums;
			//this.setRoomNums(nums);
		}
	},
	/* 设置房间人数 */
	setRoomNums:function(nums){ 
		this.roomNumObj.html( parseInt(nums));
		this.userListNum.html( parseInt(nums));
	},	
	/* 添加人数 */
	plusRoomNums:function(nums){ 
		var now=this.roomNumObj.html();
		if(now==''){
			now=0;
		}
		var num= parseInt(now)+ parseInt(nums);
		this.userListNum.html( num);
		this.roomNumObj.html( num);
		
	},	
	/* 减少人数 */
	reduceRoomNums:function(nums){
		var now=this.roomNumObj.html();
		if(now==''){
			now=0;
		}
		var num= parseInt(now) - parseInt(nums)< 0 ? 0:parseInt(now) - parseInt(nums);
		this.roomNumObj.html( num);
		this.userListNum.html( num);
	},		
	sendMsg:function(data){
		var msgtype = data.msgtype;
		var msgaction = data.action;
		var _method_ = data._method_;
		
        var _msg = '';
        if(msgtype==0){
			_msg = '<p><label class ="user_nickname" user_id='+data.ct.uid+'><img src="'+level[data.ct.level]['thumb']+'" style="margin-top: -2px;margin-right:2px;" width="35" height="15" >'+data.ct.user_nicename+'</label> : <font color="#ffffff">进入房间</font></p>'  
        }else if(msgtype == 2) {
			_msg = '<p><label class ="user_nickname" user_id='+data.uid+'><img src="'+level[data.level]['thumb']+'" style="margin-top: -2px;margin-right:2px;" width="35" height="15" >'+data.uname+'</label> : <font color="#ffffff">'+data.ct+'</font></p>'  
        } 
        
        $('#chat_hall').append(_msg);
        this.remove_msg();
        var scrojh=$("#upchat_hall")[0].scrollHeight;
        $("#chat_hall").scrollTop($("#upchat_hall").scrollTop(scrojh));
		
	},
	remove_msg:function(){
        if($("#chat_hall>p").length > 100) {
            $("#chat_hall>p").slice(0,50).remove()
        }
    },

	disconnect:function(data){
		this.reduceRoomNums(1);		
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
				<span class="link">['+data.touname+'的直播频道]</span>\
			</a>';
			$(".MR-msg-notice .msg-content").html(html);
			$(".MR-msg-notice").show();
			setTimeout(function(){
				$(".MR-msg-notice").hide();
				$(".MR-msg-notice .msg-content").html('');
			},5000)
	},
	showEndRecommend:function(data){
		$("#videoPlay").hide();   
		$("#play").remove();       
		$("#state").show();
		$("#top_box").hide();
		$(".jw-preview").show();
	},
	systemNot:function(data){
		var _msg = '';
         _msg = '<p><font color="#ff9900" class="firstfont">'+data.ct+'</font></p>'
        
        $('#chat_hall').append(_msg);
        this.remove_msg();
        var scrojh=$("#upchat_hall")[0].scrollHeight;
        $("#chat_hall").scrollTop($("#upchat_hall").scrollTop(scrojh));
	},
	sendGift:function(data){

		/* var _msg = '<p><label class ="user_nickname" user_id='+data.uid+'><img src="'+level[data.level]['thumb']+'" style="margin-top: -2px;margin-right:2px;" width="35" height="15" >'+data.uname+'</label> : <font color="#E66973">我送了'+data.ct.giftcount+'个'+data.ct.giftname+'</font></p>',evensend  
      
        
        $('#chat_hall').append(_msg);
        this.remove_msg();
        var scrojh=$("#upchat_hall")[0].scrollHeight;
        $("#chat_hall").scrollTop($("#upchat_hall").scrollTop(scrojh)); */
		/* if(data.evensend=='y'){
			evensend=1;
		}else{
			evensend=0;
		}
		var a = {
			nick: data.uname,
			ptr: data.uhead,
			fromId: data.uid,
			num: data.ct.giftcount,
			giftId: data.ct.giftid,
			evensend: evensend,
			giftPic: data.ct.gifticon,
			type: data.ct.giftname
		};
		ShowGiftAnimate.init(a); */
        var roomnum=data.roomnum;
        if(roomnum!=to_uid){
            return !1;
        }
        
		if(data.ct.type==1 && data.ct.swftype==1){
            data.ct.swf=data.ct.gifticon;
		}
	
		window.HJ_PopBox.gift(data);
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
}