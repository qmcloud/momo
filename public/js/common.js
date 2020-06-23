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

    // 所有加了dialog类名的a链接，自动弹出它的href
	if ($('a.js-dialog').length) {
		Wind.use('artDialog', 'iframeTools', function() {
			$('.js-dialog').on('click', function(e) {
				e.preventDefault();
				var $this = $(this);
				art.dialog.open($(this).prop('href'), {
					close : function() {
						$this.focus(); // 关闭时让触发弹窗的元素获取焦点
						return true;
					},
					title : $this.prop('title')
				});
			}).attr('role', 'button');

		});
	}

    // 所有的ajax form提交,由于大多业务逻辑都是一样的，故统一处理
    var ajaxForm_list = $('form.js-ajax-form');
    if (ajaxForm_list.length) {
        Wind.use('ajaxForm', 'artDialog', function () {
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
                            art.dialog({
                                id: 'warning',
                                icon: 'warning',
                                content: btn.data('msg'),
                                cancelVal: '关闭',
                                cancel: function () {
                                    //btn.data('subcheck', false);
                                    //btn.click();
                                },
                                ok: function () {
                                	 btn.data('subcheck', false);
                                	 btn.click();
                                }
                            });
                        } else {
                            btn.data('subcheck', false);
                            btn.click();
                        }

                    } else {
                        $('<span class="tips_error">请至少选择一项</span>').appendTo(btn.parent()).fadeIn('fast');
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
                            $('<span class="tips_success">' + data.info + '</span>').appendTo(btn.parent()).fadeIn('slow').delay(3000).fadeOut(function () {
                            });
                        } else if (data.state === 'fail') {
                        	var $verify_img=form.find(".verify_img");
                        	if($verify_img.length){
                        		$verify_img.attr("src",$verify_img.attr("src")+"&refresh="+Math.random()); 
                        	}
                        	
                        	var $verify_input=form.find("[name='verify']");
                        	$verify_input.val("");
                        	
                            $('<span class="tips_error">' + data.info + '</span>').appendTo(btn.parent()).fadeIn('fast');
                            btn.removeProp('disabled').removeClass('disabled');
                        }
                        setTimeout(function(){
							 if (data.referer) {
								//返回带跳转地址
								if(window.parent.art){
									//iframe弹出页
									window.parent.location.href = data.referer;
								}else{
									window.location.href = data.referer;
								}
							} else {
								if (data.state === 'success') {
									if(window.parent.art){
										reloadPage(window.parent);
									}else{
										//刷新当前页
										reloadPage(window);
									}
								}
							}
						},3000)
                       
                        
                    },
                    complete: function(){
                    	btn.data("loading",false);
                    }
                });
            });

        });
    }

    //dialog弹窗内的关闭方法
    $('#js-dialog_close').on('click', function (e) {
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
        Wind.use('artDialog', function () {
            $('.js-ajax-delete').on('click', function (e) {
                e.preventDefault();
                var $_this = this,
                    $this = $($_this),
                    href = $this.prop('href'),
                    msg = $this.data('msg');
                art.dialog({
                    title: false,
                    icon: 'question',
                    content: '确定要删除吗？',
                    follow: $_this,
                    close: function () {
                        $_this.focus();; //关闭时让触发弹窗的元素获取焦点
                        return true;
                    },
                    okVal:"确定",
                    ok: function () {
                    	
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
                    },
                    cancelVal: '关闭',
                    cancel: true
                });
            });

        });
    }
    
    
    if ($('a.js-ajax-dialog-btn').length) {
        Wind.use('artDialog', function () {
            $('.js-ajax-dialog-btn').on('click', function (e) {
                e.preventDefault();
                var $_this = this,
                    $this = $($_this),
                    href = $this.prop('href'),
                    msg = $this.data('msg');
                if(!msg){
                	msg="您确定要进行此操作吗？";
                }
                art.dialog({
                    title: false,
                    icon: 'question',
                    content: msg,
                    follow: $_this,
                    close: function () {
                        $_this.focus();; //关闭时让触发弹窗的元素获取焦点
                        return true;
                    },
                    ok: function () {
												if(href.indexOf("backup")>=0){
													 $.showLoading("处理中...");
												}
                        $.getJSON(href).done(function (data) {
                            if (data.state === 'success') {
                                if (data.referer) {
																		art.dialog({   
																		content: data.info,
																		icon: 'succeed',
																		ok: function () {   
																				location.href = data.referer;
																				return true;   
																				}
																		}); 
                                   
                                } else {	
																	art.dialog({   
																		content: data.info,
																		icon: 'succeed',
																		ok: function () {   
																				reloadPage(window); 
																				return true;   
																				}
																		}); 
                                }
                            } else if (data.state === 'fail') {
                                //art.dialog.alert(data.info);
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
                    },
                    cancelVal: '关闭',
                    cancel: true
                });
            });

        });
    }

    /*复选框全选(支持多个，纵横双控全选)。
     *实例：版块编辑-权限相关（双控），验证机制-验证策略（单控）
     *说明：
     *	"js-check"的"data-xid"对应其左侧"js-check-all"的"data-checklist"；
     *	"js-check"的"data-yid"对应其上方"js-check-all"的"data-checklist"；
     *	全选框的"data-direction"代表其控制的全选方向(x或y)；
     *	"js-check-wrap"同一块全选操作区域的父标签class，多个调用考虑
     */

    if ($('.js-check-wrap').length) {
        var total_check_all = $('input.js-check-all');

        //遍历所有全选框
        $.each(total_check_all, function () {
            var check_all = $(this),
                check_items;

            //分组各纵横项
            var check_all_direction = check_all.data('direction');
            check_items = $('input.js-check[data-' + check_all_direction + 'id="' + check_all.data('checklist') + '"]');
            
            //点击全选框
            check_all.change(function (e) {
                var check_wrap = check_all.parents('.js-check-wrap'); //当前操作区域所有复选框的父标签（重用考虑）

                if ($(this).attr('checked')) {
                    //全选状态
                    check_items.attr('checked', true);

                    //所有项都被选中
                    if (check_wrap.find('input.js-check').length === check_wrap.find('input.js-check:checked').length) {
                        check_wrap.find(total_check_all).attr('checked', true);
                    }

                } else {
                    //非全选状态
                    check_items.removeAttr('checked');
                    
                    check_wrap.find(total_check_all).removeAttr('checked');

                    //另一方向的全选框取消全选状态
                    var direction_invert = check_all_direction === 'x' ? 'y' : 'x';
                    check_wrap.find($('input.js-check-all[data-direction="' + direction_invert + '"]')).removeAttr('checked');
                }

            });

            //点击非全选时判断是否全部勾选
            check_items.change(function () {

                if ($(this).attr('checked')) {

                    if (check_items.filter(':checked').length === check_items.length) {
                        //已选择和未选择的复选框数相等
                        check_all.attr('checked', true);
                    }

                } else {
                    check_all.removeAttr('checked');
                }

            });


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

    //tab
    var tabs_nav = $('ul.js-tabs-nav');
    if (tabs_nav.length) {
        Wind.use('tabs', function () {
            tabs_nav.tabs('.js-tabs-content > div');
        });
    }

})();

//重新刷新页面，使用location.reload()有可能导致重新提交
function reloadPage(win) {
    var location = win.location;
    location.href = location.pathname + location.search;
}

/**
 * 页面跳转
 * @param url
 */
function redirect(url) {
    location.href = url;
}

/**
 * 读取cookie
 * @param name
 * @returns
 */
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
	}
	

	return null;
}

// 设置cookie
function setCookie(name, value, days) {
    var argc = setCookie.arguments.length;
    var argv = setCookie.arguments;
    var secure = (argc > 5) ? argv[5] : false;
    var expire = new Date();
    if(days==null || days==0) days=1;
    expire.setTime(expire.getTime() + 3600000*24*days);
    document.cookie = name + "=" + escape(value) + ("; path=/") + ((secure == true) ? "; secure" : "") + ";expires="+expire.toGMTString();
}

/**
 * 打开iframe式的窗口对话框
 * @param url
 * @param title
 * @param options
 */
function open_iframe_dialog(url, title, options) {
	var params = {
		title : title,
		lock : true,
		opacity : 0,
		width : "95%"
	};
	params = options ? $.extend(params, options) : params;
	Wind.use('artDialog', 'iframeTools', function() {
		art.dialog.open(url, params);
	});
}

/**
 * 打开地图对话框
 * 
 * @param url
 * @param title
 * @param options
 * @param callback
 */
function open_map_dialog(url, title, options, callback) {

	var params = {
		title : title,
		lock : true,
		opacity : 0,
		width : "95%",
		height : 400,
		ok : function() {
			if (callback) {
				var d = this.iframe.contentWindow;
				var lng = $("#lng_input", d.document).val();
				var lat = $("#lat_input", d.document).val();
				var address = {};
				address.address = $("#address_input", d.document).val();
				address.province = $("#province_input", d.document).val();
				address.city = $("#city_input", d.document).val();
				address.district = $("#district_input", d.document).val();
				callback.apply(this, [ lng, lat, address ]);
			}
		}
	};
	params = options ? $.extend(params, options) : params;
	Wind.use('artDialog', 'iframeTools', function() {
		art.dialog.open(url, params);
	});
	
}