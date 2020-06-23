var Login={
	login_url:'./index.php?g=home&m=user&a=userLogin',
	reg_url:'./index.php?g=home&m=user&a=userReg',
	loginout_url:'./index.php?g=home&m=user&a=logout',
	forget_url:'./index.php?g=home&m=user&a=forget',
	captcha_url:'./index.php?g=home&m=user&a=getCaptcha',
	code_url:'./index.php?g=home&m=user&a=getCode',
	dombody:$("body"),
	type:'login',
	checkPhone:/^0?1[3|4|5|7|8|9][0-9]\d{8}$/,
	checkPass:/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,12}$/,
	interval_reg:null,
	interval_forget:null,
	init:function(){
		this.threeOpen();
	},
	threeOpen:function(){
		$.ajax(
		{
			url:"./index.php?g=home&m=user&a=threeparty",
			data:{},
			dataType:'json',
			success:function(data)
			{
				
				Login.dom(data);
				Login.addEvent();
			}
		});
	},
	dom:function(data){
		//console.log(data);
		var threeQQ='',threeWeibo='',threeWeixin='';
		var login_html='<div class="login_pop js_login_pop">\
											<div class="title">\
												<span class="close js_close js_login_close"></span>\
												登 录\
											</div><article>';
			 	if($.inArray('qq',data.login_type)>=0)
				{
					threeQQ='<div class="qq js_qq js_login_qq"><span></span></div>';
				}
				if($.inArray('sina',data.login_type)>=0)
				{
					threeWeibo='<div class="weibo js_weibo js_login_weibo"><span></span></div>';
				}	
				if($.inArray('wx',data.login_type)>=0)
				{
					threeWeixin='<div class="weixin js_weixin js_login_weixin"><span></span></div>';
				} 

									var		lower_html='</article><article>\
												<div class="tips"></div>\
											</article>\
											<article>\
												<div class="warring js_login_warring">请输入手机号码</div>\
												<div class="phoneArea">\
													<i class="phoneIcon"></i>\
													<select id="countrycode">\
															<option value ="86">86</option>\
															<option value ="886">886</option>\
													</select>\
													<input class="phone  js_login_phone_input" type="text" placeholder="输入手机号码" maxlength="11">\
												</div>\
												<div class="phoneArea">\
													<i class="phoneIcon"></i>\
													<input class="pass js_login_pass_input" type="password" placeholder="输入密码">\
												</div>\
												<p><span class="login-btn fl js-reg">注册</span><span class="login-btn fr js-forget">忘记密码</span></p>\
												<p>登录即表示您同意<em><a style="color: #da5537;" href="./index.php?m=page&a=agreement" target="_blank">《用户协议》</a></em></p>\
												<a class="submit js_login_submit get_none">确认</a>\
											</article>\
											<p class="other_login_tip"></p>\
										</div>',
					reg_html='<div class="login_pop js_reg_pop">\
											<div class="title">\
												<span class="close js_close js_login_close"></span>\
												注 册\
											</div>\
											<article>';
						var signin='</article>\
											<article>\
												<div class="tips"></div>\
											</article>\
											<article>\
												<div class="warring js_reg_warring">请输入手机号码</div>\
												<div class="phoneArea">\
													<i class="phoneIcon"></i>\
													<select id="countrycode_reg">\
															<option value ="86">86</option>\
															<option value ="886">886</option>\
													</select>\
													<input class="phone js_reg_phone_input" type="text" placeholder="输入手机号码" maxlength="11">\
												</div>\
												<div class="phoneArea">\
													<i class="phoneIcon"></i>\
													<input class="pass js_reg_pass_input" type="password" placeholder="输入密码" >\
												</div>\
												<div class="phoneArea">\
													<i class="phoneIcon"></i>\
													<input class="pass js_reg_repass_input" type="password" placeholder="输入确认密码" >\
												</div>\
												<div class="key_con">\
													<div class="keyBorder">\
														<i class="keyIcon"></i>\
														<input class="key js_reg_code_input" type="text" placeholder="输入验证码" maxlength="6">\
													</div>\
													<a class="get_none js_reg_getcode">获取验证码</a>\
												</div>\
												<p><span class="js-login login-btn fl">登录</span><span class="login-btn fr js-forget">忘记密码</span></p>\
												<p>注册即表示您同意<em><a style="color: #da5537;" href="./index.php?m=page&a=agreement" target="_blank">《用户协议》</a></em></p>\
												<a class="submit js_reg_submit get_none">确认</a>\
											</article>\
											<p class="other_login_tip"></p>\
											<div class="login_popbox js_reg_popbox">\
												<div class="inner">\
													<i class="close"></i>\
													<div class="warring js_captcha_warring"></div>\
													<input class="js_reg_captcha_input" type="text" placeholder="请输入右侧字符" maxlength="20"/>\
													<img src=""/ class="js_reg_captcha_img">\
													<a class="js_reg_captcha get_none">确定</a>\
												</div>\
											</div>\
										</div>',	
					forget_html='<div class="login_pop js_forget_pop">\
											<div class="title">\
												<span class="close js_close js_login_close"></span>\
												忘记密码\
											</div>\
											<article style="display:none;">\
												<div class="weibo js_weibo js_login_weibo"><span></span></div>\
												<div class="weixin js_weixin js_login_weixin"><span></span></div>\
												<div class="qq js_qq js_login_qq"><span></span></div>\
											</article>\
											<article>\
												<div class="tips" style="display:none;"></div>\
											</article>\
											<article>\
												<div class="warring js_forget_warring">请输入手机号码</div>\
												<div class="phoneArea">\
													<i class="phoneIcon"></i>\
													<select id="countrycode_forget">\
															<option value ="86">86</option>\
															<option value ="886">886</option>\
													</select>\
													<input class="phone js_forget_phone_input" type="text" placeholder="输入手机号码" maxlength="11">\
												</div>\
												<div class="phoneArea">\
													<i class="phoneIcon"></i>\
													<input class="pass js_forget_pass_input" type="password" placeholder="输入新密码" >\
												</div>\
												<div class="phoneArea">\
													<i class="phoneIcon"></i>\
													<input class="pass js_forget_repass_input" type="password" placeholder="输入确认密码" >\
												</div>\
												<div class="key_con">\
													<div class="keyBorder">\
														<i class="keyIcon"></i>\
														<input class="key js_forget_code_input" type="text" placeholder="输入验证码" maxlength="6">\
													</div>\
													<a class="get_none js_forget_getcode">获取验证码</a>\
												</div>\
												<p><span class="login-btn fl js-reg">注册</span><span class="login-btn fr js-login">登录</span></p>\
												<a class="submit js_forget_submit get_none">确认</a>\
											</article>\
											<p class="other_login_tip"></p>\
											<div class="login_popbox js_forget_popbox">\
												<div class="inner">\
													<i class="close"></i>\
													<div class="warring js_forget_captcha_warring"></div>\
													<input class="js_forget_captcha_input" type="text" placeholder="请输入右侧字符" maxlength="20"/>\
													<img src=""/ class="js_forget_captcha_img">\
													<a class="js_forget_captcha get_none">确定</a>\
												</div>\
											</div>',								
					login_html_bg = '<div class="login_pop_cover js_login_pop_cover"></div>';
	
			this.dombody.append(login_html_bg+login_html+threeQQ+threeWeibo+threeWeixin+lower_html+reg_html+threeQQ+threeWeibo+threeWeixin+signin+forget_html);
	},
	addEvent:function(){
		this.js_login_pop=$(".js_login_pop"),
		this.js_login_pop_cover=$(".js_login_pop_cover"),
		this.js_reg_pop=$(".js_reg_pop"),
		this.js_forget_pop=$(".js_forget_pop"),
		this.js_close=$(".js_close"),
		this.js_login_phone_input=$(".js_login_phone_input"),
		this.js_login_pass_input=$(".js_login_pass_input"),
		this.countrycode=$("#countrycode"),
		this.js_login_submit=$(".js_login_submit"),
		
		this.js_reg_phone_input=$(".js_reg_phone_input"),
		this.js_reg_pass_input=$(".js_reg_pass_input"),
		this.js_reg_repass_input=$(".js_reg_repass_input"),
		this.js_reg_code_input=$(".js_reg_code_input"),
		this.js_reg_getcode=$(".js_reg_getcode"),
		this.js_reg_popbox=$(".js_reg_popbox");
		this.popboxclose=$(".login_popbox .close");
		this.js_reg_captcha_input=$(".js_reg_captcha_input"),
		this.js_reg_captcha_img=$(".js_reg_captcha_img"),
		this.js_reg_captcha=$(".js_reg_captcha");
		this.js_reg_submit=$(".js_reg_submit");
		this.countrycode_reg=$("#countrycode_reg");
		
		this.js_forget_phone_input=$(".js_forget_phone_input"),
		this.js_forget_pass_input=$(".js_forget_pass_input"),
		this.js_forget_repass_input=$(".js_forget_repass_input"),
		this.js_forget_code_input=$(".js_forget_code_input"),
		this.js_forget_getcode=$(".js_forget_getcode"),
		this.js_forget_popbox=$(".js_forget_popbox");
		this.js_forget_captcha_input=$(".js_forget_captcha_input"),
		this.js_forget_captcha_img=$(".js_forget_captcha_img"),
		this.js_forget_captcha=$(".js_forget_captcha");
		this.js_forget_submit=$(".js_forget_submit");
		this.countrycode_forget=$("#countrycode_forget");
		
		var _this=this;
		$(".hd-login .no-login").on("click",function(e){
			e.preventDefault(), _this.login()
		}), 
		$(".js-login").on("click",function(e){
			e.preventDefault(), _this.login()
		}), 
		$(".js-reg").on("click",function(e){
			e.preventDefault(), _this.reg()
		}), 
		$(".js-forget").on("click",function(e){
			e.preventDefault(), _this.forget()
		}), 
		$("#beloginBox .login").on("click",function(e){
			e.preventDefault(), _this.login()
		}),
		$("#beloginBox .reg").on("click",function(e){
			e.preventDefault(), _this.reg()
		}),
		$(".hd-login .logout").click(function(e) {
				e.preventDefault(), _this.logout()
		}), 
		$(".hd-login .already-login").on("click", function() {
				$(".icon-more-ed").length === 0 ? $(".icon-more").addClass("icon-more-ed") : $(".icon-more").removeClass("icon-more-ed"), $(".userinfo").fadeToggle(300)
		}), 
		$(".hd-nav .more").hover(function(e) {
			$(this).addClass("hover")
		}, function(e) {
			$(this).removeClass("hover")
		}).on("click", ".link", function(e) {
			e.preventDefault()
		}),
		_this.js_close.on("click",function(){
			_this.closePop()
		})
		_this.popboxclose.on("click",function(){
			$(".login_popbox").fadeOut(300)
		})
		
		$(".js_login_weibo").click(function() {
			window.location.href = "index.php?g=home&m=User&a=weibo";
		}), $(".js_login_weixin").click(function() {
			window.location.href = "index.php?g=home&m=User&a=weixin";
		}), $(".js_login_qq").click(function() {
			/* alert("等待第三方配置...") */
			window.location.href = "index.php?g=home&m=User&a=qq";
		})		
	},
	closePop:function(){
		this.js_login_pop.fadeOut(300),
		this.js_reg_pop.fadeOut(300),
		this.js_forget_pop.fadeOut(300),
		this.js_login_pop_cover.fadeOut(300),
		$(".js_reg_pop input").val("");
		$(".js_login_pop input").val("");
		$(".js_forget_pop input").val("");
	
	},
	loginVerify:function(){
		var _this=this;
		var phone=_this.js_login_phone_input,pass=_this.js_login_pass_input,login=_this.js_login_submit;
		
		phone.on("keyup input propertychange", function() {
			var e = $.trim($(this).val());
			e.length > 7 && W()
		})
		pass.on("keyup input propertychange", function() {
			W()
		})

		function W(){
			d = $.trim(phone.val()), v = $.trim(pass.val()), d.length > 7 && v.length > 5 ? Y() : N()
		}
		function Y() {
			login.removeClass("get_none").addClass("get_key").unbind().click(L)
		}
		function N() {
			login.unbind().removeClass("get_key").addClass("get_none")
		}
		function L(){
			_this.dologin();
		}
	},
	loginWarring:function(msg){
		var e = null,t, n;
		t = $(".login_popbox"), t.is(":hidden") ? n = $(".js_"+this.type+"_warring") : n = $(".login_popbox .js_"+this.type+"_warring"), n.text(msg).show(), e && (window.clearTimeout(e), e = null), e = window.setTimeout(function() {
					e = null, n.fadeOut(300)
				}, 5e3)
	},
	login:function(){
		this.js_login_pop_cover.fadeIn(300),
		this.js_reg_pop.fadeOut(300),
		this.js_forget_pop.fadeOut(300),
		this.js_login_pop.fadeIn(300),
		this.type='login',
		this.loginVerify()
	},
	dologin:function(){
		var _this=this,
				phone=this.js_login_phone_input.val(),
				pass =this.js_login_pass_input.val();
				countrycode =$('#countrycode option:selected') .val();
		$.ajax({
			url: _this.login_url,
			data: {mobile:phone,pass:pass,countrycode:countrycode},
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
		
	},
	regVerify:function(){
		var _this=this;
		var phone=_this.js_reg_phone_input,pass=_this.js_reg_pass_input,repass=_this.js_reg_repass_input,code=_this.js_reg_code_input,getcode=_this.js_reg_getcode,popbox=_this.js_reg_popbox,captcha=_this.js_reg_captcha_input,captchaimg=_this.js_reg_captcha_img,captchasub=_this.js_reg_captcha,reg=_this.js_reg_submit;
		var countrycode_reg=$('#countrycode_reg option:selected') .val();
		window.clearInterval(_this.interval_reg), _this.interval_reg = null, r("获取验证码"), getcode.removeClass("login_counting"), C(0) && GY();
		
		phone.on("keyup input propertychange", function() {
			var e = $.trim($(this).val());
			e.length > 7 ? ( C(1) ?  GY(): GN() ): GN() , W()
		})
		pass.on("keyup input propertychange", function() {
			W()
		})
		repass.on("keyup input propertychange", function() {
			W()
		})
		code.on("keyup input propertychange", function() {
			W()
		})
		captcha.on("keyup input propertychange", function() {
			var e = $.trim($(this).val());
			e.length == 4 ? CapY(): CapN() 
		})
		captchaimg.on("click",function(){
			getCaptcha()
		})
		function C(a){
			/* if(!_this.checkPhone.test(phone.val())){
				if(a){
					_this.loginWarring("您输入的手机号有误")
				}
				
				return !1;
			} */
			return !0;
		}
		/* 点击 获取验证码 */
		function G(){
			popbox.fadeIn(300),getCaptcha()
		}
		function GY() {
			getcode.hasClass("login_counting") || getcode.removeClass("get_none").addClass("get_key").unbind().click(G)
		}
		function GN() {
			getcode.unbind().removeClass("get_key").addClass("get_none")
		}
		function r(e) {
			getcode.text(e)
		}
		/* 计时器 */
		function O(){
			GN(), CapN();
			var e = 60;
				_this.interval_reg = window.setInterval(function() {
					if (e > 0) {
						var i = e--+"s 重新获取";
						getcode.addClass("login_counting"), r(i)
					} else window.clearInterval(_this.interval_reg), _this.interval_reg = null, r("获取验证码"), getcode.removeClass("login_counting"), C(0) && GY()
				}, 1e3)			
		}
		/* 验证码 */
		function Cap(){
				_this.type='captcha',getCodeU()
		}
		function CapY(){
			captchasub.removeClass("get_none").addClass("get_key").unbind().click(Cap)
		}
		function CapN(){
			captchasub.unbind().removeClass("get_key").addClass("get_none")
		}
		/* 注册页面 */
		function W(){
			d = $.trim(phone.val()), v = $.trim(pass.val()), rv = $.trim(repass.val()), c = $.trim(code.val()), d.length > 7 && v.length > 5 && rv.length > 5 && c.length == 6 ? Y() : N()
		}
		function Y() {
			reg.removeClass("get_none").addClass("get_key").unbind().click(R)
		}
		function N() {
			reg.unbind().removeClass("get_key").addClass("get_none")
		}
		/* 发送短信验证码 */
		function getCodeU(){
			var countrycode_reg=$('#countrycode_reg option:selected') .val();
			$.ajax({
				url: _this.code_url,
				data: {mobile:phone.val(),captcha:captcha.val(),type:'reg',countrycode:countrycode_reg},
				type: "GET",
				dataType: "json",
				cache: !1,
				success:function(data){
					if(data && data.errno ==0){
						_this.type='reg',popbox.fadeOut(300),O()
					}else{
						_this.loginWarring(data.errmsg),CapN(),getCaptcha();
						return !1;
					}
					
				}
			})			
		}
		/* 图片验证码 */
		function getCaptcha(){
			$.ajax({
				url: _this.captcha_url,
				data: {},
				type: "GET",
				dataType: "json",
				cache: !1,
				success:function(data){
					if(data && data.errno ==0){
                        captcha.val('');
						_this.js_reg_captcha_img.attr("src",data.data.captcha+"&v=" + parseInt(Math.random() * 1e8, 10));
					}else{
						_this.loginWarring(data.errmsg);
						return !1;
					}
					
				}
			})			
		}
		function R(){
			if(!_this.checkPass.test(pass.val())){
				_this.loginWarring("密码必须包含字母和数字，6-12位")
				return !1;
			}
			if( pass.val()!=repass.val() ){
				_this.loginWarring("两次密码不一致")
				return !1;
			}
			_this.doreg();
		}		
	},
	reg:function(){
		this.js_login_pop_cover.fadeIn(300),
		this.js_login_pop.fadeOut(300),		
		this.js_forget_pop.fadeOut(300),		
		this.js_reg_pop.fadeIn(300),
		this.type='reg',		
		this.regVerify()
	},
	doreg:function(){
		var _this=this,
				phone=this.js_reg_phone_input.val(),
				pass =this.js_reg_pass_input.val(),
				code =this.js_reg_code_input.val();
				countrycode_reg=$('#countrycode_reg option:selected') .val();
		$.ajax({
			url: _this.reg_url,
			data: {mobile:phone,pass:pass,code:code,countrycode:countrycode_reg},
			type: "GET",
			dataType: "json",
			cache: !1,
			success:function(data){
				if(data && data.errno ==0){
					//获取注册用户的id
					var userid=data['userid'];
					layer.msg("注册成功！",{},function(){
						window.location.reload();//页面刷新
					});
				}else{
					_this.loginWarring(data.errmsg);
					return !1;
				}
				
			}
		})				
	},
	logout:function(){
		var _this=this;
		$.ajax({
			url: _this.loginout_url,
			data: {},
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
		
	},
	forgetVerify:function(){
		var _this=this;
		var phone=_this.js_forget_phone_input,pass=_this.js_forget_pass_input,repass=_this.js_forget_repass_input,code=_this.js_forget_code_input,getcode=_this.js_forget_getcode,popbox=_this.js_forget_popbox,captcha=_this.js_forget_captcha_input,captchaimg=_this.js_forget_captcha_img,captchasub=_this.js_forget_captcha,forget=_this.js_forget_submit;
		
		var countrycode_forget=$('#countrycode_forget option:selected') .val();
		
		window.clearInterval(_this.interval_forget), _this.interval_forget = null, r("获取验证码"), getcode.removeClass("login_counting"), C(0) && GY();
		
		phone.on("keyup input propertychange", function() {
			var e = $.trim($(this).val());
			e.length > 7 ? ( C(1) ?  GY(): GN() ): GN() , W()
		})
		pass.on("keyup input propertychange", function() {
			W()
		})
		repass.on("keyup input propertychange", function() {
			W()
		})
		code.on("keyup input propertychange", function() {
			W()
		})
		captcha.on("keyup input propertychange", function() {
			var e = $.trim($(this).val());
			e.length == 4 ? CapY(): CapN() 
		})
		captchaimg.on("click",function(){
			getCaptcha()
		})
		function C(a){
			/* if(!_this.checkPhone.test(phone.val())){
				if(a){
					_this.loginWarring("您输入的手机号有误")
				}
				
				return !1;
			} */
			return !0;
		}
		/* 点击 获取验证码 */
		function G(){
			popbox.fadeIn(300),getCaptcha()
		}
		function GY() {
			getcode.hasClass("login_counting") || getcode.removeClass("get_none").addClass("get_key").unbind().click(G)
		}
		function GN() {
			getcode.unbind().removeClass("get_key").addClass("get_none")
		}
		function r(e) {
			getcode.text(e)
		}
		/* 计时器 */
		function O(){
			GN(), CapN();
			var e = 60;
				_this.interval_forget = window.setInterval(function() {
					if (e > 0) {
						var i = e--+"s 重新获取";
						getcode.addClass("login_counting"), r(i)
					} else window.clearInterval(_this.interval_forget), _this.interval_forget = null, r("获取验证码"), getcode.removeClass("login_counting"), C(0) && GY()
				}, 1e3)			
		}
		/* 验证码 */
		function Cap(){
				_this.type='forget_captcha',getCodeU()
		}
		function CapY(){
			captchasub.removeClass("get_none").addClass("get_key").unbind().click(Cap)
		}
		function CapN(){
			captchasub.unbind().removeClass("get_key").addClass("get_none")
		}
		/* 忘记密码页面 */
		function W(){
			d = $.trim(phone.val()), v = $.trim(pass.val()), rv = $.trim(repass.val()), c = $.trim(code.val()), d.length > 7 && v.length > 5 && rv.length > 5 && c.length == 6 ? Y() : N()
		}
		function Y() {
			forget.removeClass("get_none").addClass("get_key").unbind().click(R)
		}
		function N() {
			forget.unbind().removeClass("get_key").addClass("get_none")
		}
		/* 发送短信验证码 */
		function getCodeU(){
			countrycode_forget=$('#countrycode_forget option:selected').val();
			$.ajax({
				url: _this.code_url,
				data: {mobile:phone.val(),captcha:captcha.val(),type:'forget',countrycode:countrycode_forget},
				type: "GET",
				dataType: "json",
				cache: !1,
				success:function(data){
					if(data && data.errno ==0){
						_this.type='forget',popbox.fadeOut(300),O()
					}else{
						_this.loginWarring(data.errmsg),CapN(),getCaptcha();
						return !1;
					}
					
				}
			})			
		}
		/* 图片验证码 */
		function getCaptcha(){
			$.ajax({
				url: _this.captcha_url,
				data: {},
				type: "GET",
				dataType: "json",
				cache: !1,
				success:function(data){
					if(data && data.errno ==0){
                        captcha.val('');
						_this.js_forget_captcha_img.attr("src",data.data.captcha+"&v=" + parseInt(Math.random() * 1e8, 10));
					}else{
						_this.loginWarring(data.errmsg);
						return !1;
					}
					
				}
			})			
		}
		function R(){
			if(!_this.checkPass.test(pass.val())){
				_this.loginWarring("密码必须包含字母和数字，6-12位")
				return !1;
			}
			if( pass.val()!=repass.val() ){
				_this.loginWarring("两次密码不一致")
				return !1;
			}
			_this.doforget();
		}				
	},
	forget:function(){
		this.js_login_pop_cover.fadeIn(300),
		this.js_reg_pop.fadeOut(300),
		this.js_login_pop.fadeOut(300),
		this.js_forget_pop.fadeIn(300),
		this.type='forget',
		this.forgetVerify()		
	},
	doforget:function(){
		var _this=this,
				phone=this.js_forget_phone_input.val(),
				pass =this.js_forget_pass_input.val(),
				code =this.js_forget_code_input.val();
				countrycode_forget=$('#countrycode_forget option:selected').val();
		$.ajax({
			url: _this.forget_url,
			data: {mobile:phone,pass:pass,code:code,countrycode:countrycode_forget},
			type: "GET",
			dataType: "json",
			cache: !1,
			success:function(data){
				if(data && data.errno ==0){
					$(".js_reg_pop input").val("");
					$(".js_login_pop input").val("");
					$(".js_forget_pop input").val("");
					layer.msg("重置成功",{},function(){
						_this.login();
					});
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

