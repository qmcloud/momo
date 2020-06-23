function fullRoom(uid)
{
	$('#giveBox').hide();
  $.ajax({
    type:"post",
    url:"./index.php?g=admin&m=Monitor&a=full",
    data:{uid:uid},
    success:function(data)
		{
      $("#buyvip").css({"left":getMiddlePos('buyvip').pl+"px","z-index":2100,}).show();
      $("#buyvip").html(data);
    }
  });
}
function closePorp()
{
	$('#buyvip').hide();
  $('#buyvip').html("");
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
