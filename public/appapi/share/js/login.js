var Login={
	reg_url:'/wxshare/index.php/Share/userLogin',
	code_url:'/wxshare/index.php/Share/getCode',
	type:'reg',
	checkPhone:/^0?1[3|4|5|7|8|9][0-9]\d{8}$/,
	init:function(){
		this.addEvent();
	},
	addEvent:function(){
		this.js_reg_phone_input=$(".js_reg_phone_input"),
		this.js_reg_code_input=$(".js_reg_code_input"),
		this.js_reg_getcode=$(".js_reg_getcode"),
		this.js_reg_submit=$(".js_reg_submit"),
		this.js_reg_pop=$("#login");
		//this.reg();
		
		var _this=this;
		$(".js-reg").on("click",function(e){
			console.log(11),
			e.preventDefault(), _this.reg()
		})
		$("#login").click(function(e) {
            var target = $(e.target);
            //点击其他地方隐藏礼物列表
            if(!target.is('.login_form') && !target.is(".login_form *")) {
               _this.closePop()
            }

        });
		/* _this.js_close.on("click",function(){
			_this.closePop()
		}) */
	},
	closePop:function(){
		this.js_reg_pop.fadeOut(300)
	},
	loginWarring:function(msg){
		var e = null,n;
		n = $(".js_"+this.type+"_warring"), 
		n.text(msg).show(), 
		e && (window.clearTimeout(e), e = null), e = window.setTimeout(function() {
					e = null, n.fadeOut(300)
				}, 5e3)
	},
	regVerify:function(){
		var _this=this;
		var phone=_this.js_reg_phone_input,
			code=_this.js_reg_code_input,
			getcode=_this.js_reg_getcode,
			reg=_this.js_reg_submit;
		
		phone.on("keyup input propertychange", function() {
			var e = $.trim($(this).val());
			e.length > 10 ? ( C() ?  GY(): GN() ): GN() , W()
		})
		code.on("keyup input propertychange", function() {
			W()
		})
		
		/* captcha.on("keyup input propertychange", function() {
			var e = $.trim($(this).val());
			e.length == 4 ? CapY(): CapN() 
		}) */
		/* captchaimg.on("click",function(){
			getCaptcha()
		}) */
		function C(){
			if(!_this.checkPhone.test(phone.val())){
				_this.loginWarring("您输入的手机号有误")
				return !1;
			}
			return !0;
		}
		/* 点击 获取验证码 */
		function G(){
			//getCaptcha()
			getCodeU()
		}
		function GY() {
			getcode.hasClass("login_counting") || getcode.removeClass("get_none").addClass("get_key").unbind().click(G)
		}
		function GN() {
			getcode.unbind().removeClass("get_key").addClass("get_none")
		}
		/* 计时器 */
		function O(){
			function r(e) {
				getcode.text(e)
			}
			GN()/* , CapN() */;
			console.log(11);
			var e = 60,
				n = window.setInterval(function() {
					if (e > 0) {
						var i = e--+"s 重新获取";
						getcode.addClass("login_counting"), r(i)
					} else window.clearInterval(n), n = null, r("获取验证码"), getcode.removeClass("login_counting"), C() && GY()
				}, 1e3)			
		}
		/* 验证码 */
		/* function Cap(){
				_this.type='captcha',getCodeU()
		}
		function CapY(){
			captchasub.removeClass("get_none").addClass("get_key").unbind().click(Cap)
		}
		function CapN(){
			captchasub.unbind().removeClass("get_key").addClass("get_none")
		} */
		/* 注册页面 */
		function W(){
			d = $.trim(phone.val()), c = $.trim(code.val()), d.length > 10 && c.length == 6 ? Y() : N()
		}
		function Y() {
			reg.removeClass("get_none").addClass("get_key").unbind().click(R)
		}
		function N() {
			reg.unbind().removeClass("get_key").addClass("get_none")
		}
		/* 发送短信验证码 */
		function getCodeU(){
			$.ajax({
				url: _this.code_url,
				data: {mobile:phone.val()/* ,captcha:captcha.val() */},
				type: "GET",
				dataType: "json",
				cache: !1,
				success:function(data){
					if(data && data.errno ==0){
						_this.type='reg',/* popbox.fadeOut(300), */O()
					}else{
						_this.loginWarring(data.errmsg),CapN()/* ,getCaptcha() */;
						return !1;
					}
					
				},
				error:function(e){
					console.log(e);
				}
			})			
		}
		/* 图片验证码 */
		function getCaptcha(){
			$.ajax({
				url: _this.captcha_url,
				data: {},
				type: "GET",
				dataType: "jsonp",
				jsonp: "callback",
				cache: !1,
				success:function(data){
					if(data && data.errno ==0){
						_this.js_reg_captcha_img.attr("src",data.data.captcha+"&v=" + parseInt(Math.random() * 1e8, 10));
					}else{
						_this.loginWarring(data.errmsg);
						return !1;
					}
					
				}
			})			
		}
		function R(){
			_this.doreg();
		}		
	},
	reg:function(){
		this.js_reg_pop.fadeIn(300),
		this.type='reg',		
		this.regVerify()
	},
	doreg:function(){
		var _this=this,
			phone=this.js_reg_phone_input.val(),
			code =this.js_reg_code_input.val();
		$.ajax({
			url: _this.reg_url,
			data: {mobile:phone,code:code},
			type: "GET",
			dataType: "json",
			cache: !1,
			success:function(data){
				if(data && data.errno ==0){
					window.location.reload();
				}else{
					_this.loginWarring(data.errmsg);
					return !1;
				}
				
			}
		})				
	}
}
$(function(){
	Login.init();
});

