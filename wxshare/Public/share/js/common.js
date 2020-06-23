/**
*直播间js
*编码utf8
*/

//设置礼物id giftid，礼物需要金额giftmoney，余额money
var giftmoney='',money='',giftimg='',giftname='';
var myVideo=document.getElementById("video1");
var chattool=$(".chat-tool"),
userinfocon=$(".user_info_con"),
bglancemoney=$(".bglance_money");

var Ctrfn={
	is_countdown : !1,
    countdown_handler : null,
    moreShare:function(){
        userinfocon.hide();
        $(".chat-tool .more_list").hide();
        $(".share_box").addClass("sanimt");
        $(".section1").click(function(e) {
            var target = $(e.target);
            //点击其他地方隐藏分享列表
            if(!target.is('.more_list *')&&!target.is('.share_box') && !target.is('.share_box *') && !target.is("#flower-btn")) {
               $(".share_box").removeClass("sanimt");
            }
        });
    },
    //更多
    moreBtn:function(){
        if($(".chat-tool .more_list").is(":hidden")){
            $(".chat-tool .more_list").show();
        }else{
            $(".chat-tool .more_list").hide();
        }
        $(".section1").click(function(e) {
            var target = $(e.target);
            //点击其他地方隐藏礼物列表
            if(!target.is('.more_list *')&&!target.is('#more-btn')) {
               $(".chat-tool .more_list").hide();
            }
        });
    },
    talkBtn:function(e){
        userinfocon.hide();
        if(!$(".chat-tool").is(":hidden")){
            $(".chat-tool").hide();
        }
        $(".chat_input").show();  
        $("#message").focus(); 
        $(".section1").click(function(e) {
            var target = $(e.target);
            //点击其他地方隐藏礼物列表
            if(!target.is('#talk-btn')&&!target.is('.chat_input')&&!target.is('.chat_input *')&&!target.is('#gift-btn')) {
                if($(".chat-tool").is(":hidden")){
                    $(".chat-tool").show();
                }
                $(".chat_input").hide();
                $(".chat_barrage ").removeClass("animte");
                fly=""
                
            }
        });
    },
    giftTool:function(){
        userinfocon.hide();
        if(!$(".chat-tool").is(":hidden")){
            $(".chat-tool").hide();
        }
        $(".chat_gift").css({"opacity":"1","z-index":"22","display":"block"});
        
        $(".section1").click(function(e) {
            var target = $(e.target);
            //点击其他地方隐藏礼物列表
            if(!target.is('.chat_gift') && !target.is(".chat_gift *") && !target.is("#gift-btn") && !target.is("#talk-btn")) {
                $(".chongzhi_num input").val('');
                $(".chat_gift").css({"opacity":"0","z-index":"-1"});
                $(".sel").remove();
                if($(".chat-tool").is(":hidden")){
                    $(".chat-tool").show();
                }
            }

        });
    },
//    //点击发送信息设置
    onmessage:function(url){
        if($("#message").val() == ""){
            Txbb.Pop('toast', '消息不能为空...','center');
            return;
        }
        if(User){
            this.flymsgfn(url);
        }
    },
    flymsgfn:function(url){
        if(fly=="FlyMsg"){
            $.ajax({
                type: 'POST',
                url: url,
                data:{"token":User.token,"roomid":room_id,"content":$("#message").val()},
                dataType:'json',
                success: function(data){
                    if((Number($(".bglance_money").text())-100)<0){
                        $(".bglance_money").text(0); 
                        Txbb.Pop('toast', '余额不足，请充值...');
                    }else{
                        $(".bglance_money").text(Number($(".bglance_money").text())-100); 
                    }
                }
            });
        }else{

			var msg = '{"msg":[{"_method_":"SendMsg","action":0,"ct":"'+$("#message").val()+'","msgtype":"2","uid":"'+User.id+'","uname":"'+User.user_nicename+'","level":"'+User.level+'","vip_type":"'+ User.vip.type +'","liangname":"'+ User.liang.name+'","usertype":"'+ User.usertype+'","guard_type":"'+ User.guard_type+'"}],"retcode":"000000","retmsg":"OK"}';
			Socket.emitData('broadcast',msg);	
        }

        $("#message").val("");
        $(".chat_input").hide();
        chattool.show();
        $(".chat_input > .chat_barrage").removeClass("animte ");
        fly="";
    },
    init_screen:function(){
            var _top = 0;
            $(".chat_barrage_box > div").show().each(function () {
                var _left = $(window).width() - $(this).width()+200;
                var _height = $(window).height();
                _top = _top + 45;
                if (_top >= _height - 200) { 
                    _top = 40;
                }
                $(this).css({left: _left, top: _top});
                var time = 12000;
                // if ($(this).index() % 2 == 0) {
                //     time = 12000;
                // }
                $(this).animate({left: "-" + _left + "px"}, time, function () {
                    $(this).remove();
                });
            });
    },
    play:function(objbtn){
        var myVideo=document.getElementById("videoHLS_html5_api");
        objbtn.parent().hide();
        $(".jw-preview").hide();
        $(".down-bottom").hide();
        myVideo.play();
		if(!isWeixin && !User){
			//登录按钮显示
			$('#login-btn').show();
			$('.js-reg').show();
		}
		Socket.nodejsInit();
    },
    giftBtn:function(objbtn){
        var swiperSlide=$("#swiper-wrapper");
        //选中状态
        //除了当前礼物选中，其他的都移除状态
        swiperSlide.find(".selected").removeClass('selected');
        objbtn.find(".gift-select").addClass('selected');
		this.countdown_hide();
    },
	countdown_show:function() {
        var i = this;
        $(".gift-countdown-bg").show(),
        $(".gift-countdown-btn-time").html("5"),
		null !== this.countdown_handler && clearTimeout(this.countdown_handler),
        this.is_countdown = !0,
        this.seq = 0,
        this.countdown_handler = setTimeout(function() {
            i.countdown()
        },
        100)
    },
	countdown_hide : function() {
        $(".gift-countdown-bg").hide(),null !== this.countdown_handler && clearTimeout(this.countdown_handler),this.is_countdown = !1
    },
	countdown:function() {
        var i = this,
        e = parseInt($(".gift-countdown-btn-time").html()) - 1;
        e > 0 ? ($(".gift-countdown-btn-time").html(e), this.countdown_handler = setTimeout(function() {
            i.countdown()
        },
        1000)) : (null !== this.countdown_handler && clearTimeout(this.countdown_handler), this.countdown_handler = null, this.is_countdown = !1, this.countdown_hide(),  this.seq = 0)
    },
    //点击发送礼物按钮
    sendBtn:function(){
		var _this=this;
		var selected=$(".gift-select.selected");
		if(selected.length<1){
			_this.countdown_hide();
			Txbb.Pop('toast', '请选择礼物');
			return;
		}
		var giftmoney=selected.attr('data-money');
		var giftid=selected.attr('data-id');
	
		if( Number(giftmoney) > Number(User.coin) ){
			_this.countdown_hide();
			Txbb.Pop('toast', '余额不足，请充值');
			return;
		}

        //如果没有选中礼物
        if(giftid==''){
			_this.countdown_hide();
            Txbb.Pop('toast', '请选择礼物...');
            return;
        }
		
		$.ajax({
            type:"POST",
            url:'/wxshare/index.php/Share/sendGift',
            dataType:'json',
            data:{giftid:giftid,touid:to_uid,stream:room_id,"token":User.token},
            success:function(data){
                //console.log(data);
				if(data.errno==0){
					bglancemoney.text(data.coin);
					if(data.type=='0'){
						_this.countdown_show();
					}
					
					User.level=data.level;
					User.coin=data.coin;
					
					var msg = '{"retcode":"000000","retmsg":"ok","msg":[{"_method_":"SendGift","action":"0","ct":"'+data.gifttoken+'","msgtype":"1","level":"'+data.level+'","uid":"'+data.uid+'","uname":"'+User.user_nicename+'","uhead":"'+User.avatar+'","vip_type":"'+ User.vip.type +'","liangname":"'+ User.liang.name+'","usertype":"'+ User.usertype+'","guard_type":"'+ User.guard_type+'"}]}'; 
					Socket.emitData('broadcast',msg);
				}else{
					_this.countdown_hide();
					Txbb.Pop('toast',data.msg );
					return;
				}
                
            }
        })

    },

    contr_close:function(){
        $("#contributionval").removeClass("anit");
    },
    charmval:function(objbtn,url){
        var user_id=objbtn.attr("userid");
        $.ajax({
            url:url,
            type: 'get',
            dataType: 'json',
            data:{"user_id":user_id},
            success: function(data) {
                //console.log(1,data);
                var info = {
                        wealth: data.data['sum_coin'],
                        list: data.data['list']
                    };
                var html = template('ranklist', info);
                document.getElementById('contributionval').innerHTML = html;
            }
        });
        $("#contributionval").addClass("anit");
    },
    userpicBtn:function(objbtn,url){
		var user_id=objbtn.attr("user_id");
		//console.log(user_id);
		if(userinfocon.is(":hidden")){
			$.ajax({
				url:url,
				type: 'get',
				dataType: 'json',
				data:{"uid":user_id},
				success: function (data) {
					var html = template('userinfo', data.data);
					document.getElementById('user_info_con').innerHTML = html;
					userinfocon.show();
				}
			});
		}else{
		   userinfocon.hide();
		}
    },
    userinfoBtn:function(objbtn,url){
        var user_id=objbtn.attr("userid");
		//console.log(user_id);
		var profileData;
		if(User.islogin == "true"){
			profileData={"uid":user_id,"token":User.token};
		}else{
			profileData={"uid":user_id};
		}
		//console.log(profileData);
		if(userinfocon.is(":hidden")){
			$.ajax({
				url:url,
				type: 'get',
				dataType: 'json',
				data:profileData,
				success: function (data) {
					//console.log("asd",data);
					if(data.code == 0){
						var html = template('anchorInfo', data.data);
						document.getElementById('user_info_con').innerHTML = html;
						userinfocon.show();
					}else{
						alert(data.msg);
					}

				}
			});
		}else{
		   userinfocon.hide();
		}
    },
    pcanchorinfo:function(objbtn,url){
        var user_id=objbtn.attr("userid");
		$.ajax({
			url:url,
			type: 'POST',
			dataType: 'json',
			data:{"uid":user_id},
			cache: false,
			success: function (data) {
				var json=eval(data.data);
				var html = template('anchorInfo', json);
				document.getElementById('anchorinfo').innerHTML = html;
			}   
		});
    },
    iShare:function(objbtn){
        $("#share_alert").show();
        $(".share_box").removeClass("sanimt");
        if(objbtn.hasClass("iShare_wechat")){
            $(".share_prompt p").html("分享到微信，请点击右上角</br>再选择【分享给朋友】")
        }else{
            $(".share_prompt p").html("分享到QQ，请点击右上角</br>再选择【分享到手机QQ】")
        }
        
    },
    userFollowed:function(objbtn){
        if(objbtn.attr("data-follow")==0){
            objbtn.text("已关注").css("background","rgba(235,79,56,1)");
            objbtn.attr("data-follow","1");
        }else{
            objbtn.text("关注").css("background","rgba(235,79,56,0.6)");;
            objbtn.attr("data-follow","0");
        }
    }


}