/*客户端socket.io接收与发送*/
//连接socket服务器
//连接状态设置为成功
_DATA.enterChat = 0;
////////////////////////////////////////////////////////////////////////////////////
var Socket = {
    nodejsInit: function () {
        this.inituser();
    },
    inituser: function () {
        $.ajax({
         	type:"get",
        	url:"./index.php?g=home&m=show&a=setNodeInfo",
			data:{showid:_DATA.anchor.id,stream:_DATA.anchor.stream},
			dataType:'json',
        	success:function(data){
        		if(data.error==0){
					socket.emit('conn', {uid:data.userinfo.id,roomnum: data.userinfo.roomnum,nickname: data.userinfo.user_nicename,stream:data.userinfo.stream,equipment: 'pc',token:data.userinfo.token});
                    if(_DATA.user){
                        _DATA.user.guard_type=data.userinfo.guard_type;
                    }
					var enterChat=setInterval(function () {
							if (_DATA.enterChat != 1) {
								/* socket.emit('conn', {uid:data.userinfo.id,roomnum: data.userinfo.roomnum,nickname: data.userinfo.user_nicename,stream:data.userinfo.stream,equipment: 'pc',token:data.userinfo.token});*/
								var msg={ct:'聊天服务器未连接，请刷新'};
								JsInterface.systemNot(msg);
							}else{
								if(_DATA.live && _DATA.live.islive==1 &&_DATA.user!=null)
								{
									liveType.getzombie(_DATA.user.id,data.userinfo.stream);
								} 
								clearInterval(enterChat);
							}
					}, 2000);							  
				}else{
					alert("信息初始化失败，请刷新");
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
	
	//for(var i in data){
	for(i=0;i<data.length;i++){
		if(i!='remove'){
			if(data[i]=="stopplay")
			{
				JsInterface.superStopRoom();
			}
			else
			{
				JsInterface.chatFromSocket(data[i]);
			}			
		}

	} 

});
socket.on('heartbeat', function (data) {
    socket.emit("heartbeat","heartbeat");
});
//==========node改====================conn===========================================
socket.on('conn', function (data) {  
		if(data[0]=='ok'){
			_DATA.enterChat = 1;
			if(_DATA.user && _DATA.user.id &&  _DATA.anchor.id && _DATA.user.id !=  _DATA.anchor.id){
				var msg = '{"msg":[{"_method_":"requestFans","action":"","timestamp":"'+WlTools.FormatNowDate()+'","ct":"","msgtype":"1","level":"","uid":"","sex":"","uname":"","uhead":"","usign":"","city":"好像在黑洞","level":""}],"retcode":"000000","retmsg":"ok"}';
				Socket.emitData('broadcast',msg);					
			}

			// 请求用户列表
			User.getOnline();
		}
});
//==========node改====================conn===========================================
