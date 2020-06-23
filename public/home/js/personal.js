$(function() 
{
	var Accordion = function(el, multiple) 
	{
		this.el = el || {};
		this.multiple = multiple || false;
		var links = this.el.find('.link');
		links.on('click', {el: this.el, multiple: this.multiple}, this.dropdown)
	}
	Accordion.prototype.dropdown = function(e) 
	{
		var $el = e.data.el;
		$this = $(this),
		$next = $this.next();
		$next.slideToggle();
		$this.parent().toggleClass('open');
		if (!e.data.multiple) 
		{
			$el.find('.submenu').not($next).slideUp().parent().removeClass('open');
		};
	}	
	var accordion = new Accordion($('#accordion'), false);
});
$('#coin').bind('input propertychange', function() {
		var coin = $("#coin").val();
		var coin_z = document.getElementById('coin_z').innerHTML;
		if(parseInt(coin)>parseInt(coin_z))
		{
			document.getElementById('coin_c').style.display='block';
		}
	  else
		{
			document.getElementById('coin_c').style.display='none';
		}
  });
/*
个人中心
*/
var Personal={
	baocunj:function()
	{
		var nickName = $("#nickName").val();
		var signature=$("#signature").val();
		var radios=document.getElementsByName("gender");
		var birthday=$("#birthday").val();
		if(signature.length>20)
		{
			layer.msg("签名字数最多20个字");
			return;
		}
		if(nickName.length>8)
		{
			layer.msg("昵称最多8个字符");
			return;
		}
		for(var i=0;i<radios.length;i++)
		{
      if(radios[i].checked==true)
       {
				 var sex=radios[i].value;
       }
    }
		if(nickName.length==0 ||nickName=="")
		{
			layer.msg("昵称不能为空");
			return;
		}
		$.ajax({
			cache: true,
			type: "POST",
			url:'/index.php?g=home&m=Personal&a=edit_modify',
			data:{nickName:nickName,signature:signature,sex:sex,birthday:birthday},
			async: false,
			error: function(request)
			{
				layer.msg("数据请求失败");
			},
			success: function(data)
			{
				var str = JSON.parse(data);
				if(str["state"]==0)
				{
					pupop(str["msg"],1);
				}
				else
				{
					pupop(str["msg"],2);
				}
			}
    });
	},
	admin_del:function(touid)
	{
		$.ajax({
			cache: true,
			type: "POST",
			url:'/index.php?g=home&m=Personal&a=admin_del',
			data:{touid:touid},
			async: false,
			error: function(request)
			{
				layer.msg("数据请求失败");
			},
			success: function(data)
			{
				var str = JSON.parse(data);
				if(str['state']==0)
				{
					pupop(str['msg'],1);
				}
				else
				{
					pupop(str['msg'],2);
				}
			}
    });
	},
	/*个人中心拉黑*/
	blacklist:function(touid){
		
		$.ajax({
			url: '/index.php?g=Home&m=Personal&a=blacklist',
			type: 'POST',
			dataType: 'json',
			data: {touid:touid},
			success:function(data){
				if(data.state==0){
					pupop(data.msg,1);
				}else{
					pupop(data.msg,2);
				}

			},
			error:function(e){

				layer.msg("数据请求失败");
			}
		});
		
		
	},
	/*取消关注*/
	cancel:function(followID)
	{
		$.getJSON("/index.php?g=home&m=Personal&a=follow_dal", {followID:followID},
		function(data) 
			{
				if(data["state"]==0)
				{
					pupop(data['msg'],1);
				}
				else
				{
					pupop(data['msg'],2);
				}
		});
	},
	/*添加关注*/
	follow_add:function(touid)
	{
		$.getJSON("/index.php?g=home&m=Personal&a=follow_add",{touid:touid},
		function(data) 
			{
				if(data["state"]==0)
				{
					pupop(data['msg'],1);
				}
				else
				{
					pupop(data['msg'],2);
				}
		});
	},
	played:function(touid)
	{
		$('#giveBox').hide();
		$.ajax({
			type:"post",
			url:"/index.php?g=home&m=Playback&a=index",
			data:{touid:touid},
			success:function(data)
			{
				if(data=='2')
				{
					$('.hd-login .no-login').click();
				}
				else
				{
					document.getElementById('ds-dialog-bg').style.display='block';
					$("#buyvip").css({"left":getMiddlePos('buyvip').pl+"px","z-index":210,}).show();
					$("#buyvip").html(data);
				}
			}
		});

	},
	/*移除黑名单*/
	list_del:function(touid)
	{
		$.getJSON("/index.php?g=home&m=Personal&a=list_del",{touid:touid},
		function(data) 
			{
				if(data["state"]==0)
				{
					pupop(data['msg'],1);
				}
				else
				{
					pupop(data['msg'],2);
				}
		});
	},
	exchange:function()
	{
		$.getJSON("/index.php?g=home&m=Personal&a=edit_exchange", {},
		function(data) 
			{
				 if(data['state']==0)
					{
						pupop(data['msg'],1);
					}
				else
				{
					pupop(data['msg'],2);
				} 			
		});
	},
	photo:function()
	{
		var avatar =encodeURIComponent($("#thumb").val());
		$.getJSON("/index.php?g=home&m=Personal&a=edit_photo", {avatar:avatar},
		function(data) 
			{
				if(data['error']==1)
				{
					pupop(data['type'],1);
				}
				else
				{
					pupop(data['type'],2);
				}
		});
	},
	/*修改密码*/
	updatepass:function()
	{
		//当前密码
		var oldpass = $("#oldpass").val();
		//新密码
		var newpass=$("#newpass").val();
		//确认密码
		var repass=$("#repass").val();
		if(newpass!=""&&repass!="")
		{
			$.getJSON("/index.php?g=home&m=Personal&a=savepass", {oldpass:oldpass,newpass:newpass,repass:repass},
			function(data) 
			{
				if(data["code"]==0)
				{
					layer.msg('修改完成');
					window.location.href="";
				}
				else
				{
					layer.msg(data['msg']);	
				}
			});
		}
		else
		{
			layer.msg('密码不能为空');
		}
	}
}
function personal_data()
{
	$("#fpost").submit();	
}
function pupop(msg,icon)
{
	layer.alert(msg, 
	{
		skin: 'layui-layer-molv' //样式类名
		,closeBtn: 0,
		shift: 5,
		icon: icon
	}, function(index)
	{   
        layer.close(index);
        if(icon==1){
            window.location.href="";
        }
		
	});
}
/**
 * 获取layer居中的位置
 */
var getMiddlePos=function(obj){
    this.objPop=obj;
    this.winW=oPos.windowWidth(); 
    this.winH=oPos.windowHeight(); 
    this.dScrollTop=oPos.scrollY();
    this.dScrollLeft=oPos.scrollX();
    this.dWidth=$('#'+this.objPop).width(),dHeight=$('#'+this.objPop).height();
    this.dLeft=(this.winW/2)-(this.dWidth)/2+this.dScrollLeft;
    this.dTop=(this.winH/2)-(this.dHeight/2)+this.dScrollTop;
    return {"pl":this.dLeft,'pt':this.dTop};
}
/**
 * 判断浏览器
 */
var Sys={};
var Gift_obj={};
var Gift_numobj={};
var ua=navigator.userAgent.toLowerCase();
Sys.ie=(s=ua.match(/msie ([\d.]+)/)) ? true : false;
Sys.ie6=(s=ua.match(/msie ([0-6]\.+)/)) ? s[1] : false;
Sys.ie7=(s=ua.match(/msie ([7]\.+)/)) ? s[1] : false;
Sys.ie8=(s=ua.match(/msie ([8]\.+)/)) ? s[1] : false;
Sys.firefox=(s=ua.match(/firefox\/([\d.]+)/)) ? true : false;
Sys.chrome=(s=ua.match(/chrome\/([\d.]+)/)) ? true : false;
Sys.opera=(s=ua.match(/opera.([\d.]+)/)) ? s[1] : false;
Sys.safari=(s=ua.match(/version\/([\d.]+).*safari/)) ? s[1] : false;
Sys.ie6&&document.execCommand("BackgroundImageCache",false,true);
Sys.ispro="";//是否推广url过来
String.prototype.hasString=function(a){
    if(typeof a=="object"){
        for(var b=0,c=a.length;b<c;b++)
           if(!this.hasString(a[b]))
                 return false;
            return true
    }else if(this.indexOf(a)!=-1)
        return true
};

/**
 * 计算位置
 */
var dom=document.documentElement || document.body;
var oPos={
    width:function(a){return parseInt(a.offsetWidth)},
    height:function(a){return parseInt(a.offsetHeight)},
    pageWidth:function(){return document.body.scrollWidth||document.documentElement.scrollWidth},
    pageHeight:function(){return document.body.scrollHeight||document.documentElement.scrollHeight},
    windowWidth:function(){var a=document.documentElement;return self.innerWidth||a&&a.clientWidth||document.body.clientWidth},
    windowHeight:function(){var a=document.documentElement;return self.innerHeight||a&&a.clientHeight||document.body.clientHeight},
    scrollX:function(){
      var b=document.documentElement;
      return self.pageXOffset||b&&b.scrollLeft||document.body.scrollLeft
    }
    ,scrollY:function(){
      var b=document.documentElement;
      return self.pageYOffset||b&&b.scrollTop||document.body.scrollTop
    },
    popW:function(){return Math.max(dom.clientWidth,dom.scrollWidth)},
    popH:function(){return Math.max(dom.clientHeight,dom.scrollHeight)}
}
var mousePosition=function(e){
  var e=e || window.event;
  return {x:e.clientX+oPos.scrollX(),y:e.clientY+oPos.scrollY()}
};
