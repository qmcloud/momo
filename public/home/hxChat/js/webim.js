var curUserId = null;
var curChatUserId = null;
var conn = null;
var curRoomId = null;
var curChatRoomId = null;
var msgCardDivId = "chat01";
var talkToDivId = "talkTo";
var talkInputId = "talkInputId";
var bothRoster = [];
var toRoster = [];
var groupQuering = false;
var textSending = false;
var time = 0;


var friendsSub = {};
var PAGELIMIT = 8;
var pageLimitKey = new Date().getTime();

var encode = function ( str ) {
	if ( !str || str.length === 0 ) return "";
	var s = '';
	s = str.replace(/&amp;/g, "&");
	s = s.replace(/<(?=[^o][^)])/g, "&lt;");
	s = s.replace(/>/g, "&gt;");
	//s = s.replace(/\'/g, "&#39;");
	s = s.replace(/\"/g, "&quot;");
	s = s.replace(/\n/g, "<br>");
	return s;
};


/*多网页共存*/

var handlePageLimit = (function () {
	if ( Easemob.im.config.multiResources && window.localStorage ) {//如果设置为多浏览器共存，且支持window.localStorage（存储在 localStorage 里面的数据没有过期时间）
		var keyValue = 'empagecount' + pageLimitKey;

		$(window).on('storage', function () {
			localStorage.setItem(keyValue, 1);
		});
		return function () {
			try {
				localStorage.clear();
				localStorage.setItem(keyValue, 1);
			} catch ( e ) {}
		}
	} else {
		return function () {};
	}
}());



var clearPageSign = function () {
	if ( Easemob.im.config.multiResources && window.localStorage ) {
		try {
			localStorage.clear();
		} catch ( e ) {}
	}
};


/*获取总登录数*/

var getPageCount = function () {   
	var sum = 0;

	if ( Easemob.im.config.multiResources && window.localStorage ) {
		for ( var o in localStorage ) {
			if ( localStorage.hasOwnProperty(o) && /^empagecount/.test(o.toString()) ) {
				sum++;		
			}
		}
	}

	return sum;
};

window.URL = window.URL || window.webkitURL || window.mozURL || window.msURL;
var getLoginInfo = function () {
	return {
		isLogin : false
	};
};

var showChatUI = function () {
	$('#content').css({
		"display" : "block"
	});
	var login_userEle = document.getElementById("login_user").children[0];
	login_userEle.innerHTML = curUserId;
	login_userEle.setAttribute("title", curUserId);
};


//登录之前不显示web对话框
var hiddenChatUI = function () {
	$('#content').css({
		"display" : "none"
	});
	document.getElementById(talkInputId).value = "";
};


//定义消息编辑文本域的快捷键，enter和ctrl+enter为发送，alt+enter为换行
//控制提交频率
$(function() {
	$("textarea").keydown(function(event) {
		if (event.altKey && event.keyCode == 13) {
			e = $(this).val();
			$(this).val(e + '\n');
		} else if (event.ctrlKey && event.keyCode == 13) {
			//e = $(this).val();
			//$(this).val(e + '<br>');
			event.returnValue = false;
			sendText();   //调用发送方法
			return false;
		} else if (event.keyCode == 13) {
			event.returnValue = false;
			sendText();   //调用发送方法
			return false;
		}
	});
	$("#usetoken").on("click", function(){
		if ($("#password").attr("disabled")) {
			$("#password").removeAttr("disabled");
		} else {
			$("#password").attr("disabled", "disabled");
		}
		if ($("#token").attr("disabled")) {
			$("#token").removeAttr("disabled");
		} else {
			$("#token").attr("disabled", "disabled");
		}
	});
});
//easemobwebim-sdk注册回调函数列表
$(document).ready(function() {
	if ( Easemob.im.Helper.getIEVersion && Easemob.im.Helper.getIEVersion < 10 ) {
		$('#em-cr').remove();
	}

	
	
	conn = new Easemob.im.Connection({
		multiResources: Easemob.im.config.multiResources,
		https : Easemob.im.config.https,
		url: Easemob.im.config.xmppURL
	});
	//初始化连接
	conn.listen({
		//当连接成功时的回调方法
		onOpened : function() {

			handleOpen(conn);
		},
		//当连接关闭时的回调方法
		onClosed : function() {
			handleClosed();
		},
		//收到文本消息时的回调方法
		onTextMessage : function(message) {

			handleTextMessage(message);
		},
		//收到表情消息时的回调方法
		onEmotionMessage : function(message) {
			handleEmotion(message);
		},
		
		//收到联系人信息的回调方法
		onRoster: function(message) {
			handleRoster(message);
		},
		
        onOffline: function () {
            setTimeout(logout, 1000);
        },
		//异常时的回调方法
		onError: function(message) {
			//alert("in onError function");
			handleError(message);
		}
	});
	


	$('#confirm-block-div-modal').on('hidden.bs.modal', function(e) {
	});
	$('#option-room-div-modal').on('hidden.bs.modal', function(e) {
	});
	$('#notice-block-div').on('hidden.bs.modal', function(e) {
	});
	$('#regist-div-modall').on('hidden.bs.modal', function(e) {
	});


	
});

//处理连接时函数,主要是登录成功后对页面元素做处理
var handleOpen = function(conn) {

	//从连接中获取到当前的登录人注册帐号名
	curUserId = conn.context.userId;
	 conn.setPresence();//设置用户上线状态，必须调用
	

	//启动心跳
	if (conn.isOpened()) {
		conn.heartBeat(conn);
	}
};
//连接中断时的处理，主要是对页面进行处理
var handleClosed = function() {
	curUserId = null;
	curChatUserId = null;
	curRoomId = null;
	curChatRoomId = null;
	bothRoster = [];
	toRoster = [];
	hiddenChatUI();
	for(var i=0,l=audioDom.length;i<l;i++) {
		if(audioDom[i].jPlayer) audioDom[i].jPlayer('destroy');
	}
	clearContactUI("contactlistUL", "contracgrouplistUL",
			"momogrouplistUL", msgCardDivId);
	showLoginUI();
	groupQuering = false;
	textSending = false;
};


	
	
	
	
//easemobwebim-sdk中处理出席状态操作
var handleRoster = function(rosterMsg) {
	for (var i = 0; i < rosterMsg.length; i++) {
		var contact = rosterMsg[i];
		if (contact.ask && contact.ask == 'subscribe') {
			continue;
		}
		if (contact.subscription == 'to') {
			toRoster.push({
				name : contact.name,
				jid : contact.jid,
				subscription : "to"
			});
		}
		if (contact.subscription == 'from' || contact.subscription == 'both') {
			toRoster.push({
				name : contact.name,
				jid : contact.jid,
				subscription : "from"
			});

			var isexist = contains(bothRoster, contact);
			if (!isexist) {
				var lielem = $('<li>').attr({
					"id" : contact.name,
					"class" : "offline",
					"className" : "offline"
				}).click(function() {//点击时聊天
					chooseContactDivClick(this);
				});
				$('<img>').attr({
					"src" : "/public/home/hxChat/images/contact_normal.png"
				}).appendTo(lielem);
				$('<span>').html(contact.name).appendTo(lielem);
				$('#contactlistUL').append(lielem);
				bothRoster.push(contact);
			}
		}
		if (contact.subscription == 'remove') {
			var isexist = contains(bothRoster, contact);
			if (isexist) {
				removeFriendDomElement(contact.name);
			}
		}
	}
};
//异常情况下的处理方法
var handleError = function(e) {
	curChatRoomId = null;

	clearPageSign();
	e && e.upload && $('#fileModal').modal('hide');
	if (curUserId == null) {

		//alert(e.msg + ",请重新登录");
		//alert("您的线路不稳定，请刷新页面再试哦！");
		layer.msg("您的线路不稳定，请刷新页面再试哦！");
	} else {
		var msg = e.msg;
		if (e.type == EASEMOB_IM_CONNCTION_SERVER_CLOSE_ERROR) {
			if (msg == "" || msg == 'unknown' ) {
				alert("私信聊天服务器断开连接,可能是因为在别处登录");
			} else {
				alert("私信聊天服务器断开连接");
			}
		} else if (e.type === EASEMOB_IM_CONNCTION_SERVER_ERROR) {
			if (msg.toLowerCase().indexOf("user removed") != -1) {
				alert("用户已失去私信功能,请与管理员联系！");
			}
		} else {
			//alert(msg);
			alert("线路不畅，私信聊天开小差啦");
		}
	}

	

	conn.stopHeartBeat();
};






//判断要操作的联系人和当前联系人列表的关系
var contains = function(roster, contact) {
	var i = roster.length;
	while (i--) {
		if (roster[i].name === contact.name) {
			return true;
		}
	}
	return false;
};

Array.prototype.remove = function(val) {
	var index = this.indexOf(val);
	if (index > -1) {
		this.splice(index, 1);
	}
};





//设置当前显示的聊天窗口div，如果有联系人则默认选中联系人中的第一个联系人，如没有联系人则当前div为null-nouser
var setCurrentContact = function(defaultUserId) {
	$.ajax({
		url:"./index.php?g=home&m=user&a=searchUserInfo",
		type:"get",
		data:{uid:defaultUserId},
		dataType:"json",
		success:function(data){

			/*成功后执行--start*/
			showContactChatDiv(defaultUserId,data.user_nicename);
			if (curChatUserId != null) {
				hiddenContactChatDiv(curChatUserId);
			} else {
				$('#null-nouser').css({
					"display" : "none"
				});
			}
			curChatUserId = defaultUserId;
			/*成功后执行--end*/
		},
		error:function(){

		}
	});
	
};



//选择联系人的处理
var getContactLi = function(chatUserId) {
	return $(chatUserId);
};

//构造当前聊天记录的窗口div
var getContactChatDiv = function(chatUserId) {
	return document.getElementById(curUserId + "-" + chatUserId);
};

//如果当前没有某一个联系人的聊天窗口div就新建一个
var createContactChatDiv = function(chatUserId) {
	var msgContentDivId = curUserId + "-" + chatUserId;
	var newContent = document.createElement("div");
	$(newContent).attr({
		"id" : msgContentDivId,
		"class" : "chat01_content",
		"className" : "chat01_content",
		"style" : "display:none"
	});
	return newContent;
};

//显示当前选中联系人的聊天窗口div，并将该联系人在联系人列表中背景色置为黑色
var showContactChatDiv = function(chatUserId,userNicename) {
	var contentDiv = getContactChatDiv(chatUserId);
	if (contentDiv == null) {
		contentDiv = createContactChatDiv(chatUserId);
		document.getElementById(msgCardDivId).appendChild(contentDiv);
	}
	contentDiv.style.display = "block";
	var contactLi = document.getElementById(chatUserId);
	if (contactLi == null) {
		return;
	}
	contactLi.style.backgroundColor = "#161616";
	$(".chatUserName").css('color', '#FFF');
	var dispalyTitle = null;//聊天窗口显示当前对话人名称
	
		dispalyTitle = "与" + userNicename + "聊天中";
		$("#roomMemberImg").css('display', 'none');
	
    var title = $('#' + talkToDivId).find('a');
	title.html(dispalyTitle).attr('title', dispalyTitle);
};
//对上一个联系人的聊天窗口div做隐藏处理，并将联系人列表中选择的联系人背景色置空
var hiddenContactChatDiv = function(chatUserId) {
	var contactLi = document.getElementById(chatUserId);
	if (contactLi) {
		contactLi.style.backgroundColor = "";
	}
	var urllength=$("#momogrouplistUL").children('li').length;
	if(urllength>1){
		var contentDiv = getContactChatDiv(chatUserId);
		if (contentDiv) {
			contentDiv.style.display = "none";
			/* $("#momogrouplistUL").children('li'). */
			$("#momogrouplistUL").find("li:first-child").css("display","block");
		}
	}
	
};



//切换联系人聊天窗口div

var chooseContactDivClick = function(li) {
	//console.log(123);
	//console.log('chooseContactDivClick');
	//console.log(li);
	
	/*获取当前li里的span内容（即用户昵称）*/
	var spanText=$(li).find("span:first").text();
	//console.log(li);
	$(li).css('backgroundColor', '#161616');
	$(li).css('background-image', '');
	$(".chatUserName").css('color', '#FFF');
	var chatUserId = li.id;   //获取当前li的id
	
	var currentUid=$("#CurrentUid").val();
	$("#"+currentUid+"-undefined").remove();
	$("#none-undefined").remove();

	var HxChatUid=$("#HxChatUid").val();

	/*解决：重复点击某人时，右侧聊天记录丢失*/
	if($("#"+HxChatUid+"-"+chatUserId).css("display")=='block'){
		return;
	}


	var currentChatUid=$("#currentChatUid").attr('value');//获取隐藏域的id

/* 	if(currentChatUid==chatUserId){
		return;
	} */

	$("#currentChatUid").val(chatUserId);


		roomId = $(li).attr("roomId"),
        isChatroom = roomId && $(li).attr("type") === chatRoomMark;
		//console.log(chatUserId);

  

   		hiddenContactChatDiv(curChatUserId);

    if ( isChatroom ) {
        curChatUserId = null;
    }
    if (chatUserId != curChatUserId) {
        showContactChatDiv(chatUserId,spanText);
		isChatroom || (curChatUserId = chatUserId);
	}

	

	//对默认的null-nouser div进行处理,走的这里说明联系人列表肯定不为空所以对默认的聊天div进行处理
	$('#null-nouser').css({
		"display" : "none"
	});
	console.log(789);
	var badgespan = $(li).children(".badge");
	if (badgespan && badgespan.length > 0) {
		li.removeChild(li.children[2]);
	}

	//点击有未读消息对象时对未读消息提醒的处理
	var badgespanGroup = $(li).parent().parent().parent().find(".badge");
	if (badgespanGroup && badgespanGroup.length == 0) {
		$(li).parent().parent().parent().prev().children().children().remove();
	}
};


var emotionFlag = false;

/*显示表情列表*/
var showEmotionDialog = function() {
	if (emotionFlag) {
		$('#wl_faces_box').css({
			"display" : "block"
		});
		return;
	}
	emotionFlag = true;
	// Easemob.im.Helper.EmotionPicData设置表情的json数组
	var sjson = Easemob.im.EMOTIONS,
		data = sjson.map,
		path = sjson.path;

	for ( var key in data) {
		var emotions = $('<img>').attr({
			"id" : key,
			"src" : path + data[key],
			"style" : "cursor:pointer;"
		}).click(function() {
			selectEmotionImg(this);
		});
		$('<li>').append(emotions).appendTo($('#emotionUL'));
	}
	$('#wl_faces_box').css({
		"display" : "block"
	});
};


//表情选择div的关闭方法
var turnoffFaces_box = function() {
	$("#wl_faces_box").fadeOut("slow");
};


var selectEmotionImg = function(selImg) {
	var txt = document.getElementById(talkInputId);
	txt.value = txt.value + selImg.id;
	txt.focus();
};


/*点击发送按钮时发送信息*/

var sendText = function() {

	if (textSending) {  //如果顶部设置不可发送，将无法发送
		return;
	}
	textSending = true;//修改textSending值，表示可发送
	var msgInput = document.getElementById(talkInputId);  //获取输入框对象
	var msg = msgInput.value;  //获取输入框的值
	if (msg == null || msg.length == 0) {  //如果为空，不可发送
		textSending = false;
		return;
	}
	var to = curChatUserId;  //获取接收者id


	if (to == null) {
		textSending = false;
		return;
	}

	var options = {
		to : to,
		msg : msg,
		type : "chat"
	};
			
	//easemobwebim-sdk发送文本消息的方法 to为发送给谁，meg为文本消息对象
	conn.sendTextMessage(options);
	//当前登录人发送的信息在聊天窗口中原样显示
	var msgtext = Easemob.im.Utils.parseLink(Easemob.im.Utils.parseEmotions(encode(msg)));
	//console.log('123_a');
	//将左侧的对应人红点和背景图更换
	var leftChatUser =$("#momogrouplistUL").children("#"+to);
	
	if(leftChatUser.css("background-image")!=""){
		leftChatUser.css("backgroundColor","#161616");
		leftChatUser.css("background-image","");
	}
	appendMsg(curUserId, to, msgtext,0);
	turnoffFaces_box();//关闭表情选择包
	msgInput.value = "";
	msgInput.focus();
	setTimeout(function() {
		textSending = false;
	}, 1000);


	/*将搜索框清空，将搜索结果清空*/
	$("#searchfriend").val('');
	
	$(".searchResult").html("").removeClass('searchMsg');
	$(".searchResult").height(0);
	$("#contractlist11").css('height', 440);
	$("#momogrouplist").css('height', 436);
	
	$("#momogrouplistUL").css("height",436).css("overflow",'auto');




	
};


var send = function () {

	var fI = $('#fileInput');
	fI.val('').attr('data-type', this.getAttribute('type')).click();
};
$('#sendPicBtn, #sendAudioBtn, #sendFileBtn').on('click', send);



//easemobwebim-sdk收到文本消息的回调方法的实现

var handleTextMessage = function(message) {
	
	var from = message.from;//消息的发送者
	
	var currentUid=$("#CurrentUid").val();//当前登录用户
	/*if(currentUid==""||isNaN(currentUid)){//如果没有登录，直接return
		return;
	}*/
	$.ajax({
		type:"get",
		url:"./index.php?g=Home&m=User&a=checkBlack",
		data:{uid:currentUid,touid:from},
		dataType:"text",
		async:"false",
		success:function(data){	//返回是数字
			if(data==1){//如果是被拉黑的人发送信息，直接不接收
				return;
			}

			//为隐藏域赋值
			//$("#currentChatUid").attr("value",from);
			$("#currentChatUid").val(from);
			//console.log(from+"from");
			var mestype = message.type;//消息发送的类型是群组消息还是个人消息
			//console.log(mestype+"type");
			var messageContent = message.data;//文本消息体
			//console.log(messageContent+"data");
			//TODO  根据消息体的to值去定位那个群组的聊天记录
			var room = message.to;
			//console.log(room+"to");
			//console.log('123_b');
			appendMsg(message.from, message.from, messageContent,1);
			
		},
		error:function(){

		}

	});
	


};


//easemobwebim-sdk收到表情消息的回调方法的实现，message为表情符号和文本的消息对象，文本和表情符号sdk中做了
//统一的处理，不需要用户自己区别字符是文本还是表情符号。
var handleEmotion = function(message) {
	var from = message.from;
	var room = message.to;
	var mestype = message.type;//消息发送的类型是群组消息还是个人消息
	

		appendMsg(from, from, message);
	
};





//收到陌生人消息时创建陌生人列表
var createMomogrouplistUL = function createMomogrouplistUL(who, message) {

	var momogrouplistUL = document.getElementById("momogrouplistUL");
	var cache = {};
	if (who in cache) {
		return;
	}
	cache[who] = true;

	$("#currentChatUid").attr("value",who);//将当前聊天的隐藏域的value值换为当前用户id

	/*判断列表中是否存在该用户（如果不存在，在末尾添加，如果存在，）*/
	var momoSearchLiLen=$("#momogrouplistUL").children("#"+contact).length;
		// alert(momoSearchLiLen);
		if(momoSearchLiLen==0){ //如果列表中不存在该用户，就在列表末尾加上
			
		//	createUserMessage(who);  //调用创建用户信息方法

		}else{//如果存在，就选中聊天
			
			chooseContactDivClick(this);
			
		}

/*	createUserMessage(who);  
	console.log("aaa"+who);*/
};


var createUserMessage=function(who){
	
	var lielem = document.createElement("li");
	$(lielem).attr({
		'id' : who,
		'class' : 'offline',
		'className' : 'offline',
		'type' : 'chat',
		'displayName' : who,
		'title' : who
		
	});

	$(lielem).css('backgroundColor', '#3384ff');
	$(lielem).css("background-image","url(/public/home/hxChat/images/redBgDian.png)");
	$(lielem).css("background-position","170px 0");
	$(lielem).css("background-repeat","no-repeat");
	$(".chatUserName").css('color', '#FFF');



	lielem.onclick = function() {

		chooseContactDivClick(this);  //点击陌生人名称时聊天
		//console.log('lielem');
	};
	var imgelem = document.createElement("img");
	//使用ajax获取用户的头像和昵称
	$.ajax({
		type:"GET",
		url:"./index.php?g=Home&m=User&a=searchUserInfo",
		data:{uid:who},
		dataType:"json",
		async:false,
		success:function(data){
			if(data.code==0){
				imgelem.setAttribute("src",data.avatar);
				imgelem.style.width="35px";
				imgelem.style.height="35px";
				lielem.appendChild(imgelem);
				var spanelem = document.createElement("span");
				spanelem.innerHTML = data.user_nicename;  //聊天人账号
				
				
			}else{
				imgelem.style.width="35px";
				imgelem.style.height="35px";
				imgelem.setAttribute("src", "/public/home/hxChat/images/contact_normal.png");
				lielem.appendChild(imgelem);
				var spanelem = document.createElement("span");
				spanelem.innerHTML = who;  //聊天人账号
			
			}
			lielem.appendChild(spanelem);
		},
		error:function(){

		}
	});
	//最新消息置顶
	var urlength=momogrouplistUL.childNodes.length;
	if(urlength==0){
		momogrouplistUL.appendChild(lielem);
	}else{
		for (var i=0;i<=urlength;i++)
		{
			if (i==0)
		   {
				momogrouplistUL.insertBefore(lielem,momogrouplistUL.childNodes[i]);
				break;
		   }
		}
	}

}


var createUserMessage_a=function(who){
	
	var lielem = document.createElement("li");
	$(lielem).attr({
		'id' : who,
		'class' : 'offline',
		'className' : 'offline',
		'type' : 'chat',
		'displayName' : who,
		'title' : who
		
	});
	//console.log(123);
	$(lielem).css('backgroundColor', '#161616');
	$(".chatUserName").css('color', '#FFF');



	lielem.onclick = function() {

		chooseContactDivClick(this);  //点击陌生人名称时聊天
		//console.log('lielem');
	};
	var imgelem = document.createElement("img");
	//使用ajax获取用户的头像和昵称
	$.ajax({
		type:"GET",
		url:"./index.php?g=Home&m=User&a=searchUserInfo",
		data:{uid:who},
		dataType:"json",
		success:function(data){
			if(data.code==0){
				imgelem.setAttribute("src",data.avatar);
				imgelem.style.width="35px";
				imgelem.style.height="35px";
				lielem.appendChild(imgelem);
				var spanelem = document.createElement("span");
				spanelem.innerHTML = data.user_nicename;  //聊天人账号
				
			}else{
				imgelem.style.width="35px";
				imgelem.style.height="35px";
				//spanelem.setAttribute("class","chatUserName");
			 
				if(who==1){
					imgelem.setAttribute("src", "/public/images/sytemmesg.png");
					lielem.appendChild(imgelem);
					var spanelem = document.createElement("span");
					spanelem.innerHTML = "系统消息";  //聊天人账号
				}else{
					imgelem.setAttribute("src", "/public/home/hxChat/images/contact_normal.png");
					lielem.appendChild(imgelem);
					var spanelem = document.createElement("span");
					spanelem.innerHTML = who;  //聊天人账号
				}
				
				
			}
			lielem.appendChild(spanelem);
			/* momogrouplistUL.prepend(lielem); */
			momogrouplistUL.appendChild(lielem);
			/* //判断列表中是否存在该用户
			var momoSearchLiLen=$("#momogrouplistUL").children("#"+who).length;
			if(momoSearchLiLen==0){
				momogrouplistUL.appendChild(lielem);
			} */
		},
		error:function(){

		}
	});
	

	  
}





var handleChatRoomMessage = function (contact) {
	//alert(contact);
	/*if ( contact.indexOf(chatRoomMark) > -1 ) {
		return contact.slice(chatRoomMark.length) === curChatRoomId;
	}*/
	return true;
};
 
//显示聊天记录的统一处理方法
var appendMsg = function(who, contact, message,color, onlyPrompt) {
	if ( !handleChatRoomMessage(contact) ) { return; }
	

	/*判断陌生人列表内容是否为空*/
	var momoLiLength=$("#momogrouplistUL").children('li').length;

	/* alert(momoLiLength); */

		//判断列表中是否存在该用户
		var momoSearchLiLen=$("#momogrouplistUL").children("#"+contact).length;

		if(momoSearchLiLen==0){ //如果列表中不存在该用户，就在列表末尾加上
			console.log("zou");
			if(color ==1){
				createUserMessage(contact);  //调用创建用户信息方法

			}else{
				createUserMessage_a(contact);  //调用创建用户信息方法

			}

		}else{//如果存在，就选中聊天
			
			if(who!=_DATA.user.id){

				$("#momogrouplistUL").children("#"+contact).css("background-color","#3384ff");
				$("#momogrouplistUL").children("#"+contact).css("background-image","url(/public/home/hxChat/images/redBgDian.png)");
				$("#momogrouplistUL").children("#"+contact).css("background-position","170px 0");
				$("#momogrouplistUL").children("#"+contact).css("background-repeat","no-repeat");
				
				
			}
			//置顶
			$("#momogrouplistUL").prepend($("#momogrouplistUL").children("#"+contact)); 
		}
	/* 	var HxChatUid=$("#HxChatUid").val();
		$(".chatRight #"+HxChatUid+"-"+contact).css("display","block"); */
			//chooseContactDivClick(this);
		
		

	var contactDivId = contact;
	var contactLi = getContactLi(contactDivId);
	//console.log(contactLi);


	// 消息体 {isemotion:true;body:[{type:txt,msg:ssss}{type:emotion,msg:imgdata}]}
	var localMsg = null;
	if (typeof message == 'string') {
		localMsg = Easemob.im.Helper.parseTextMessage(message);
		localMsg = localMsg.body;
	} else {
		localMsg = message.data;
	}
	var headstr = onlyPrompt ? ["<p1>" + message + "</p1>"] : [ "<p1>" + "  " + "   <span></span>" + "   </p1>",
			"<p2 class='chatTime'>" + getLoacalTimeString() + "<b></b><br/></p2>" ];
	var header = $(headstr.join(''))
	var lineDiv = document.createElement("div");
	for (var i = 0; i < header.length; i++) {
		var ele = header[i];
		lineDiv.appendChild(ele);
	}
	var messageContent = localMsg,
		flg = onlyPrompt ? 0 : messageContent.length;

	for (var i = 0; i < flg; i++) {
		var msg = messageContent[i];
		var type = msg.type;
		var data = msg.data;
		
		
		if (type == "emotion") {
			var ele = $("<p><img src='" + data + "'/></p>");
			ele.attr("class", "chat-content-p3");
            lineDiv.appendChild(ele.get(0));
		}
		
		if(type=="txt"){
			var ele = $("<p>" + data + "</p>");
			ele.attr("class", "chat-content-p3");
	        lineDiv.appendChild(ele.get(0));
		}
		
		
	}
	if (curChatUserId == null) {
		onlyPrompt || setCurrentContact(contact);
		if (time < 1) {
			//$('#accordion3').click();
			time++;
		}
	}
	// alert("curUserId"+curUserId);
	// alert("contactDivId"+contactDivId);
	var msgContentDiv = getContactChatDiv(contactDivId);
	if ( onlyPrompt ) {
		lineDiv.style.textAlign = "center";
	} else if (curUserId == who) {
		lineDiv.style.textAlign = "right";
	} else {
		lineDiv.style.textAlign = "left";
	}
	var create = false;
	if (msgContentDiv == null) {
		msgContentDiv = createContactChatDiv(contactDivId);
		create = true;
		
	}
	msgContentDiv.appendChild(lineDiv);
	if (create) {
		document.getElementById(msgCardDivId).appendChild(msgContentDiv);
	}
	if(type == 'audio' && msg.audioShim) {
		setTimeout(function(){
			playAudioShim(d.find('.'+t), data.currentSrc, t);
		}, 0);
	}
	// alert(msgContentDiv);
	//console.log(msgContentDiv);
	var $ChatDiv=$(msgContentDiv);

	//msgContentDiv.scrollTop = msgContentDiv.scrollHeight;
	
	$ChatDiv.animate({scrollTop:9999},1000);//设置滚动条滚动到底部显示最新信息
	return lineDiv;
};






//添加输入框鼠标焦点进入时清空输入框中的内容
var clearInputValue = function(inputId) {
	$('#' + inputId).val('');
};
var showDelFriend = function() {
	$('#delFridentModal').modal('toggle');
	$('#delfridentId').val('好友账号');//输入好友账号
	$('#del-frident-warning').html("");
};




var showWarning = function(message) {
	$('#notice-block-div').modal('toggle');
	$('#notice-block-body').html(message);
};




//清除聊天记录
var clearCurrentChat = function clearCurrentChat() {
	var currentDiv = getContactChatDiv(curChatUserId)
			|| createContactChatDiv(curChatUserId);
	currentDiv.innerHTML = "";
};





/*var showRegist = function showRegist() {
	$('#loginmodal').modal('hide');
	$('#regist-div-modal').modal('toggle');
};*/
var getObjectURL = function getObjectURL(file) {
	var url = null;
	if (window.createObjectURL != undefined) { // basic
		url = window.createObjectURL(file);
	} else if (window.URL != undefined) { // mozilla(firefox)
		url = window.URL.createObjectURL(file);
	} else if (window.webkitURL != undefined) { // webkit or chrome
		url = window.webkitURL.createObjectURL(file);
	}
	return url;
};
var getLoacalTimeString = function getLoacalTimeString() {
	var date = new Date();
	var time = date.getHours() + ":" + date.getMinutes() + ":"
			+ date.getSeconds();
	return time;
}


/*关闭聊天窗口*/

var closeHxChat=function(){
	$(".hxChatWindow").slideUp('slow');
}


/*点击聊天小图标，调出聊天界面*/
var ShowhxChatWindow=function(){
	$(".hxChatWindow").slideDown('slow');
	$("#content").css('display', 'block');
}


/**/




Easemob.im.EMOTIONS = {
    path: '/public/home/hxChat/img/faces/'
    , map: {
        '[):]': 'ee_1.png',
        '[:D]': 'ee_2.png',
        '[;)]': 'ee_3.png',
        '[:-o]': 'ee_4.png',
        '[:p]': 'ee_5.png',
        '[(H)]': 'ee_6.png',
        '[:@]': 'ee_7.png',
        '[:s]': 'ee_8.png',
        '[:$]': 'ee_9.png',
        '[:(]': 'ee_10.png',
        '[:\'(]': 'ee_11.png',
        '[:|]': 'ee_12.png',
        '[(a)]': 'ee_13.png',
        '[8o|]': 'ee_14.png',
        '[8-|]': 'ee_15.png',
        '[+o(]': 'ee_16.png',
        '[<o)]': 'ee_17.png',
        '[|-)]': 'ee_18.png',
        '[*-)]': 'ee_19.png',
        '[:-#]': 'ee_20.png',
        '[:-*]': 'ee_21.png',
        '[^o)]': 'ee_22.png',
        '[8-)]': 'ee_23.png',
        '[(|)]': 'ee_24.png',
        '[(u)]': 'ee_25.png',
        '[(S)]': 'ee_26.png',
        '[(*)]': 'ee_27.png',
        '[(#)]': 'ee_28.png',
        '[(R)]': 'ee_29.png',
        '[({)]': 'ee_30.png',
        '[(})]': 'ee_31.png',
        '[(k)]': 'ee_32.png',
        '[(F)]': 'ee_33.png',
        '[(W)]': 'ee_34.png',
        '[(D)]': 'ee_35.png'
    }
};
