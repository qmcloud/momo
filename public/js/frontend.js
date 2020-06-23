;(function () {
    //全局ajax处理
    $.ajaxSetup({
        complete: function (jqXHR) {},
        data: {
        },
        error: function (jqXHR, textStatus, errorThrown) {
            //请求失败处理
        }
    });

    if ($.browser.msie) {
        //ie 都不缓存
        $.ajaxSetup({
            cache: false
        });
    }

    //不支持placeholder浏览器下对placeholder进行处理
    if (document.createElement('input').placeholder !== '') {
        $('[placeholder]').focus(function () {
            var input = $(this);
            if (input.val() == input.attr('placeholder')) {
                input.val('');
                input.removeClass('placeholder');
            }
        }).blur(function () {
            var input = $(this);
            if (input.val() == '' || input.val() == input.attr('placeholder')) {
                input.addClass('placeholder');
                input.val(input.attr('placeholder'));
            }
        }).blur().parents('form').submit(function () {
            $(this).find('[placeholder]').each(function () {
                var input = $(this);
                if (input.val() == input.attr('placeholder')) {
                    input.val('');
                }
            });
        });
    }


    //所有加了dialog类名的a链接，自动弹出它的href
    if ($('a.js-dialog').length) {
        Wind.use('artDialog', 'iframeTools', function () {
            $('.js-dialog').on('click', function (e) {
                e.preventDefault();
                var $_this = this,
                    _this = $($_this);
                art.dialog.open($(this).prop('href'), {
                    close: function () {
                        $_this.focus(); //关闭时让触发弹窗的元素获取焦点
                        return true;
                    },
                    title: _this.prop('title')
                });
            }).attr('role', 'button');

        });
    }

    //所有的ajax form提交,由于大多业务逻辑都是一样的，故统一处理
    var ajaxForm_list = $('form.js-ajax-form');
    if (ajaxForm_list.length) {
        Wind.use('ajaxForm', 'noty', function () {
            if ($.browser.msie) {
                //ie8及以下，表单中只有一个可见的input:text时，会整个页面会跳转提交
                ajaxForm_list.on('submit', function (e) {
                    //表单中只有一个可见的input:text时，enter提交无效
                    e.preventDefault();
                });
            }

            $('button.js-ajax-submit').on('click', function (e) {
                e.preventDefault();
                /*var btn = $(this).find('button.js-ajax-submit'),
					form = $(this);*/
                var btn = $(this),
                    form = btn.parents('form.js-ajax-form');

                if(btn.data("loading")){
            		return;
            	}
                //批量操作 判断选项
                if (btn.data('subcheck')) {
                    btn.parent().find('span').remove();
                    if (form.find('input.js-check:checked').length) {
                        var msg = btn.data('msg');
                        if (msg) {
                        	 noty({
                				 	text: msg,
                					type:'confirm',
                					layout:"center",
                					timeout: false,
                					modal: true,
                				   	buttons: [
                				   	{
                				   		addClass: 'btn btn-primary',
                				   		text: '确定',
                				   		onClick: function($noty){
                				   			$noty.close();
                				   			btn.data('subcheck', false);
                				   			btn.click();
                				   		}
                				   	},
                				   	{
                				   		addClass: 'btn btn-danger',
                				   		text: '取消',
                				   		onClick: function($noty) {
                				   			$noty.close();
                				   		}
                				   	}
                					]
                             });
                        } else {
                            btn.data('subcheck', false);
                            btn.click();
                        }

                    } else {
                    	noty({text:"请至少选择一项",
                    		type:'error',
                    		layout:'center'
                    	});
                    }
                    return false;
                }

                //ie处理placeholder提交问题
                if ($.browser.msie) {
                    form.find('[placeholder]').each(function () {
                        var input = $(this);
                        if (input.val() == input.attr('placeholder')) {
                            input.val('');
                        }
                    });
                }

                form.ajaxSubmit({
                    url: btn.data('action') ? btn.data('action') : form.attr('action'), //按钮上是否自定义提交地址(多按钮情况)
                    dataType: 'json',
                    beforeSubmit: function (arr, $form, options) {
                    	btn.data("loading",true);
                        var text = btn.text();
                        //按钮文案、状态修改
                        btn.text(text + '中...').prop('disabled', true).addClass('disabled');
                    },
                    success: function (data, statusText, xhr, $form) {
                        var text = btn.text();
                        //按钮文案、状态修改
                        btn.removeClass('disabled').text(text.replace('中...', '')).parent().find('span').remove();
                        if (data.state === 'success') {
                        	noty({text: data.info,
                        		type:'success',
                        		layout:'center'
                        	});
                        } else if (data.state === 'fail') {
                        	var $verify_img=form.find(".verify_img");
                        	if($verify_img.length){
                        		$verify_img.attr("src",$verify_img.attr("src")+"&refresh="+Math.random()); 
                        	}
                        	
                        	var $verify_input=form.find("[name='verify']");
                        	$verify_input.val("");
                        	
                        	noty({text: data.info,
                        		type:'error',
                        		layout:'center'
                        	});
                            btn.removeProp('disabled').removeClass('disabled');
                        }
                        
                        if (data.referer) {
                            //返回带跳转地址
                        	var wait=btn.data("wait");
                        	if(!wait){
                        		wait=1500;
                        	}
                            if(window.parent.art){
                                //iframe弹出页
                            	if(wait){
                            		setTimeout(function(){
                            			window.parent.location.href = data.referer;
                            		},wait);
                        		}else{
                        			window.parent.location.href = data.referer;
                        		}
                                
                            }else{
                            	if(wait){
                            		setTimeout(function(){
                            			window.location.href = data.referer;
                            		},wait);
                        		}else{
                        			window.location.href = data.referer;
                        		}
                            }
                        } else {
                        	if (data.state === 'success') {
                        		var wait=btn.data("wait");
                        		if(window.parent.art){
                                    if(wait){
                                		setTimeout(function(){
                                			reloadPage(window.parent);
                                		},wait);
                            		}else{
                            			reloadPage(window.parent);
                            		}
                                }else{
                                    //刷新当前页
                                	if(wait){
                                		setTimeout(function(){
                                			reloadPage(window);
                                		},wait);
                            		}else{
                            			reloadPage(window);
                            		}
                                }
                        	}
                        }
                        
                    },
                    complete: function(){
                    	btn.data("loading",false);
                    }
                });
            });

        });
    }

    //dialog弹窗内的关闭方法
    $('#js-dialog-close').on('click', function (e) {
        e.preventDefault();
        try{
            art.dialog.close();
        }catch(err){
            Wind.use('artDialog','iframeTools',function(){
                art.dialog.close();
            });
        };
    });

    //所有的删除操作，删除数据后刷新页面
    if ($('a.js-ajax-delete').length) {
        Wind.use('noty', function () {
            $('.js-ajax-delete').on('click', function (e) {
                e.preventDefault();
                var $_this = this,
                    $this = $($_this),
                    href = $this.prop('href'),
                    msg = $this.data('msg');
                
                noty({
   				 	text: msg,
   					type:'confirm',
   					layout:"center",
   					timeout: false,
   					modal: true,
   				   	buttons: [
   				   	{
   				   		addClass: 'btn btn-primary',
   				   		text: '确定',
   				   		onClick: function($noty){
   				   			$noty.close();
	   				   		$.getJSON(href).done(function (data) {
	                            if (data.state === 'success') {
	                                if (data.referer) {
	                                    location.href = data.referer;
	                                } else {
	                                    reloadPage(window);
	                                }
	                            } else if (data.state === 'fail') {
	                                //art.dialog.alert(data.info);
	                            	//alert(data.info);//暂时处理方案
									art.dialog({   
										content: data.info,
										icon: 'warning',
										ok: function () {   
											this.title(data.info);   
											return true;   
										}
									}); 
	                            }
	                        });
   				   		}
   				   	},
   				   	{
   				   		addClass: 'btn btn-danger',
   				   		text: '取消',
   				   		onClick: function($noty) {
   				   			$noty.close();
   				   		}
   				   	}
   					]
                });
                
            });

        });
    }
    
    
    if ($('a.js-ajax-dialog-btn').length) {
        Wind.use('noty', function () {
            $('.js-ajax-dialog-btn').on('click', function (e) {
                e.preventDefault();
                var $_this = this,
                    $this = $($_this),
                    href = $this.prop('href'),
                    msg = $this.data('msg');
                
                noty({
   				 	text: msg,
   					type:'confirm',
   					layout:"center",
   					timeout: false,
   					modal: true,
   				   	buttons: [
   				   	{
   				   		addClass: 'btn btn-primary',
   				   		text: '确定',
   				   		onClick: function($noty){
   				   			$noty.close();
	   				   		$.getJSON(href).done(function (data) {
	                            if (data.state === 'success') {
	                                if (data.referer) {
	                                    location.href = data.referer;
	                                } else {
	                                    reloadPage(window);
	                                }
	                            } else if (data.state === 'fail') {
	                                art.dialog.alert(data.info);
	                            }
	                        });
   				   		}
   				   	},
   				   	{
   				   		addClass: 'btn btn-danger',
   				   		text: '取消',
   				   		onClick: function($noty) {
   				   			$noty.close();
   				   		}
   				   	}
   					]
                });
                
            });

        });
    }

    //所有的请求刷新操作
    var ajax_refresh = $('a.J_ajax_refresh'),
        refresh_lock = false;
    if (ajax_refresh.length) {
        ajax_refresh.on('click', function (e) {
            e.preventDefault();
            if (refresh_lock) {
                return false;
            }
            refresh_lock = true;

            $.post(this.href, function (data) {
                refresh_lock = false;

                if (data.state === 'success') {
                    if (data.referer) {
                        location.href = data.referer;
                    } else {
                        reloadPage(window);
                    }
                } else if (data.state === 'fail') {
                    Wind.art.dialog.alert(data.info);
                }
            }, 'json');
        });
    }

    //日期选择器
    var dateInput = $("input.js-date")
    if (dateInput.length) {
        Wind.use('datePicker', function () {
            dateInput.datePicker();
        });
    }

    //日期+时间选择器
    var dateTimeInput = $("input.js-datetime");
    if (dateTimeInput.length) {
        Wind.use('datePicker', function () {
            dateTimeInput.datePicker({
                time: true
            });
        });
    }

    //赞，拍等，有数量操作的按钮
    var $js_count_btn=$('a.js-count-btn');
    if ($js_count_btn.length) {
        Wind.use('noty', function () {
        	$js_count_btn.on('click', function (e) {
                e.preventDefault();
                var $this = $(this),
                    href = $this.prop('href');
                
                $.post(href,{},function(data){
                	
                	if (data.state === 'success') {
                		
                		var $count=$this.find(".count");
                		var count=parseInt($count.text());
                		$count.text(count+1);
                		if(data.info){
                			noty({text: data.info,
                        		type:'success',
                        		layout:'center'
                        	});
                		}
                		
                		
                    } else if (data.state === 'fail') {
                    	noty({text: data.info,
                    		type:'error',
                    		layout:'center'
                    	});
                    }
                	
                	
                },"json");
                
            });

        });
    }
    
    //
    var $J_action_btn=$('a.J_action_btn');
    if ($J_action_btn.length) {
        Wind.use('noty', function () {
        	$J_action_btn.on('click', function (e) {
                e.preventDefault();
                var $this = $(this),
                    href = $this.prop('href');
                
                $.post(href,{},function(data){
                	
                	if (data.state === 'success') {
                		
                		if(data.info){
                			noty({text: data.info,
                        		type:'success',
                        		layout:'center'
                        	});
                		}
                		
                		
                    } else if (data.state === 'fail') {
                    	noty({text: data.info,
                    		type:'error',
                    		layout:'center'
                    	});
                    }
                	
                	
                },"json");
                
            });

        });
    }
    
    var $js_favorite_btn=$('a.js-favorite-btn');
    if ($js_favorite_btn.length) {
        Wind.use('noty', function () {
        	$js_favorite_btn.on('click', function (e) {
                e.preventDefault();
                var $this = $(this),
                    href = $this.prop('href'),
                    url = $this.data("url"),
                    key = $this.data("key"),
                    title = $this.data("title"),
                    description = $this.data("description");
                
                
                $.post(href,{url:url,key:key,title:title,description:description},function(data){
                	
                	if (data.state === 'success') {
                		
                		if(data.info){
                			noty({text: data.info,
                        		type:'success',
                        		layout:'center'
                        	});
                		}
                		
                		
                    } else if (data.state === 'fail') {
                    	noty({text: data.info,
                    		type:'error',
                    		layout:'center'
                    	});
                    }
                	
                	
                },"json");
                
            });

        });
    }
    
    var $comment_form=$(".comment-area .comment-form");
    if($comment_form.length){
    	Wind.use("ajaxForm",function(){
    		
    		$(".js-ajax-submit",$comment_form).on("click",function(e){
        		var btn=$(this),
        			form=btn.parents(".comment-form");
        		e.preventDefault();
        		
        		var url= btn.data('action') ? btn.data('action') : form.attr('action');
        		var data=form.serialize()+"&url="+encodeURIComponent(location.href);
        		$.ajax({
        			url:url,
        			dataType: 'json',
        			type: "POST",
        			beforeSend:function(){
        				 var text = btn.text();

                         //按钮文案、状态修改
                         btn.text(text + '中...').prop('disabled', true).addClass('disabled');
        			},
        			data:data,
        			success: function (data, textStatus, jqXHR) {
                        var text = btn.text();

                        //按钮文案、状态修改
                        btn.removeClass('disabled').text(text.replace('中...', '')).parent().find('span').remove();
                        btn.removeProp('disabled').removeClass('disabled');
                        if (data.state === 'success') {
                            $('<span class="tips_success">' + data.info + '</span>').appendTo(btn.parent()).fadeIn('slow').delay(1000).fadeOut(function () {
                            });
                        } else if (data.state === 'fail') {
                            $('<span class="tips_error">' + data.info + '</span>').appendTo(btn.parent()).fadeIn('fast');
                            btn.removeProp('disabled').removeClass('disabled');
                        }
                        
                        if (data.state === 'success') {
                    		var $comments=form.siblings(".comments");
                    		var comment_tpl=btn.parents(".comment-area").find(".comment-tpl").html();
                    		var $comment_tpl=$(comment_tpl);
                    		$comment_tpl.attr("data-id",data.data.id);
                    		var $comment_postbox=form.find(".comment-postbox");
                    		var comment_content=$comment_postbox.val();
                    		$comment_tpl.find(".comment-content .content").html(comment_content);
                    		$comments.append($comment_tpl);
                    		$comment_postbox.val("");
                    	}
                        
                    }
        			
        			
        		});
        		
        		return false;
        		
        	});
    	});
    	
    }


})();

function comment_reply(obj){
	
	$(".comments .comment-reply-submit").hide();
	var $this=$(obj);
	var $comment_body=$this.parents(".comments > .comment> .comment-body");
	
	var commentid=$this.parents(".comment").data("id");
	
	var $comment_reply_submit=$comment_body.find(".comment-reply-submit");
	
	if($comment_reply_submit.length){
		$comment_reply_submit.show();
	}else{
		var comment_reply_box_tpl=$comment_body.parents(".comment-area").find(".comment-reply-box-tpl").html();
		$comment_reply_submit=$(comment_reply_box_tpl);
		$comment_body.append($comment_reply_submit);
	}
	$comment_reply_submit.find(".textbox").focus();
	$comment_reply_submit.data("replyid",commentid);
}

function comment_submit(obj){
	
	Wind.use('noty', function () {
		
		var $this=$(obj);
		
		var $comment_reply_submit=$this.parents(".comment-reply-submit");
		
		var $reply_textbox=$comment_reply_submit.find(".textbox");
		var reply_content=$reply_textbox.val();
		
		if(reply_content==''){
			$reply_textbox.focus();
			return;
		}
		
		var $comment_body=$this.parents(".comments > .comment> .comment-body");
		
		var comment_tpl=$comment_body.parents(".comment-area").find(".comment-tpl").html();
		
		var $comment_tpl=$(comment_tpl);
		
		var replyid=$comment_reply_submit.data('replyid');
		
		var $comment=$(".comments [data-id='"+replyid+"']");
		
		var username=$comment.data("username");
		
		var comment_content="回复 "+username+":"+reply_content;
		$comment_tpl.find(".comment-content .content").html(comment_content);
		$comment_reply_submit.before($comment_tpl);
		
		var $comment_form=$this.parents(".comment-area").find(".comment-form");
		
		var comment_url=$comment_form.attr("action");
		
		var post_table=$comment_form.find("[name='post_table']").val();
		var post_title=$comment_form.find("[name='post_title']").val();
		var post_id=$comment_form.find("[name='post_id']").val();
		
		var uid=$comment.data("uid");
		
		$.post(comment_url,
		{
			post_title:post_title,
			post_table:post_table,
			post_id:post_id,
			to_uid:uid,
			parentid:replyid,
			content:reply_content,
			url:encodeURIComponent(location.href)
		},function(data){
			if(data.status==0){
				noty({text: data.info,
            		type:'error',
            		layout:'center'
            	});
				$comment_tpl.remove();
			}
			
			if(data.status==1){
				$comment_tpl.attr("data-id",data.data.id);
				$reply_textbox.val('');
			}
			
		},'json');
		
		
		$comment_reply_submit.hide();
		
		
		
	});
	
}

//重新刷新页面，使用location.reload()有可能导致重新提交
function reloadPage(win) {
	if(win){
		
	}else{
		win=window;
	}
    var location = win.location;
    location.href = location.pathname + location.search;
}

//页面跳转
function redirect(url) {
    location.href = url;
}

//读取cookie
function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1, c.length);
        }
        if (c.indexOf(nameEQ) == 0) {
            return c.substring(nameEQ.length, c.length);
        }
    };

    return null;
}

//设置cookie
function setCookie(name, value, days) {
    var argc = setCookie.arguments.length;
    var argv = setCookie.arguments;
    var secure = (argc > 5) ? argv[5] : false;
    var expire = new Date();
    if(days==null || days==0) days=1;
    expire.setTime(expire.getTime() + 3600000*24*days);
    document.cookie = name + "=" + escape(value) + ("; path=/") + ((secure == true) ? "; secure" : "") + ";expires="+expire.toGMTString();
}

//浮出提示_居中
function resultTip(options) {

    var cls = (options.error ? 'warning' : 'success');
    var pop = $('<div style="left:50%;top:30%;" class="pop_showmsg_wrap"><span class="pop_showmsg"><span class="' + cls + '">' + options.msg + '</span></span></div>');

    pop.appendTo($('body')).fadeIn(function () {
        pop.css({
            marginLeft: -pop.innerWidth() / 2
        }); //水平居中
    }).delay(1500).fadeOut(function () {
        pop.remove();

        //回调
        if (options.callback) {
            options.callback();
        }
    });

}

//弹窗居中定位 非ie6 fixed定位
function popPos(wrap) {
    var ie6 = false,
        pos = 'fixed',
        top,
        win_height = $(window).height(),
        wrap_height = wrap.outerHeight();

    if ($.browser.msie && $.browser.version < 7) {
        ie6 = true;
        pos = 'absolute';
    }

    if (win_height < wrap_height) {
        top = 0;
    } else {
        top = ($(window).height() - wrap.outerHeight()) / 2;
    }

    wrap.css({
        position: pos,
        top: top + (ie6 ? $(document).scrollTop() : 0),
        left: ($(window).width() - wrap.innerWidth()) / 2
    }).show();
}

//新窗口打开
function openwinx(url,name,w,h) {
    if(!w) w=screen.width;
    if(!h) h=screen.height;
    //window.open(url,name,"top=100,left=400,width=" + w + ",height=" + h + ",toolbar=no,menubar=no,scrollbars=yes,resizable=yes,location=no,status=no");
    window.open(url,name);
}
//询问
function confirmurl(url, message) {
    Wind.use("artDialog", "iframeTools", function () {
        art.dialog.confirm(message, function () {
            location.href = url;
        }, function () {
            art.dialog.tips('你取消了操作');
        });
    });
}

function open_iframe_dialog(url,title,options){
	var params={
            title: title,
            lock:true,
            opacity:0,
            width:"95%"
        };
	params=options?$.extend(params,options):params;
	 Wind.use('artDialog','iframeTools', function () {
	            art.dialog.open(url, params);
	        });
}