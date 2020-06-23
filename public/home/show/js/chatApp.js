var Interface = {
    startEndLive: function(type) {
        if (type == 1) {
            var msg = '{"retmsg":"ok","retcode":"000000","msg":[{"uid":"0","timestamp":"","sex":"0","level":"0","touid":"0","msgtype":"1","tougood":"","_method_":"StartEndLive","touname":"","action":"18","ugood":"","ct":"直播关闭","city":"","vip_type":"","liangname":""}]}';
            window.location.href = "/";
        } else {
            var msg = '{"retmsg":"ok","retcode":"000000","msg":[{"uid":"0","timestamp":"","sex":"0","level":"0","touid":"0","msgtype":"1","tougood":"","_method_":"StartEndLive","touname":"","action":"15","ugood":"","ct":"直播开始","city":"","vip_type":"0","liangname":"0"}]}';
        }
        Socket.emitData('broadcast', msg);
    },
    stopRoom: function() {
        getSwfObject("container").stopRoom();

    }
};

//设置标题(title)、房间类型(type)、及其房间类型的收费金额(stand)、已经请求僵尸粉 
var isobs = 0;
var liveType = {
        getzombie_url: '/api/public/?service=Live.getZombie',
        checkLive_url: '/index.php?g=home&m=Show&a=checkLive',
        selectPlay_url: '/index.php?g=home&m=Show&a=selectPlay',
        //flash调用js弹出设置类型窗口
        Choice: function(obs) {
            isobs = obs;
            liveType.selectPlay();
            /* var choice='{"title":"未设置标题","type":"","stand":"","obs":"'+obs+'"}';
		document.getElementById('webplayer')._myclock(choice); */
        },
        selectPlay: function() {
            $('#giveBox').hide();
            $.ajax({
                type: "post",
                url: "/index.php?g=home&m=Show&a=selectplay",
                data: {},
                success: function(data) {
                    document.getElementById('ds-dialog-bg').style.display = 'block';
                    $("#selectPlay").css({ "left": getMiddlePos('selectPlay').pl + "px", "z-index": 210, }).show();
                    $("#selectPlay").html(data);
                }
            });

        },
        closePorp: function() {
            $('#selectPlay').hide();
            $('#selectPlay').html("");
            document.getElementById('ds-dialog-bg').style.display = 'none';
        },
        other: function() {
            var id = $(".mount-method .mount_btn_on").attr("id");
            var type = "";
            var stand = "";
            var title = $("#title").val();
            if (id == "btn0") {
                type = "0";
            } else if (id == "btn1") {
                type = "1";
                stand = $("#gift_number").val();
                if (stand == "") {
                    liveType.closePorp();
                    layer.msg("密码不能为空");
                    return !1;
                }
            } else if (id == "btn2") {
                type = "2";
                stand = $("#gift_number").val();
                if (stand == "") {
                    liveType.closePorp();
                    layer.msg("金额不能为空");
                    return !1;
                }
            } else if (id == "btn3") {
                type = "3";
                stand = $("#gift_select").val();
                if (stand == "") {
                    liveType.closePorp();
                    layer.msg("金额不能为空");
                    return !1;
                }
            }
            liveType.closePorp();
            var choice = '{"title":"' + title + '","type":"' + type + '","obs":"' + isobs + '","stand":"' + stand + '"}';
            document.getElementById('webplayer')._myclock(choice);
        },
        getzombie: function(uid, stream) {
            if (_DATA.user != null) {
                $.ajax({
                    url: this.getzombie_url,
                    data: { uid: uid, stream: stream },
                    dataType: 'json',
                    success: function(data) {}
                });
            }
        },
        checkLive: function() {
            if (_DATA.live && _DATA.live.islive == 1) {
                var liveuid = _DATA.anchor.id;
                var stream = _DATA.live.stream;
                $.ajax({
                    url: this.checkLive_url,
                    data: { liveuid: liveuid, stream: stream },
                    dataType: 'json',
                    success: function(data) {
                        if (data.land == 0) {
                            if (data.type == 1) {
                                liveType.passRoom(data.type_msg, data.type_value);
                            } else if (data.type == 2) {
                                liveType.charge(data.type_msg, liveuid, stream, data.type_value, 1);
                            } else if (data.type == 3) {
                                liveType.timecharge(data.type_msg, liveuid, stream, data.type_value, 1);
                            } else if (data.type == 4) {
                                liveType.bothcharge(data.type_msg, liveuid, stream, data.type_value, 1);
                            } else {
                                liveType.type();
                            }
                        } else {
                            var t = setTimeout("window.location.href='/'", 20000);
                            layer.alert(data.type_msg, {
                                skin: 'layui-layer-molv' //样式类名
                                    ,
                                closeBtn: 0
                            }, function() {
                                window.location.href = '/';
                            });
                        }

                    },
                    error: function(request) {
                        layer.msg("信息处理错误");
                    },
                });
            } else //当前主播未开播
            {
                Video.endRecommend();

            }
            Socket.nodejsInit();
        },
        passRoom: function(type_msg, type_value) {
            var t = setTimeout("window.location.href='/'", 20000);
            layer.closeAll();
            layer.prompt({
                title: type_msg,
                btn2: function() { window.location.href = '/' },
                formType: 1,
            }, function(pass, index) {
                if (hex_md5(pass) == type_value) {
                    clearTimeout(t);
                    liveType.type();
                    layer.close(index);
                } else {

                    type_msg = "密码错误，请重新输入";
                    setTimeout(liveType.passRoom(type_msg, type_value), 2000);
                }
            });
        },
        charge: function(type_msg, liveuid, stream, type_value, isload) {
            var t = setTimeout("window.location.href='/'", 20000);
            layer.closeAll();
            layer.confirm(type_msg, {
                btn: ['开始付费观看', '拒绝'] //按钮
            }, function() {
                clearTimeout(t);
                liveType.roomCharge(liveuid, stream, 2, isload);
            }, function() {
                window.location.href = "./";
            });
        },
        timecharge: function(type_msg, liveuid, stream, type_value, isload) {
            var t = setTimeout("window.location.href='/'", 20000);
            layer.closeAll();
            layer.confirm(type_msg, {
                btn: ['开始付费观看', '拒绝'] //按钮
            }, function() {
                clearTimeout(t);
                liveType.roomCharge(liveuid, stream, 3, isload);
            }, function() {
                window.location.href = "./";
            });


        },
        bothcharge: function(type_msg, liveuid, stream, type_value, isload) {
            var t = setTimeout("window.location.href='/'", 20000);
            layer.closeAll();
            layer.confirm(type_msg, {
                btn: ['门票支付(' + type_value.fee_2 + '钻)', '计时付费(1分钟' + type_value.fee_3 + '钻)', '拒绝'] //按钮
            }, function() {
                clearTimeout(t);
                liveType.roomCharge(liveuid, stream, 2, isload);
            }, function() {
                clearTimeout(t);
                liveType.roomCharge(liveuid, stream, 3, isload);
            }, function() {
                window.location.href = "./";
            });


        },
        roomCharge: function(liveuid, stream, type, isload) {
            $.getJSON("/index.php?g=home&m=Spend&a=roomCharge", { liveuid: liveuid, stream: stream, type: type },
                function(data) {
                    if (data.code != 0) {
                        layer.msg(data.msg);
                        setTimeout("window.location.href='/'", 5000);
                    } else {
                        layer.closeAll();
                        var type_val = (_DATA.live && _DATA.live.type_val) || 0;
                        if (type_val) {
                            var msg = '{"retcode":"000000","retmsg":"ok","msg":[{"_method_":"updateVotes","action":"1","msgtype":"26","votes":"' + type_val + '","uid":"' + _DATA.user.id + '","isfirst":"0","ct":"","vip_type":"0","liangname":"0","usertype":"' + _DATA.usertype + '","guard_type":"' + _DATA.user.guard_type + '"}]}';
                            Socket.emitData('broadcast', msg);
                        }

                        _DATA.user.coin = data.coin;
                        User.updateMoney();
                        if (isload) {
                            liveType.type();
                        }
                        if (type == 3) {
                            /* 计时 */
                            charge_interval = setTimeout(
                                function() {
                                    liveType.roomCharge(liveuid, stream, 3, 0);
                                }, 60000);
                        }
                    }
                });
        },
        changeTypeValue: function() {
            $('#giveBox').hide();
            $.ajax({
                type: "post",
                url: "./index.php?g=home&m=Show&a=selectplay2",
                data: "",
                success: function(data) {
                    document.getElementById('ds-dialog-bg').style.display = 'block';
                    $("#selectPlay").css({ "left": getMiddlePos('selectPlay').pl + "px", "z-index": 210, }).show();
                    $("#selectPlay").html(data);
                }
            });

        },
        submitTypeValue: function() {
            var id = $(".mount-method .mount_btn_on").attr("id");
            var type = "";
            var stand = "";
            if (id == "btn0") {
                type = "0";
            } else if (id == "btn1") {
                type = "1";
                stand = $("#gift_number").val();
                if (stand == "") {
                    liveType.closePorp();
                    layer.msg("密码不能为空");
                    return !1;
                }
            } else if (id == "btn2") {
                type = "2";
                stand = $("#gift_number").val();
                if (stand == "") {
                    liveType.closePorp();
                    layer.msg("金额不能为空");
                    return !1;
                }
            } else if (id == "btn3") {
                type = "3";
                stand = $("#gift_select").val();
                if (stand == "") {
                    liveType.closePorp();
                    layer.msg("金额不能为空");
                    return !1;
                }
            }
            liveType.closePorp();
            /* 更新直播信息 */
            $.ajax({
                url: '/index.php?m=show&a=changeTypeValue',
                data: { type_value: stand, type: type },
                dataType: 'json',
                success: function(data) {
                    if (data.code == 0) {
                        var info = data.info;
                        var msg = '{"msg":[{"_method_":"changeLive","action":1,"msgtype":"27","type":"' + info.type + '","type_val":"' + info.type_value + '"}],"retcode":"000000","retmsg":"OK"}';
                        Socket.emitData('broadcast', msg);
                        alert("切换成功");
                    } else {
                        alert(data.msg);
                    }
                }
            })
        },
        //根据这里选择时使用ck播放视频
        //根据这里选择时使用ck播放视频
        type: function() {







            var pull = _DATA.live.pull;
            if (pull == '') {
                return !1;
            }
            xgPlay('playerzmblbkjP', pull);


        }
    }
    //房间管理操作
var Controls = {
        cancel_url: '/index.php?g=home&m=Spend&a=cancel',
        gag_url: '/index.php?g=home&m=Spend&a=gag',
        tiren_url: '/index.php?g=home&m=Spend&a=tiren',
        black_url: '/index.php?g=home&m=Spend&a=black',
        stopRoom_url: '/api/public/?service=Live.superStopRoom',
        report_url: '/index.php?g=home&m=Spend&a=report',
        admin_close: $("#admin_close"),
        admin_stopit: $("#admin_stopit"),
        admin_report: $("#admin_report"),
        init: function() {
            this.addEvent();
        },
        addEvent: function() {
            var _this = this;
            _this.admin_close.on("click", function() {
                    var type = _this.admin_close.attr("data-type");
                    _this.stopRoom(type);
                }),
                _this.admin_stopit.on("click", function() {
                    var type = _this.admin_stopit.attr("data-type");
                    _this.stopRoom(type);
                }),
                _this.admin_report.on("click", function() {
                    layer.prompt({ title: '请填写举报内容', formType: 2 }, function(text, index) {
                        layer.close(index);
                        _this.report(text);
                    })

                })
        },
        cancel: function() //设置 取消 管理
            {
                var touid = $("#popup_info").attr("popup-data-uid");
                $.ajax({
                    dataType: 'json',
                    url: this.cancel_url,
                    data: { touid: touid, roomid: _DATA.anchor.id }, // 你的formid
                    error: function(request) {
                        layer.msg("数据请求失败！");
                    },
                    success: function(data) {
                        layer.msg(data.msg);
                        $("#popup_info").remove();
                        if (data.isadmin == 1) {
                            ct = data.user_nicename + "被设为管理员";
                        } else {
                            ct = data.user_nicename + "被删除管理员";
                        }
                        var msg = '{"msg":[{"_method_":"SystemNot","action":"13","ct":"' + ct + '","msgtype":"4","uname":"' + _DATA.user.user_nicename + '","toname":"' + data.user_nicename + '","touid":' + touid + ',"uid":' + _DATA.user.id + ',"level":' + _DATA.user.level + ',"vip_type":"0","liangname":"0","usertype":"' + _DATA.usertype + '","guard_type":"' + _DATA.user.guard_type + '"}],"retcode":"000000","retmsg":"ok"}';
                        Socket.emitData('broadcast', msg);
                    }
                });

            },
        gag: function() //禁言
            {
                var touid = $("#popup_info").attr("popup-data-uid");
                var name = $("#popup_info").attr("popup-data-name");
                $("#popup_info").remove();
                $.ajax({
                    url: this.gag_url,
                    data: { touid: touid, roomid: _DATA.anchor.id },
                    dataType: 'json',
                    success: function(data) {
                        if (data.code == 0) {
                            layer.msg(data.msg);
                            //uid操作人ID uname操作人昵称 touid 被禁言人的ID toname被禁言人昵称
                            var msg = '{"msg":[{"_method_":"ShutUpUser","action":"1","ct":"' + name + '被禁言了","msgtype":"4","uid":' + _DATA.user.id + ',"uname":"' + _DATA.user.user_nicename + '","touid":' + touid + ',"toname":"' + name + '","vip_type":"0","liangname":"0","usertype":"' + _DATA.usertype + '","guard_type":"' + _DATA.user.guard_type + '"}],"retcode":"000000","retmsg":"ok"}';
                            Socket.emitData('broadcast', msg);
                        } else {
                            layer.msg(data.msg);
                        }
                    }
                });
            },
        tiren: function() //踢人
            {
                var touid = $("#popup_info").attr("popup-data-uid");
                var name = $("#popup_info").attr("popup-data-name");
                $("#popup_info").remove();
                $.ajax({
                    url: this.tiren_url,
                    data: { touid: touid, roomid: _DATA.anchor.id },
                    dataType: 'json',
                    success: function(data) {
                        if (data.code == 0) {
                            layer.msg("操作成功");
                            var msg = '{"msg":[{"_method_":"KickUser","action":"2","ct":"' + name + '被踢出房间","msgtype":"4","uid":' + _DATA.user.id + ',"uname":"' + _DATA.user.user_nicename + '","touid":' + touid + ',"touname":"' + name + '","vip_type":"0","liangname":"0","usertype":"' + _DATA.usertype + '","guard_type":"' + _DATA.user.guard_type + '"}],"retcode":"000000","retmsg":"ok"} ';
                            Socket.emitData('broadcast', msg);
                        } else {
                            layer.msg(data.msg);
                        }
                    }
                });
            },
        stopRoom: function(type) //超管关闭直播0 禁止直播1
            {
                $.ajax({
                    url: this.stopRoom_url,
                    data: { uid: _DATA.user.id, token: _DATA.user.token, liveuid: _DATA.anchor.id, type: type },
                    dataType: 'json',
                    success: function(data) {
                        if (data.ret == 200 && data.data.code == 0) {
                            var msg = '{"msg":[{"_method_":"stopLive","action":"10","ct":"该直播间涉嫌违规，已被停播","msgtype":"4","uid":' + _DATA.user.id + ',"uname":"' + _DATA.user.user_nicename + '","touid":' + _DATA.anchor.id + ',"touname":"' + _DATA.anchor.id.user_nicename + '","vip_type":"0","liangname":"0"}],"retcode":"000000","retmsg":"ok"} ';
                            Socket.emitData('broadcast', msg);
                            layer.msg(data['data']['info']['0']['msg']);
                        } else {
                            layer.msg(data['data']['info']['0']['msg']);
                        }
                    }
                });
            },
        report: function(content) //举报
            {
                $.ajax({
                    url: this.report_url,
                    data: { tlleuid: _DATA.user.id, token: _DATA.user.token, liveuid: _DATA.anchor.id, content: content },
                    dataType: 'json',
                    success: function(data) {
                        layer.msg(data.msg);
                    },
                    error: function(request) {
                        layer.msg("信息处理错误");
                    },
                });
            },
        black: function() {
            var touid = $("#popup_info").attr("popup-data-uid");
            if (touid != _DATA.user.id) {
                $.ajax({
                    url: this.black_url,
                    data: { touid: touid },
                    dataType: 'json',
                    success: function(data) {
                        if (data.code == 0) {
                            layer.msg(data.msg);
                        } else {
                            layer.msg(data.msg);
                        }
                    }
                });
            } else {
                layer.msg('无法将自己拉黑');
            }
            $("#popup_info").remove();
        },

        HxChat_User: function() {

            //判断环信窗口是否显示
            var status = $(".hxChatWindow").css("display");

            if (status == 'none') {
                $(".hxChatWindow").slideDown('slow');
            }

            var uid = $("#popup_info").attr("popup-data-uid");

            searchMember(uid);

        }



    }
    /* 
     *弹窗 提示效果
     *
     *调用 
     * alert 	:(new Dialog).alert(msg, fn, isclose);							msg 内容 fn 确定/取消执行的function  isclose 点击右上角X 是否执行fn
     * confirm :(new Dialog).confirm(msg, fn, isclose);						msg 内容 fn 确定/取消执行的function  isclose 点击右上角X 是否执行fn
     * tip 		:(new Dialog).tip(content, Ele_contrast, options);  content 提示内容 Ele_contrast 位置对象 options 参数
     *或
     *	var dialog=new Dialog; .alert(msg, fn, isclose);
     *
     */
var Dialog = function() {
    var Dialog = {
        _default: function() {
            var setting = {
                container: null,
                width: "auto",
                height: "auto",
                left: "auto",
                right: "auto",
                top: "auto",
                bottom: "auto",
                lock: !0,
                fixed: !1,
                drag: !0,
                skin: "dds-dialog",
                title: "Dialog Title",
                content: "Dialog Content",
                onOpen: function() {},
                onClosed: function() {},
                bgClosed: !1,
                zIndexCout: "dialogZindexCout",
                initZIndex: 2e3,
                fx: 0,
                timing: !1,
                delay: 5e3,
                tipClose: !1,
                isDestroy: !0,
                towards: "bottom"
            };
            return setting;
        },
        _dislog: function(options) {
            var _default = this._default();
            var setting = $.extend({},
                _default, options);
            for (var name in setting) this[name] = setting[name];
            this.timer = null
        },
        init: function() {
            this.dom(),
                this.setParams(),
                this.addEvent(),
                this.open(),
                this.setFx(),
                this.timing && this.setTiming()
        },
        getH: function() {
            if (!this.isFullScreen) return this.relPanel.height();
            var ch = document.documentElement.clientHeight || document.body.clientHeight,
                sh = document.documentElement.scrollHeight || document.body.scrollHeight;
            return ch < sh ? sh : ch
        },
        dom: function() {
            this.relPanel = this.container,
                this.isFullScreen = !1,
                this.container === null && (this.container = $(document.body), this.relPanel = $(window), this.isFullScreen = !0),
                this.lock && (this.pageBg = $('<div class="' + this.skin + '-bg"></div>'), this.pageBg.css({
                    height: this.getH(),
                    zIndex: window[this.zIndexCout] || this.initZIndex
                }), this.container.append(this.pageBg)),
                this.boxer = $("<div></div>"),
                this.boxer.addClass(this.skin + "-boxer"),
                this.container.append(this.boxer);
            var str = '<span class="' + this.skin + '-closed"></span>';
            this.boxer.append('<table cellspacing="0" cellpadding="0" border="0"><tbody><tr><td><div class="' + this.skin + '-tl"></div></td>' + '<td><div class="' + this.skin + '-tc">' + '<span class="' + this.skin + '-title">' + this.title + "</span>" + str + "</div></td>" + '<td><div class="' + this.skin + '-tr"></div></td></tr>' + '<tr><td class="' + this.skin + '-cl"></td>' + '<td><div class="' + this.skin + '-cc">' + this.content + "</div></td>" + '<td class="' + this.skin + '-cr"></td></tr>' + '<tr><td><div class="' + this.skin + '-bl"></div></td>' + '<td><div class="' + this.skin + '-bc"></div></td>' + '<td><div class="' + this.skin + '-br"></div></td>' + "</tr></tbody></table>").css({
                    width: this.width,
                    height: this.height,
                    zIndex: window[this.zIndexCout] || this.initZIndex
                }),
                typeof window[this.zIndexCout] != "undefined" ? window[this.zIndexCout] += 1 : window[this.zIndexCout] = this.initZIndex + 1
        },
        setTiming: function() {
            var _this = this;
            this.boxer.bind("mouseover",
                function() {
                    clearTimeout(_this.timer)
                }).bind("mouseout",
                function() {
                    _this.openTiming()
                })
        },
        openTiming: function() {
            var _this = this;
            this.timer = setTimeout(function() {
                    _this.fx === 3 ? _this.boxer.animate({
                            opacity: 0
                        },
                        "200", "swing",
                        function() {
                            _this.destroy()
                        }) : _this.destroy()
                },
                this.delay)
        },
        setFx: function() {
            if (this.fx === 3) {
                this.boxer.css({
                        opacity: 0
                    }),
                    this.boxer.animate({
                            opacity: 1
                        },
                        "400", "swing");
                return
            }
            var l = this.boxer.offset().left;
            if (this.fx === 2) this.boxer.animate({
                    left: l + 5
                },
                100, "swing").animate({
                    left: l - 5
                },
                100, "swing").animate({
                    left: l
                },
                100, "swing");
            else if (this.fx === 1) {
                var t = this.boxer.offset().top,
                    w = this.boxer.width(),
                    h = this.boxer.height();
                this.boxer.css({
                        opacity: 0,
                        width: 0,
                        height: 0,
                        left: l + w / 2,
                        top: t + h / 2
                    }),
                    this.boxer.animate({
                            opacity: 1,
                            top: t,
                            left: l,
                            width: w,
                            height: h
                        },
                        200, "swing")
            }
        },
        setBg: function() {
            this.pageBg.css({
                height: this.getH()
            })
        },
        setPos: function(t, l) {
            return this.right != "auto" ? (this.bottom != "auto" ? this.boxer.css({
                right: this.right,
                bottom: this.bottom
            }) : this.boxer.css({
                right: this.right,
                top: t
            }), !1) : (this.bottom != "auto" ? this.boxer.css({
                left: l,
                bottom: t
            }) : this.boxer.css({
                left: l,
                top: t
            }), !1)
        },
        setParams: function() {
            var tc = $("." + this.skin + "-tc", this.boxer),
                tl = $("." + this.skin + "-tl", this.boxer),
                tr = $("." + this.skin + "-tr", this.boxer),
                cl = $("." + this.skin + "-cl", this.boxer),
                cc = $("." + this.skin + "-cc", this.boxer),
                cr = $("." + this.skin + "-cr", this.boxer),
                bl = $("." + this.skin + "-bl", this.boxer),
                bc = $("." + this.skin + "-bc", this.boxer),
                br = $("." + this.skin + "-br", this.boxer),
                w = this.boxer.width(),
                h = this.boxer.height();
            tc.css({
                    width: w - tl.width() - tr.width()
                }),
                bc.css({
                    width: w - tl.width() - tr.width()
                }),
                this.height != "auto" && cc.css({
                    height: h - tl.height() - tr.height()
                }),
                this.width != "auto" && cc.css({
                    width: w - tl.width() - tr.width()
                });
            var t = (this.relPanel.height() - h) / 2,
                l = (this.relPanel.width() - w) / 2;
            this.relPanel.height() < h && (t = 15),
                this.fixed || (t += $(document).scrollTop(), l += $(document).scrollLeft()),
                t = this.top != "auto" ? this.top : t,
                l = this.left != "auto" ? this.left : l,
                this.setPos(t, l)
        },
        reWAH: function(width, height) {
            this.width = width,
                this.height = height,
                this.boxer.css({
                    width: width,
                    height: height
                });
            var w = this.boxer.width(),
                h = this.boxer.height(),
                cc = $("." + this.skin + "-cc", this.boxer),
                tl = $("." + this.skin + "-tl", this.boxer),
                tr = $("." + this.skin + "-tr", this.boxer);
            this.height != "auto" && cc.css({
                    height: h - tl.height() - tr.height()
                }),
                this.width != "auto" && cc.css({
                    width: w - tl.width() - tr.width()
                }),
                this.reSize()
        },
        reSize: function() {
            var t = (this.relPanel.height() - this.boxer.height()) / 2,
                l = (this.relPanel.width() - this.boxer.width()) / 2;
            this.relPanel.height() < this.boxer.height() && (t = 15),
                this.fixed || (t += $(document).scrollTop(), l += $(document).scrollLeft()),
                t = this.top != "auto" ? this.top : t,
                l = this.left != "auto" ? this.left : l,
                this.setPos(t, l),
                this.lock && this.setBg()
        },
        addEvent: function() {
            var _this = this;
            this.fixed && $(this.boxer).css({
                    position: "fixed"
                }),

                $("." + this.skin + "-closed", this.boxer).bind("click",
                    function() {
                        _this.destroy()
                    }).hover(function() {
                        $(this).addClass(_this.skin + "-closed-over")
                    },
                    function() {
                        $(this).removeClass(_this.skin + "-closed-over")
                    }),
                $(this.relPanel).bind("resize",
                    function() {
                        _this.reSize()
                    }),
                this.bgClosed && this.pageBg.bind("click",
                    function() {
                        _this.destroy()
                    })
        },
        open: function() {
            this.show(),
                this.onOpen(this)
        },
        show: function() {
            this.pageBg && this.pageBg.css("display", "block"),
                $(this.boxer).css("display", "block")
        },
        close: function() {
            this.pageBg && this.pageBg.css("display", "none"),
                $(this.boxer).css("display", "none"),
                this.onClosed()
        },
        destroy: function() {
            this.close();
            if (!this.isDestroy) return !1;
            this.pageBg && this.pageBg.remove(),
                $(this.boxer).remove()
        },
        alert: function(content, fun, isclose) {
            this._dislog();
            this.title = "提示";
            var _this = this,
                txt = content || "";
            return this.content = '<div class="' + this.skin + '-alert">' + '<div class="' + this.skin + '-txt">' + txt + "</div>" + '<div class="' + this.skin + '-btnc">' + '<input type="button" value="" class="' + this.skin + '-btn"/>' + "</div></div>",
                this.onOpen = function(Class) {
                    var btn = $("input", Class.boxer);
                    btn.click(function() {
                            Class.destroy(),
                                fun && $.isFunction(fun) && fun()
                        }),
                        fun && isclose && $("." + Class.skin + "-closed", Class.boxer).bind("click",
                            function() {
                                fun()
                            })
                },
                this.init(),

                this
        },
        confirm: function(content, fun, isclose) {
            this._dislog();
            this.title = "询问";
            var txt = content || "";
            return this.content = '<div class="' + this.skin + '-confirm"><div class="' + this.skin + '-txt">' + txt + "</div>" + '<div class="' + this.skin + '-btn">' + '<input type="button" value="" class="' + this.skin + '-sure" />' + '<input type="button" value="" class="' + this.skin + '-cancel" /></div>',
                this.onOpen = function(Class) {
                    $("." + Class.skin + "-cancel", Class.boxer).click(function() {
                            Class.destroy(),
                                fun && $.isFunction(fun) && fun(!1)
                        }),
                        $("." + Class.skin + "-sure", Class.boxer).click(function() {
                            Class.destroy(),
                                fun && $.isFunction(fun) && fun(!0)
                        }),
                        fun && isclose && $("." + Class.skin + "-closed", Class.boxer).bind("click",
                            function() {
                                fun(!1)
                            })
                },
                this.init(),
                this
        },
        tip: function(content, Ele_contrast, options) {
            this._dislog();
            var _this = this;
            this.title = "",
                this.content = '<div class="' + this.skin + '-con">' + content + "</div>",
                this.lock = !1,
                this.drag = !1,
                this.fx = 3,
                this.timing = !0,
                this.emptySpace = 1,
                this.repairSpace = 0;
            var contrast = $(Ele_contrast),
                top = contrast.offset().top,
                left = contrast.offset().left,
                width = contrast.width(),
                height = contrast.height();
            for (var name in options) this[name] = options[name];
            this.dom(),
                this.boxer.addClass(this.skin + "-tip");
            if (this.tipClose) {
                var _closed = $("." + this.skin + "-closed", this.boxer);
                _closed.addClass(this.skin + "-closed-on"),
                    _closed.bind("click",
                        function() {
                            _this.destroy()
                        }).hover(function() {
                            $(this).addClass(_this.skin + "-closed-over")
                        },
                        function() {
                            $(this).removeClass(_this.skin + "-closed-over")
                        })
            }
            var w = this.boxer.width(),
                h = this.boxer.height(),
                arrowsPos = {
                    top: 0,
                    left: 0
                };
            return this.towards === "bottom" ? (this.top = top - h - this.emptySpace - 6 + this.repairSpace, arrowsPos = {
                        top: h - this.emptySpace,
                        left: width / 2
                    },
                    width <= w ? (this.left = left, left + w >= $(this.relPanel).width() && (this.left = left - w + width, arrowsPos.left = w - width / 2 - 8)) : (this.left = left + (width - w) / 2, arrowsPos.left = w / 2)) : this.towards === "top" ? (this.top = top + h + this.emptySpace + this.repairSpace, arrowsPos = {
                        top: -6 + this.emptySpace,
                        left: width / 2
                    },
                    width <= w ? (this.left = left, left + w >= $(this.relPanel).width() && (this.left = left - w + width, arrowsPos.left = w - width / 2 - 8)) : (this.left = left + (width - w) / 2, arrowsPos.left = w / 2)) : this.towards === "left" ? (this.top = top - h / 2 + height / 2, arrowsPos = {
                        top: (h - 20) / 2,
                        left: -5
                    },
                    this.left = left + this.emptySpace + width + 8 + this.repairSpace) : this.towards === "right" && (this.top = top - h / 2 + height / 2, arrowsPos = {
                        top: (h - 20) / 2,
                        left: w - 5
                    },
                    this.left = left - this.emptySpace - w - 8 - this.repairSpace),
                this.boxer.css({
                    top: this.top,
                    left: this.left
                }),
                this.boxer.append('<div class="' + this.skin + "-arrows " + this.skin + "-" + this.towards + '" style="left:' + arrowsPos.left + "px;top:" + arrowsPos.top + 'px;"></div>'),
                this.onOpen(this),
                this.setFx(),
                this.timing && (this.setTiming(), this.openTiming()),
                this
        }
    };
    return Dialog;
};
/* 
	*滚动条
	*调用
	*var scroll=new Scroll;
					scroll.init({
							Ele_panel: '.MR-guard-container .list-inner',  //内容区域 
							Ele_scroll: '.MR-guard-container .scroller'	//滚动条
					});		
 */
var Scroll = function() {
    var Scroll = {
        _default: function() {
            var setting = {
                Ele_panel: null,
                Ele_scroll: null,
                fixedHeight: 0,
                height: "auto",
                width: "auto",
                rollerX: !1,
                rollerY: !0,
                flow: !1,
                minHeight: 15,
                minWidth: 15,
                skin: "dds-scroll",
                onScroll: function(type, positionPercent) {}
            };
            return setting;
        },
        _scroll: function(options) {
            var _default = this._default();
            var setting = $.extend({},
                _default, options);
            for (var name in setting) this[name] = setting[name];
            this.scrollCbTimer = null
        },
        init: function(options) {

            this._scroll(options),
                this.Ele_panel = $(this.Ele_panel),
                this.panel_parent = this.Ele_panel.parent(),
                this.Ele_scroll = $(this.Ele_scroll),
                this.rollerY ? (this.height = this.height == "auto" ? this.panel_parent.height() : this.height, this.setHeight(this.height)) : this.rollerX && (this.width = this.width == "auto" ? this.panel_parent.width() : this.width, this.setWidth(this.width)),
                this.flow && this.Ele_scroll.hide(),
                this.dom()
        },
        setHeight: function(h) {
            this.height = h,
                this.panel_parent.css({
                    height: this.height
                }),
                this.Ele_scroll.css({
                    height: this.height
                }),
                this.scrollPanel && (this.scrollPanel.css({
                    height: this.height
                }), this.resetH())
        },
        setWidth: function(w) {
            this.width = w,
                this.panel_parent.css({
                    width: this.width
                }),
                this.Ele_scroll.css({
                    width: this.width
                }),
                this.scrollPanel && (this.scrollPanel.css({
                    width: this.width
                }), this.resetW())
        },
        dom: function() {
            this.scrollPanel = $('<div class="' + this.skin + '-panel"><div class="top-bg"></div><div class="bottom-bg"></div></div>'),
                this.scrollPanel.css({
                    height: this.height
                }),
                this.slider = $('<div class="' + this.skin + '-slider"><div class="top-bg"></div><div class="bottom-bg"></div></div>'),
                this.scrollPanel.append(this.slider),
                this.Ele_scroll.append(this.scrollPanel),
                this.rollerY ? this.resetH() : this.rollerX && (this.scrollPanel.addClass(this.skin + "-panel-x"), this.resetW()),
                this.addEvent()
        },
        clearScreen: function() {
            this.rollerY ? (this.resetH(), this.slider.css({
                top: 0
            }), this.panel_parent.scrollTop(0)) : this.rollerX && (this.resetW(), this.slider.css({
                left: 0
            }), this.panel_parent.scrollLeft(0))
        },
        addEvent: function() {
            var _this = this;
            this.slider.hover(function() {
                        $(this).addClass(_this.skin + "-over")
                    },
                    function() {
                        $(this).removeClass(_this.skin + "-over")
                    }),
                this.scrollPanel.bind("click",
                    function(e) {
                        if (_this.slider.css("display") != "none") {
                            var source = e.srcElement || e.target;
                            if (source.className != _this.skin + "-panel") return;
                            if (_this.rollerY) {
                                var _y = e.pageY - _this.scrollPanel.offset().top,
                                    _top = _this.slider.position().top;
                                _y >= _top ? _this.slider.css({
                                    top: _y - _this.slider.height()
                                }) : _this.slider.css({
                                    top: _y
                                })
                            } else if (this.rollerX) {
                                var _x = e.pageX - _this.scrollPanel.offset().left,
                                    _left = _this.slider.position().left;
                                _x >= _left ? _this.slider.css({
                                    left: _x - _this.slider.width()
                                }) : _this.slider.css({
                                    left: _x
                                })
                            }
                            _this.scrollTxt()
                        }
                    });
            var options = {};
            this.rollerY ? _options = {
                lockX: !0,
                limit: !0,
                mxbottom: this.height,
                onMove: function() {
                    _this.scrollTxt()
                }
            } : this.rollerX && (_options = {
                lockY: !0,
                limit: !0,
                mxright: this.width,
                onMove: function() {
                    _this.scrollTxt()
                }
            });

            var isFF = navigator.userAgent.indexOf("Firefox") >= 0 ? !0 : !1;
            isFF ? this.Ele_panel[0].addEventListener("DOMMouseScroll",
                function(e) {
                    e.preventDefault();
                    var delta = -e.detail / 3;
                    delta && (_this.rollerY ? _this.panel_parent[0].scrollTop -= delta * 20 : _this.rollerX && (_this.panel_parent[0].scrollLeft -= delta * 20), _this.setScrollPos())
                }, !1) : this.Ele_panel[0].onmousewheel = function(e) {
                var e = e ? e : window.event;
                e.preventDefault ? e.preventDefault() : e.returnValue = !1;
                var delta = e.wheelDelta ? e.wheelDelta / 120 : -e.detail / 3;
                delta && (_this.rollerY ? _this.panel_parent[0].scrollTop -= delta * 20 : _this.panel_parent[0].scrollLeft -= delta * 20, _this.setScrollPos())
            }
        },
        setScrollPos: function() {
            if (this.rollerY) {
                var H = this.Ele_panel.height(),
                    _h = this.panel_parent[0].scrollTop,
                    _top = _h * this.height / H;
                _h >= H - this.height && (_top = this.height - this.slider.height()),
                    this.slider.css({
                        top: _top
                    }),
                    this.onScrollCb("y", _h / (H - this.height))
            } else if (this.rollerX) {
                var W = this.Ele_panel.width(),
                    _w = this.panel_parent[0].scrollLeft,
                    _left = _w * this.width / W;
                _w >= W - this.width && (_left = this.width - this.slider.width()),
                    this.slider.css({
                        left: _left
                    }),
                    this.onScrollCb("x", _w / (W - this.width))
            }
        },
        scrollTxt: function() {
            if (this.rollerY) {
                var _top = this.slider.position().top,
                    H = this.Ele_panel.height(),
                    _h = _top * H / this.height;
                _top <= 0 ? _h = 0 : _top >= this.height - this.slider.height() && (_h = H - this.height),
                    this.panel_parent.scrollTop(_h),
                    this.onScrollCb("y", _h / (H - this.height))
            } else if (this.rollerX) {
                var _left = this.slider.position().left,
                    W = this.Ele_panel.width(),
                    _w = _left * W / this.width;
                _left <= 0 ? _w = 0 : _left >= this.width - this.slider.width() && (_w = W - this.width),
                    this.panel_parent.scrollLeft(_w),
                    this.onScrollCb("x", _w / (W - this.width))
            }
        },
        resetW: function() {
            var w = this.coutW();
            this.slider.css({
                    width: w
                }),
                w <= 0 || this.width < this.minWidth ? (this.slider.hide(), this.scrollPanel.hide()) : (this.slider.show(), this.scrollPanel.show()),
                w + this.slider.position().left > this.width && this.toRight()
        },
        resetH: function() {
            var h = this.coutH();
            this.slider.css({
                    height: h
                }),
                h <= 0 || this.height < this.minHeight ? (this.slider.hide(), this.scrollPanel.hide()) : (this.slider.show(), this.scrollPanel.show()),
                h + this.slider.position().top > this.height && this.toBottom()
        },
        toRight: function() {
            if (this.coutW() <= 0) return !1;
            this.slider.css({
                    left: this.width - this.coutW()
                }),
                this.panel_parent.scrollLeft(99999)
        },
        toLeft: function() {
            this.slider.css({
                    left: 0
                }),
                this.panel_parent.scrollTop(0)
        },
        toBottom: function() {
            if (this.coutH() <= 0) return !1;
            this.slider.css({
                    top: this.height - this.coutH()
                }),
                this.panel_parent.scrollTop(99999)
        },
        toTop: function() {
            this.slider.css({
                    top: 0
                }),
                this.panel_parent.scrollTop(0)
        },
        coutW: function() {
            var W = this.Ele_panel.width();
            return W <= this.width ? 0 : this.width * this.width / W
        },
        coutH: function() {
            var H = this.Ele_panel.height();
            return H == 0 && this.fixedHeight != 0 && (H = this.fixedHeight),
                H <= this.height ? 0 : this.height * this.height / H
        },
        onScrollCb: function(type, percent) {
            this.scrollCbTimer && (clearTimeout(this.scrollCbTimer), this.scrollCbTimer = null);
            var _this = this;
            this.scrollCbTimer = setTimeout(function() {
                    _this.onScroll.call(_this, type, percent),
                        _this.scrollCbTimer = null
                },
                200)
        }
    };
    return Scroll;
};
/* 页面缩放高度范围 */
var minHeight = 640;
var maxHeight = 5960;
var minWidth = 375;
/* var minWidth = 1100;*/

/* 虚拟币名称 */
var coin_text = _DATA.config.name_coin;

/* 工具方法 */
var WlTools = {
    FormatNowDate: function() {
        var mDate = new Date();
        var H = mDate.getHours();
        var i = mDate.getMinutes();
        var s = mDate.getSeconds();
        if (H < 10) {
            H = '0' + H;
        }
        if (i < 10) {
            i = '0' + i;
        }
        if (s < 10) {
            s = '0' + s;
        }
        //return H + ':' + i + ':' + s;
        return H + ':' + i;
    },
    /**
     *  JS版  数字 金额格式化
     * @param string s 需要处理的数字串
     * @param string n 保留小数的位数
     */
    fmoney: function(s, n) {
        n = n >= 0 && n <= 20 ? n : 2;
        s = parseFloat((s + "").replace(/[^\d\.-]/g, "")).toFixed(n) + ""; //更改这里n数也可确定要保留的小数位  
        var l = s.split(".")[0].split("").reverse(),
            r = s.split(".")[1];
        t = "";
        for (i = 0; i < l.length; i++) {
            t += l[i] + ((i + 1) % 3 == 0 && (i + 1) != l.length ? "," : "");
        }
        if (n > 0) {
            return t.split("").reverse().join("") + "." + r.substring(0, n); //保留2位小数  如果要改动 把substring 最后一位数改动就可  
        } else {
            return t.split("").reverse().join("");
        }
    },
    jsonTostr: function(json) {
        return JSON.stringify(json);
    },
    strTojson: function(str) {
        return JSON.parse(str);
    }

};

/* 判断 */
var Check = {
    checkCoin: function(needcoin) {
        if (needcoin > _DATA.user.coin) {
            return !1;
        } else {
            return !0;
        }
    },
    checkLogin: function() {
        if (_DATA.user) {
            return !0;
        } else {
            $(".hd-login .no-login").click();
            return !1;
        }

    },
    checkNum: function(num) {
        var patrn = /^(\d)*$/;
        if (patrn.test(num)) {
            return !0;
        } else {
            return !1;
        }

    }


};
/* 会员 */
var User = {
    userMoney: $(".MR-user .money"),
    listBtn: $("#LF-toggle-online"),
    attionBtn: $(".follow_status"),
    //moreListBtn:$(".MR-online .user-con .more-toggle"),
    userCon: $(".MR-online .user-con .boarder ul"),
    userCite: $(".MR-online .nav-tab cite"),
    adiCon: $(".MR-online .admin-con .boarder ul"),
    user_Ele_panel: '.MR-online .user-con .boarder ul',
    user_Ele_scroll: '.MR-online .user-con .scroller',
    adi_Ele_panel: '.MR-online .admin-con .boarder ul',
    adi_Ele_scroll: '.MR-online .admin-con .scroller',
    userlistutl: '/index.php?g=home&m=show&a=getUserList',
    attionUrl: '/index.php?g=home&m=show&a=attention',
    popupInfo: '/index.php?g=home&m=show&a=popupInfo',
    minus_h: 491,
    defnums: 20,
    user_ismore: 1,
    user_times: 0,
    user_isfirst: 1,
    adi_isfirst: 1,
    popup_to: 85,

    init: function() {
        this.setUsersScorll();
        this.setUserHeight();
        this.addEvent();
        //this.getOnline(); //改为eventListen中socket连接成功后调取用户列表
    },
    /*用户列表弹窗popup*/
    popup: function(id) {


        clearTimeout(timeout);


        var _this = this;
        if ($("#popup_info").length > 0) {
            $("#popup_info").remove();
        }
        var top = $("#anchor_" + id).position().top;
        var winHeight = $(window).height();
        winHeight = winHeight - 50;
        if (winHeight - top < 160 && id != _DATA.user.id) {
            top = top - 27;
        } else {
            top = this.popup_to + top;
        }
        var html = '';

        if (_DATA.user == null) {
            $('.hd-login .no-login').click();
            return;
        }

        if (id > 0 && id != _DATA.user.id) {
            $.ajax({
                type: "POST",
                url: this.popupInfo,
                data: { touid: id, roomid: _DATA.anchor.id },
                async: false,
                error: function(request) {
                    layer.msg("网络请求出错！");
                },
                success: function(data) {

                    var data = JSON.parse(data);
                    html += '<div class="popup" id="popup_info" popup-data-name="' + data.info.user_nicename + '" popup-data-uid="' + data.info.id + '" style="top:' + top + 'px;display:none"><div class="pop_content">';
                    html += '<div class="popup_img"><img src="' + data.info.avatar + '"/></div><div class="handle"><div class="name">昵称:<a>' + data.info.user_nicename + '</a></div>';

                    if (data.uid_admin > 10) {
                        if (data.uid_admin == 60) /*当前身份为超管时*/ {
                            if (data.touid_admin == 50) { //对方身份为主播
                                html += '<span onclick="Controls.stopit()" id="popup_stopit">停止直播</span>';
                                html += '<span onclick="Controls.close()" id="popup_close">关闭直播</span>';
                            } else if (data.touid_admin < 60) //对方身份不为超管
                            {
                                html += '<span onclick="Controls.gag()" id="popup_gag">禁止发言</span>';
                                html += '<span onclick="Controls.tiren()" id="popup_tiren">踢出房间</span>';
                            }
                        } else if (data.uid_admin == 50) /* 当前用户为主播 */ {
                            if (data.touid_admin < 50) //当对方身份不为超管时
                            {
                                if (data.touid_admin == 40) { /* 对方为管理员 */
                                    html += '<span onclick="Controls.cancel()" id="popup_setup">取消管理</span>';
                                } else {
                                    html += '<span onclick="Controls.cancel()" id="popup_cancel">设为管理</span>';
                                }
                                html += '<span onclick="Controls.gag()" id="popup_gag">禁止发言</span>';
                                html += '<span onclick="Controls.tiren()" id="popup_tiren">踢出房间</span>';
                            }
                        } else if (data.uid_admin == 40) /* 当前用户为管理员 */ {
                            /* 当前用户为管理员 */
                            if (data.touid_admin < 40) {
                                /* 对方为普通用户 */
                                html += '<span onclick="Controls.gag()" id="popup_gag">禁止发言</span>';
                                html += '<span onclick="Controls.tiren()" id="popup_tiren">踢出房间</span>';
                            }
                        }
                        //判断对方身份 只有不是游客都加入
                        if (data.touid_admin > 10) {
                            if (data.isBlack == 1) {
                                html += '<span onclick="Controls.black()" id="popup_black">移除拉黑</span>';
                            } else {
                                html += '<span onclick="Controls.black()" id="popup_black">加黑名单</span>';
                            }
                            // html+='<span onclick="Controls.HxChat_User()" id="HxChat_User">开始私聊</span>';
                        }
                    }
                    html += '</div></div></div>';
                    $("#nav_con").append(html);
                    $("#popup_info").fadeIn();

                    timeout = setTimeout(function() {
                        $("#popup_info").remove();
                    }, 5000);
                }
            })
        } else if (id == _DATA.user.id) {
            html += '<div class="popupOwn" id="popup_info" style="top:' + top + 'px;display:none"><div class="pop_contentOwn">';
            html += '找到自己了呢！';
            html += '</div></div>';
            $("#nav_con").append(html);
            $("#popup_info").fadeIn();

            setTimeout(function() {
                $("#popup_info").remove();
            }, 5000);
        }
    },
    /* 用户列表 */
    setUsersScorll: function() {
        this.usersscroll = new Scroll;
        this.usersscroll.init({
            Ele_panel: this.user_Ele_panel,
            Ele_scroll: this.user_Ele_scroll
        });
        this.usersscroll.toBottom();
    },
    resetusersH: function(height, h) {
        $(".MR-online .user-con .boarder").css("height", h),
            this.usersscroll.setHeight(h),
            this.usersscroll.resetH(),
            this.usersscroll.toBottom();
    },
    resetuserssH: function() {
        this.usersscroll.resetH(),
            this.usersscroll.toBottom();
    },
    setUserHeight: function() {
        var _this = this;
        //var height=$(window).height();
        var height = $("#LF-stager").height();
        if (height < minHeight) {
            height = minHeight;
        } else if (height > maxHeight) {
            height = maxHeight;
        }
        var user_list_bottom = $(".MR-chat .user_list_bottom").height();
        console.log("页面高度：");
        console.log(height);
        console.log();
        var h = height - _this.minus_h - 20 - user_list_bottom;
        console.log(h);
        _this.resetusersH(height, h);
    },
    /* 管理员列表 */
    setAdiScorll: function() {
        this.adiscroll = new Scroll;
        this.adiscroll.init({
            Ele_panel: this.adi_Ele_panel,
            Ele_scroll: this.adi_Ele_scroll
        });
        this.adiscroll.toBottom();
    },
    resetadiH: function(height, h) {
        $(".MR-online .admin-con .boarder").css("height", h),
            this.adiscroll.setHeight(h),
            this.adiscroll.resetH(),
            this.adiscroll.toBottom();
    },
    resetadisH: function() {
        this.adiscroll.resetH(),
            this.adiscroll.toBottom();
    },
    setAdiHeight: function() {
        var _this = this;
        //var height=$(window).height();
        var height = $("#LF-stager").height();
        if (height > minHeight && height < maxHeight) {
            var h = height - _this.minus_h;
            _this.resetadiH(height, h);
        }
    },
    addEvent: function() {
        var _this = this;
        $(window).on("resize", function() {
            _this.setUserHeight();
        })
        $(document).mouseup(function(e) {
            var _con = $('#LF-nav-bg,#LF-toggle-online'); // 设置目标区域
            if (_con.hasClass("bg-show")) {
                if (!_con.is(e.target) && _con.has(e.target).length === 0) { // Mark 1
                    _this.closeNavBg();

                }
            }

        });
        /*this.listBtn.on("click",function(){
        	if($(this).hasClass("on")){
        		if($("#popup_info").length>0){

        			$("#popup_info").remove();
        		}

        		$(this).removeClass("on");
        		_this.closeNavBg();
        	}else{
        		$(this).addClass("on");
        		_this.showNavBg();
        	}
        	
        })*/
        /*this.moreListBtn.on("click",function(){
        	_this.getOnline();
        })*/
        this.attionBtn.on("click", function() {
            _this.setAttention();

        })
    },
    setAttention: function() {
        var _this = this;
        if (!Check.checkLogin()) {
            //(new Dialog).tip("请登录", _this.chatSendBtn, {delay:2e3});
            return !1;
        }
        var attion = _this.attionBtn.html();
        if (attion == '已关注') {
            //(new Dialog).tip("请登录", _this.chatSendBtn, {delay:2e3});
            return !1;
        }
        $.ajax({
            url: _this.attionUrl,
            data: { roomnum: _DATA.anchor.id },
            dataType: 'json',
            success: function(data) {
                if (data.error == 0) {
                    _this.attionBtn.html(data.msg);
                    $(".fans_num").html("粉丝数:" + data.data);
                    var ct = _DATA.user.user_nicename + "关注了主播";
                    var msg = '{"msg":[{"_method_":"SystemNot","action":"13","ct":"' + ct + '","msgtype":"4","uname":"' + _DATA.user.user_nicename + '","toname":"' + _DATA.anchor.user_nicename + '","touid":' + _DATA.anchor.id + ',"uid":' + _DATA.user.id + ',"level":' + _DATA.user.level + ',"vip_type":"0","liangname":"0","usertype":"' + _DATA.usertype + '","guard_type":"' + _DATA.user.guard_type + '"}],"retcode":"000000","retmsg":"ok"}';
                    Socket.emitData('broadcast', msg);
                } else {
                    (new Dialog).alert(data.msg);
                }
            }
        })

    },
    showNavBg: function() {
        $("#LF-nav-bg").show().addClass("bg-show").animate({
            "left": '70px',
            "opacity": 1
        }, 200)
        this.getOnline();
    },
    closeNavBg: function() {
        if ($("#popup_info").length > 0) {

            $("#popup_info").remove();
        }
        //this.listBtn.removeClass("on");
        $("#LF-nav-bg").removeClass("bg-show").animate({
            "left": '-190px',
            "opacity": 0
        }, 200);
        setTimeout(function() {
            $("#LF-nav-bg").hide();
        }, 300)
    },
    getOnline: function() {
        var _this = this;
        if (_this.user_ismore == 1) {
            //这里的人数是通过输出用户列表时进行统计，如果想得到redis中的人数 可以使用data.nums

            $.ajax({
                url: _this.userlistutl,
                data: { showid: _DATA.anchor.id, times: _this.user_times, defnums: _this.defnums },
                dataType: "json",
                success: function(data) {
                    var list = data.list,
                        len = list.length,
                        html = '';
                    var n = 0;
                    for (var i = 0; i < len; i++) {
                        var userinfo = list[i];
                        n = n + 1;

                        html += '<li data-cardid="' + userinfo.id + '" id="anchor_' + userinfo.id + '" class="anchor">\
									<a class="xinxi" onclick="User.popup(' + userinfo.id + ')">\
										<div class="user_avatar">\
											<img src="' + userinfo.avatar_thumb + '">\
										</div>\
										<div class="user_info">\
											<p>\
												<span class="name" title="' + userinfo.user_nicename + '">' + userinfo.user_nicename + '</span>\
												<img class="level" src="' + _DATA.level[userinfo.level]['thumb'] + '">\
												<div class="clearboth"></div>\
											</p>\
											<p class="user_id">ID:' + userinfo.id + '</p>\
										</div>\
									</a>\
								</li>';

                    }
                    /*alert(html);
                    alert(_this.user_isfirst);*/

                    /*if(_this.user_isfirst==1){
                    	_this.userCon.html(html);
                    	_this.userCite.html(data.nums);
                    }else{
                    	_this.userCon.append(html);
                    	_this.userCite.html(data.nums);
                    }*/

                    _this.userCon.html(html);
                    _this.userCite.html(data.nums);

                    _this.resetuserssH();

                    /*if(len < _this.defnums){
                    	_this.moreListBtn.hide();
                    }else{
                    	_this.moreListBtn.show();
                    }*/
                },
                error: function(request) {
                    console.log("数据请求失败");
                }

            });

        }


    },
    updateMoney: function() {
        this.userMoney.find("cite").html(_DATA.user.coin);
        this.userMoney.find("cite").attr("title", _DATA.user.coin + coin_text);
    }
};
/* 视频 */
var Video = {
    resolution_w: 9,
    resolution_h: 16,
    plus_w: 676 + 20,
    stager: $("#LF-stager"),
    LF_video: $("#LF-area-video"),
    player: $("#LF-area-video .channel-player-v2"),
    playerObj: $("#playerzmblbkjP"),
    about: $(".MR-about"),
    aboutdh: 40,
    abouth: 110,
    videoMinWidth: 404,
    endReUrl: '/index.php?g=home&m=show&a=endRecommend',
    endReCon: $("#SR-video-rec-con"),
    init: function() {
        //this.setHeight();
        //this.addEvent();
    },

    setHeight: function() {
        var height = $(window).height();
        if (height > maxHeight) {
            var p = (height - maxHeight) / 2;
            this.stager.css("padding-top", p);
            height = maxHeight;
        } else if (height < minHeight) {
            height = minHeight;
        }
        var w = (height / this.resolution_h * this.resolution_w) - 11;
        //console.log("++++");
        //console.log(w);
        if (w < this.videoMinWidth) {
            w = this.videoMinWidth;
        }
        var stager_w = w + this.plus_w;
        if (stager_w < minWidth) {
            stager_w = minWidth;
        }
        this.LF_video.css({ "width": w, "height": height - 20 });
        this.player.css({ "width": w, "height": height - 20 });
        this.playerObj.css({ "width": w, "height": height - 20 });
        this.stager.css({ "width": stager_w });
    },
    addEvent: function() {
        var _this = this;
        $(window).on("resize", function() {
                _this.setHeight();
            })
            /*this.about.hover(function(){
            		$(this).addClass("MR-about-hover").animate({height:_this.abouth},200);
            	},
            	function(){
            		$(this).removeClass("MR-about-hover").animate({height:_this.aboutdh},200);
            	}
            )*/

    },
    endRecommend: function() {
        var _this = this;
        if (_DATA.user && _DATA.user.id == _DATA.anchor.id) {
            return !1;
        }
        $("#playerzmblbkjP_wrapper").css('display', 'none');
        $.ajax({
            url: _this.endReUrl,
            data: {},
            dataType: 'json',
            success: function(data) {
                if (data.error == 0) {
                    var len = data.data.length;
                    if (len > 0) {
                        var html = '';
                        for (var i = 0; i < len; i++) {
                            html += '<li>\
											<div class="rec-item">\
												<a href="/' + data.data[i].uid + '"><img src="' + data.data[i].userinfo.avatar + '" /></a>\
												<img class="levelanchor" src="' + _DATA.level_anchor[data.data[i].userinfo.level_anchor]['thumb'] + '">\
												<span class="rec-author-name rec-row">' + data.data[i].userinfo.user_nicename + '</span>\
												</div>\
										</li>';
                        }
                        _this.endReCon.find(".video-rec-con ul").html(html);
                        _this.endReCon.show();
                    }

                    $(".channel-player-v2").find("object").remove();
                }
            }
        })
    },
    statRecommend: function() {
        window.location.href = "";
    }
};

/* 聊天 */
var Chat = {
    interval: 5,
    chat_max_text_len: 30,
    fly_gold_max_text_len: 30,
    fly_site_max_text_len: 15,
    chat_default_value: '和大家聊会儿天？',
    flt_default_value: '请输入...',
    isfree: 0,
    sendUrl: '/index.php?g=home&m=Spend&a=sendHorn',
    isShutUp: '/index.php?g=home&m=Spend&a=isShutUp',
    Ele_panel: '.msg-chat .MR-chat .boarder ul',
    Ele_scroll: '.msg-chat .MR-chat .scroller',
    minus_h: 428,
    chatSendBtn: $(".MR-talk .send-btn"),
    chatContent: $(".MR-talk .speaker input"),
    chatCite: $(".MR-talk .speaker cite"),
    hornToggle: $(".MR-horn .toggle"),
    hornSelector: $(".MR-horn .selector"),
    hornDia: $(".MR-horn .dialog"),
    hornDiaT: $(".MR-horn .dialog textarea"),
    hornType: 0,
    init: function() {
        this.setScorll();
        this.addEvent();
        this.setHeight();
    },
    setScorll: function() {
        this.scroll = new Scroll;
        this.scroll.init({
            Ele_panel: this.Ele_panel,
            Ele_scroll: this.Ele_scroll
        });
        this.scroll.toBottom();
    },
    resetH: function(height, h) {
        $("#LF-area-chat").css("height", height - 20), //减去上下padding值
            $(".msg-chat .MR-chat .boarder").css("height", h),
            this.scroll.setHeight(h),
            this.scroll.resetH(),
            this.scroll.toBottom();
    },
    resetsH: function() {
        this.scroll.resetH(),
            this.scroll.toBottom();
    },
    setHeight: function() {
        var _this = this;
        var height = $(window).height();
        if (height > maxHeight) {
            height = maxHeight;
        } else if (height < minHeight) {
            height = minHeight;
        }
        var h = height - _this.minus_h;
        _this.resetH(height, h);
    },
    addEvent: function() {
        var _this = this;
        $(window).on("resize", function() {
            _this.setHeight();
        })
        this.chatContent.on({
            focus: function() {
                if (_this.chatContent.val() == _this.chat_default_value) {
                    _this.chatContent.addClass("txt-focus").val("");
                }
            },
            blur: function() {
                if (_this.chatContent.val() == '') {
                    _this.chatContent.removeClass("txt-focus").val(_this.chat_default_value);
                }
            },
            keyup: function() {
                var chatmsg = $.trim(_this.chatContent.val());
                var txtnum = chatmsg.length;
                if (txtnum > _this.chat_max_text_len) {
                    var num = txtnum - _this.chat_max_text_len;
                    _this.chatCite.html(num);
                    _this.chatCite.addClass("txt-error");
                } else {
                    var num = _this.chat_max_text_len - txtnum;
                    _this.chatCite.html(num);
                    _this.chatCite.removeClass("txt-error");
                }
            },
            keydown: function(e) {
                if (e.keyCode == 13) {
                    _this.sendChat();
                }
            }
        })
        this.chatSendBtn.on("click", function() {
            _this.sendChat();
        })
        this.hornToggle.on("click", function() {
            _this.showHorn();
        })
        this.hornSelector.find(".closed").on("click", function() {
            _this.closeHorn();
        })
        this.hornSelector.find(".btn").on("click", function() {
            if ($(this).hasClass("gold")) {
                _this.hornType = 1;
            } else {
                _this.hornType = 2;
            }
            _this.showHornDia();
        })

        this.hornDiaT.on({
            focus: function() {
                if (_this.hornDiaT.val() == _this.flt_default_value) {
                    _this.hornDiaT.addClass("txt-focus").val("");
                }
            },
            blur: function() {
                if (_this.hornDiaT.val() == '') {
                    _this.hornDiaT.removeClass("txt-focus").val(_this.flt_default_value);
                }
            },
            keyup: function() {
                var chatmsg = $.trim(_this.hornDiaT.val());
                var txtnum = chatmsg.length;
                if (_this.hornType == 1) {
                    var max = _this.fly_gold_max_text_len;
                } else {
                    var max = _this.fly_site_max_text_len;
                }

                if (txtnum > max) {
                    var num = txtnum - max;
                    /* _this.hornDia.find("span").eq(0).html("已经超过"+num+"个字");
                    _this.hornDia.find("span").eq(0).addClass("txt-error"); */
                } else {
                    var num = max - txtnum;
                    /* 	_this.hornDia.find("span").eq(0).html("还能输入"+num+"个字"); */
                    /* _this.hornDia.find("span").eq(0).removeClass("txt-error"); */
                }
            }
        })
        this.hornDia.find(".closed").on("click", function() {
            _this.closeHormDia();
        })
        this.hornDia.find(".horn-send").on("click", function() {
            _this.sendHorn();
        })

    },
    showHorn: function() {
        var _this = this;
        this.hornSelector.show().addClass("selector-show");

    },
    closeHorn: function() {
        this.hornSelector.removeClass("selector-show").hide();
    },
    showHornDia: function() {
        var _this = this;
        if (this.hornType == 1) {
            this.hornDia.find("h4")[0].className = "gold", this.hornDia.find("i").html("金喇叭"), this.hornDia.find(".detail").removeClass("detail-site");
            /* 	_this.hornDia.find("span").eq(0).html("还能输入"+_this.fly_gold_max_text_len+"个字"); */
        } else {
            this.hornDia.find("h4")[0].className = "site", this.hornDia.find("i").html("弹幕"), this.hornDia.find(".detail").addClass("detail-site");
            /* _this.hornDia.find("span").eq(0).html("还能输入"+_this.fly_site_max_text_len+"个字"); */
        }
        _this.hornDiaT.val(_this.flt_default_value).removeClass("txt-focus");
        this.hornSelector.removeClass("selector-show").addClass("selector-fx");
        setTimeout(function() {
                _this.hornDia.show().addClass("dialog-fx"),
                    _this.hornSelector && _this.hornSelector.hide().removeClass("selector-fx")
            },
            600)

    },
    closeHormDia: function() {
        this.hornDia.removeClass("dialog-fx").hide();
    },
    sendChat: function() {
        var _this = this;
        var chatmsg = $.trim(_this.chatContent.val());
        if (!Check.checkLogin()) {
            //(new Dialog).tip("请登录", _this.chatSendBtn, {delay:2e3});
            return !1;
        }
        if (!chatmsg || chatmsg == _this.chat_default_value) {
            (new Dialog).tip("内容不能为空", _this.chatContent, { delay: 2e3 });
            return !1;
        }
        if (chatmsg.length > _this.chat_max_text_len) {
            (new Dialog).tip("内容不能超过" + _this.chat_max_text_len + "个字", _this.chatContent, { delay: 2e3 });
            return !1;
        }
        $.ajax({
            dataType: 'json',
            url: this.isShutUp,
            data: { showid: _DATA.anchor.id }, // 你的formid
            error: function(request) {
                layer.msg("数据请求失败！");
            },
            success: function(data) {
                if (data['info'] == 1) {
                    layer.msg("你已经被禁言");
                } else {
                    if (data['admin'] == 60) {
                        var msg = '{"msg":[{"_method_":"SystemNot","action":"13","ct":"' + chatmsg + '","msgtype":"4","uname":"' + _DATA.user.user_nicename + '","toname":"' + _DATA.anchor.user_nicename + '","touid":' + _DATA.anchor.id + ',"uid":' + _DATA.user.id + ',"level":' + _DATA.user.level + ',"vip_type":"' + _DATA.user.vip.type + '","liangname":"' + _DATA.user.liang.name + '","usertype":"' + _DATA.usertype + '","guard_type":"' + _DATA.user.guard_type + '"}],"retcode":"000000","retmsg":"ok"}';
                    } else {
                        var msg = '{"msg":[{"_method_":"SendMsg","action":0,"ct":"' + chatmsg + '","msgtype":"2","tougood":"","touid":"","touname":"","ugood":"' + _DATA.user.id + '","uid":"' + _DATA.user.id + '","uname":"' + _DATA.user.user_nicename + '","level":"' + _DATA.user.level + '","vip_type":"' + _DATA.user.vip.type + '","liangname":"' + _DATA.user.liang.name + '","usertype":"' + _DATA.usertype + '","guard_type":"' + _DATA.user.guard_type + '"}],"retcode":"000000","retmsg":"OK"}';
                    }
                    Socket.emitData('broadcast', msg);
                }
            }
        });
        _this.chatContent.val('');
    },
    sendHorn: function() {
        var _this = this;
        //console.log(_this.hornType);
        var chatmsg = $.trim(_this.hornDiaT.val());
        if (!Check.checkLogin()) {
            //(new Dialog).tip("请登录", _this.giftSendBtn, {delay:2e3});
            return !1;
        }
        if (!chatmsg || chatmsg == _this.flt_default_value) {
            (new Dialog).tip("内容不能为空", _this.hornDiaT, { delay: 2e3 });
            return !1;
        }
        if (_this.hornType == 1) {
            var needcoin = 0;
        } else {
            /*本站喇叭走这里*/
            var needcoin = barrage_fee;

        }
        var coin = $("#LF-user .MR-user .login .money cite").html();
        if (coin < needcoin && _DATA.user.coin < needcoin) {
            if (!Check.checkCoin(needcoin)) {
                (new Dialog).tip('<div class="status-no-money" id="_temp_DDS_noEnoughMoney">\
									<span>抱歉，您的星币不足哦</span>\
									<div class="opt">\
										<a class="BTN BTN-recharge" href="/index.php?g=home&m=Payment&a=index" target="_blank">充值</a>\
									</div>\
								</div>', _this.hornDia.find(".horn-send"), { delay: 2e3 });
                return !1;
            }
        }
        var stream = (_DATA.live && _DATA.live.stream) || 0;
        if (stream == 0) {
            layer.msg("主播未开播，不能发送弹幕");
            return !1;
        }
        $.ajax({
            url: _this.sendUrl,
            data: { liveuid: _DATA.anchor.id, content: chatmsg, stream: stream },
            dataType: 'JSON',
            success: function(data) {
                //console.log(data);
                if (data.code != 0) {
                    layer.msg(data.msg);
                    return !1;
                } else {
                    var data = data.info;
                    _DATA.user.level = data.level;
                    _DATA.user.coin = data.coin;
                    User.updateMoney();
                    var msg = '{"msg":[{"sex":"","action":"7","city":"","usign":"","ugood":"' + _DATA.user.id + '","roomnum":"' + _DATA.anchor.id + '","level":"' + data.level + '","timestamp":"","equipment":"app","uid":"' + _DATA.user.id + '","ct":"' + data.barragetoken + '","touid":"0","_method_":"SendBarrage","msgtype":"1","tougood":"","uhead":"' + _DATA.user.avatar + '","uname":"' + _DATA.user.user_nicename + '","touname":"","vip_type":"' + _DATA.user.vip.type + '","liangname":"' + _DATA.user.liang.name + '","usertype":"' + _DATA.usertype + '","guard_type":"' + _DATA.user.guard_type + '"}],"retmsg":"OK","retcode":"000000"}';
                    Socket.emitData('broadcast', msg);
                    _this.hornDia.removeClass("dialog-fx").hide();
                    _this.hornDiaT.val(_this.flt_default_value);
                }
            },
            error: function(data) {
                console.log("出错啦~");
            }
        })
    },
    clearChat: function() {

    },
    setDisabled: function() {


    }
};
/* 礼物记录 */
var Giftlist = {
    Ele_panel: '.msg-gift .MR-chat .boarder ul',
    Ele_scroll: '.msg-gift .MR-chat .scroller',
    difHeight: 170,
    init: function() {
        this.setScorll();
        this.addEvent();
    },
    setScorll: function() {
        this.scroll = new Scroll;
        this.scroll.init({
            Ele_panel: this.Ele_panel,
            Ele_scroll: this.Ele_scroll
        });
    },
    resetH: function() {
        this.scroll.resetH(),
            this.scroll.toBottom();
    },
    addEvent: function() {
        var _this = this;
        $(window).on("resize", function() {
            $(".msg-gift .MR-chat .boarder").css("height", _this.difHeight);
            _this.resetH();
        })
    }

};

/* 礼物 */
var Gift = {
    giftLi: $(".gift-con .con li"),
    giftTip: $(".MR-gift-tip"),
    giftGroup: $(".gift-group"),
    giftSendBtn: $(".MR-gift .send-btn"),
    giftSelBox: $(".MR-gift .select-box"),
    giftNum: $(".MR-gift .num-input"),
    difHeight: 170,
    selectId: 0,
    giftSelListH: 0,
    sendUrl: '/index.php?g=home&m=Spend&a=sendGift',
    tiptime: null,
    init: function() {
        this.addEvent();
    },
    addEvent: function() {
        var _this = this;
        this.giftLi.on("click", function() {
            $(this).siblings().removeClass("selected");
            $(this).addClass("selected");
            _this.selectId = $(this).data("id");
        })
        this.giftLi.hover(function() {
            _this.tiptime && clearTimeout(_this.tiptime);
            _this.showtips($(this));
        }, function() {
            _this.tiptime = setTimeout(function() {
                    _this.hidetips();
                },
                50)
        })
        this.giftTip.hover(function() {
            _this.tiptime && clearTimeout(_this.tiptime);
        }, function() {
            _this.giftTip.hide();
        })
        this.giftSendBtn.on("click", function() {
            _this.sendGift();
        })
    },
    showtips: function(ele) {
        var giftid = ele.data("id"),
            giftinfo = _DATA.gift[giftid],
            pos = ele.position();

        this.giftTip.find(".tip-img").attr("src", giftinfo.gifticon);
        this.giftTip.find(".gift-tip-name").html(giftinfo.giftname);
        this.giftTip.find(".gift-tip-price").html(giftinfo.needcoin + coin_text);
        this.giftTip.find(".gift-tip-desc").html('');

        this.giftTip.attr("style", "");
        var tipWidth = this.giftTip.width();
        pos.left + tipWidth > this.giftGroup.width() ? this.giftTip.css({
                right: "" + (this.giftGroup.width() - pos.left - 53) + "px"
            }) : this.giftTip.css({
                left: "" + pos.left + "px"
            }),
            this.giftTip.show();
    },
    hidetips: function() {
        this.giftTip.hide();
    },
    sendGift: function() {
        var _this = this;
        if (!Check.checkLogin()) {
            (new Dialog).tip("请登录", _this.giftSendBtn, { delay: 2e3 });
            return !1;
        }
        if (_DATA.anchor.id == _DATA.user.id) {
            (new Dialog).tip("不允许给自己送礼物", _this.giftSendBtn, { delay: 2e3 });
            return !1;
        }
        if (_DATA.live == null || _DATA.live.islive == 0) {
            (new Dialog).tip("主播未开播，不能送礼物", _this.giftSendBtn, { delay: 2e3 });
            return !1;
        }
        if (_this.selectId == 0) {
            (new Dialog).tip("请选择礼物", _this.giftSendBtn, { delay: 2e3 });
            return !1;
        }
        var needcoin = _DATA.gift[_this.selectId].needcoin * 1;
        if (!Check.checkCoin(needcoin)) {
            (new Dialog).tip('<div class="status-no-money" id="_temp_DDS_noEnoughMoney">\
								<span>抱歉，您的星币不足哦</span>\
								<div class="opt">\
									<a class="BTN BTN-recharge" href="/index.php?g=home&m=Payment&a=index" target="_blank">充值</a>\
								</div>\
							</div>', _this.giftSendBtn, { delay: 2e3 });
            return !1;
        }

        var showid = (_DATA.live && _DATA.live.showid) || 0;
        //touid 主播Id giftid礼物ID giftcount 礼物数量
        $.ajax({
            url: _this.sendUrl,
            data: { touid: _DATA.anchor.id, giftid: _this.selectId, showid: showid },
            cache: true,
            type: "POST",
            success: function(data) {
                var data = JSON.parse(data);
                if (data.errno != 0) {
                    (new Dialog).alert(data.msg);
                    return !1;
                } else {
                    _DATA.user.level = data.level;
                    _DATA.user.coin = data.coin;
                    User.updateMoney();
                    data.level = data.level.toString();
                    data.uid = data.uid.toString();
                    var msg = '{"retcode":"000000","retmsg":"ok","msg":[{"_method_":"SendGift","action":"0","ct":"' + data.gifttoken + '","msgtype":"1","roomnum":"' + _DATA.anchor.id + '","level":"' + data.level + '","uid":"' + data.uid + '","timestamp":"' + WlTools.FormatNowDate() + '","uname":"' + _DATA.user.user_nicename + '","uhead":"' + _DATA.user.avatar + '","vip_type":"' + _DATA.user.vip.type + '","liangname":"' + _DATA.user.liang.name + '","usertype":"' + _DATA.usertype + '","guard_type":"' + _DATA.user.guard_type + '"}]}';
                    Socket.emitData('broadcast', msg);
                }
            },
            error: function(data) {
                alert("请重试");
            }

        })

    }
};
/* 排行榜 */
var Rank = {
    minus_h: 243,
    tab: $(".MR-rank .tab"),
    tabli: $(".MR-rank .tab li"),
    tabc: $(".MR-rank .con"),
    info: $("#LF-area-info"),
    rank: $(".MR-rank"),
    now_more: $(".now_more"),
    all_more: $(".all_more"),
    rank_other: $(".MR-rank .tab-con .other"),
    url: '/index.php?g=home&m=show&a=rank',
    init: function() {
        this.adddate();
        this.setHeight();
        this.addEvent();
        this.switchTab();
        this.rankMore();
    },
    setScorll: function() {
        var _this = this;
        this.tabli.each(function(i) {
            var scroller = new Scroll;
            scroller.init({
                Ele_panel: _this.tabc.find(".tab-con").eq(i).find(".boards"),
                Ele_scroll: _this.tabc.find(".tab-con").eq(i).find(".board-scroll")
            });
            $(this).data("scroller", scroller);
        })
    },

    setHeight: function() {
        //var _this = this;
        //var height = $(window).height();
        //if (height > maxHeight) {
        //    height = maxHeight;
        //} else if (height < minHeight) {
        //    height = minHeight;
        //}
        //var h = height - _this.minus_h;
        //_this.info.css("height", height - 20); //减去上下padding
        //_this.rank.find(".other").css("height",h-17);		
    },
    resetSH: function() {
        this.tabli.each(function() {
            var _scroller = $(this).data("scroller");
            _scroller.resetH();
            _scroller.toTop();
        })
    },
    addEvent: function() {
        var _this = this;
        $(window).on("resize", function() {
            _this.setHeight();
            //_this.resetSH();

        })
    },
    switchTab: function() {
        var _this = this;
        this.tabli.on("click", function(i) {
            var i = $(this).index();
            $(this).addClass("on");
            $(this).siblings().removeClass("on");
            _this.tabc.find(".tab-con").hide();
            _this.tabc.find(".tab-con").eq(i).show();

            var _scroller = $(this).data("scroller");
            /*_scroller.resetH();
            _scroller.toTop();*/

        })
    },
    adddate: function() {
        var _this = this;
        var showid = (_DATA.live && _DATA.live.showid) || 0;
        $.ajax({
            url: this.url,
            type: "get",
            data: { touid: _DATA.anchor.id, showid: showid },
            dataType: 'json',
            success: function(data) {
                var html = "";
                //console.log(data);
                var html_now_t = _this.setThr(data.now.slice(0, 3)); //前三个
                var html_now_o = _this.setOther(data.now.slice(3)); //其余
                var html_all_t = _this.setThr(data.all.slice(0, 3)); //前三个
                var html_all_o = _this.setOther(data.all.slice(3)); //其余

                html += '<div class="tab-con now"><div class="thr">' + html_now_t + '<div class="clearboth"></div></div><div class="MR-rank-more now_more" onmouseover="show_nowOther()" onmouseleave="hide_nowOther()"></div><div class="other now_other" onmouseover="show_nowOther()" onmouseleave="hide_nowOther()"><div class="boards"><ul class="clearfix">' + html_now_o + '</ul></div></div></div>';

                html += '<div class="tab-con all hide"><div class="thr">' + html_all_t + '<div class="clearboth"></div></div><div class="MR-rank-more all_more" onmouseover="show_allOther()" onmouseleave="hide_allOther()"></div><div class="other all_other" onmouseover="show_allOther()" onmouseleave="hide_allOther()"><div class="boards"><ul class="clearfix">' + html_all_o + '</ul></div></div></div>';

                //console.log(html);


                _this.tabc.html(html);
                /*_this.setScorll();
                _this.setHeight();*/

            },
            error: function() {
                _this.setHeight();
                console.log("Rank 请求失败 或数据为空");
            }
        })


    },
    setThr: function(arr) {
        //console.log(arr);
        var len = 3,
            classes = ["f", "s", "t"];
        _html = "";
        for (var i = 0; i < len; i++) {
            var _temp = {
                uid: "",
                total: "",
                userinfo: {
                    user_nicename: "",
                    avatar: ""
                }
            };




            arr[i] && (_temp = arr[i]);

            /*_html += '<div class="' + classes[i] + '">' + '<div class="stage">',
				_temp.total != "" && (_html += '<span class="ICON-coins">' + _temp.total + "</span>"),
				_html += "</div>",
				_temp.userinfo.avatar != "" && (_html += '<div class="photo"><img src="' + _temp.userinfo.avatar + '" data-id="' + _temp.uid + '" />' + "<cite>" + (i + 1) + '</cite><i></i></div><p class="name" title="' + _temp.userinfo.user_nicename + '">' + _temp.userinfo.user_nicename + "</p>"),
		
				_html += "</div>"*/

            if (parseFloat(_temp.total) > 0) {
                _html += '<div class="' + classes[i] + '"><div class="stage">贡献<span class="ICON-coins">' + _temp.total + '</span></div><div class="photo"><img src="/public/home/show/images/contribute' + (i + 1) + '.png" class="rank_bg"><img src="' + _temp.userinfo.avatar + '" data-id="' + _temp.uid + '" class="rank_f_avatar"></div><p class="name" title="' + _temp.userinfo.user_nicename + '">' + _temp.userinfo.user_nicename + '</p></div>';
            }





        }

        //console.log(_html);
        return _html;
    },
    setOther: function(arr) {
        var _html = "";
        for (var i = 0, len = arr.length; i < len; i++) {
            _html += '<li data-id="' + arr[i].uid + '">',
                _html += "<label>" + (i + 4) + "</label>",
                _html += '<span title="' + arr[i].userinfo.user_nicename + '" class="name">' + arr[i].userinfo.user_nicename + "</span>",
                _html += '<span class="ICON-coins">' + arr[i].total + "</span>",
                _html += "</li>";

            //<li data-id="11153"><label>5</label><span title="手机用户9125" class="name">手机用户9125</span><span class="ICON-coins">1</span></li>		

        }
        return _html;
    },
    rankMore: function() {

        $(".MR-rank-more").on('click', function() {
            //alert("ffffffff");
        });

        this.now_more.on('mouseleave', function() {

        });
    }
};

/* 分享 */
var Share = {
    share: $(".nav-link  .share"),
    info: $("#LF-share"),
    shareBtn: $(".detail a"),
    title: _DATA.config.sitename,
    share_icon: $(".share_icon"),
    share_area: $(".share_area"),
    url: encodeURIComponent(location.href),
    pic: encodeURIComponent(_DATA.anchor.avatar),
    shareText: encodeURIComponent('天啦噜直播还可以这么玩儿，宝宝们快来围观！我在 #' + _DATA.config.sitename + '#，一个高颜值的直播平台！（分享自@' + _DATA.config.sitename + ''),
    api: {
        weibo: "http://service.weibo.com/share/share.php?url={$url}&title={$desc}&pic={$pic}",
        qq: "http://connect.qq.com/widget/shareqq/index.html?url={$url}&desc=&title={$title}&summary={$desc}&pics={$pic}&site={$site}&style=201&width=600&height=400",
        qzone: "http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url={$url}&summary={$desc}&pics={$pic}&width=98&height=22"
    },
    tiptime: null,
    init: function() {
        this.addEvent();
    },
    addEvent: function() {
        var _this = this;
        _this.share.hover(function() {
            _this.tiptime && clearTimeout(_this.tiptime);
            _this.showInfo($(this));
        }, function() {
            _this.tiptime = setTimeout(function() {
                    _this.hideInfo();
                },
                50)
        })
        _this.info.hover(function() {
            _this.tiptime && clearTimeout(_this.tiptime);
        }, function() {
            _this.info.hide();
        })
        _this.shareBtn.on("click", function() {
            var target = $(this).data("target");
            _this.shareInfo(target);

        })
        _this.share_icon.on('mouseover', function() {

            _this.share_area.show();

        }).on('mouseleave', function() {

            intervaltime = setTimeout(function() {
                _this.share_area.hide();
            }, 300);
        })

        _this.share_area.on('mouseover', function() {

            clearTimeout(intervaltime);
            _this.share_area.show();

        }).on('mouseleave', function() {

            intervaltime = setTimeout(function() {
                _this.share_area.hide();
            }, 300);
        })


    },
    showInfo: function() {
        this.info.show().animate({
            "opacity": 1,
            "top": 0
        }, 200);
    },
    hideInfo: function() {
        this.info.hide().css("opacity", 0);
    },
    shareInfo: function(target) {
        var s = this.api[target];
        var u = '';
        u = s.replace("{$url}", this.url),
            u = u.replace("{$title}", this.title),
            u = u.replace("{$desc}", this.shareText),
            u = u.replace("{$pic}", this.pic),
            window.open(u)
    }

};
/*点亮*/
var Light = {
    light_url: '/index.php?g=home&m=Spend&a=light',
    praises: $("#player-praises .praises"),
    init: function() {
        this.addEvent();
    },
    addEvent: function() {
        var _this = this;
        _this.praises.on("click", function() {
            if (_DATA.user == null) {
                $('.hd-login .no-login').click();
            } else {
                if (_DATA.user.id == _DATA.anchor.id) {
                    layer.msg("不能为自己点亮");
                } else {
                    _this.light();
                }
            }

        })
    },
    light: function() {
        var timestamp = Date.parse(new Date());
        timestamp = timestamp / 1000;
        var time = 5 + _DATA.user.lighttime;
        if (_DATA.user.light == 0) {
            _DATA.user.light = 1;
            var msg = '{"retcode":"000000","retmsg":"ok","msg":[{"_method_":"SendMsg","action":"0","msgtype":"2","level":"' + _DATA.user.level + '","uname":"' + _DATA.user.user_nicename + '","uid":"' + _DATA.user.id + '","heart":"1","ct":"我点亮了","vip_type":"' + _DATA.user.vip.type + '","liangname":"' + _DATA.user.liang.name + '","usertype":"' + _DATA.usertype + '","guard_type":"' + _DATA.user.guard_type + '"}]}';
            Socket.emitData('broadcast', msg);
        }
        /* if(time<=timestamp)
        {	
        }
        _DATA.user.lighttime=timestamp; */
        var light = '{"msg":[{"_method_":"light","action":"2","msgtype":"0","vip_type":"0","liangname":"0","usertype":"' + _DATA.usertype + '","guard_type":"' + _DATA.user.guard_type + '"}],"retcode":"000000","retmsg":"OK"}';
        Socket.emitData('broadcast', light);
    }
};
/* 初始化 */
var Initial = {
    init: function() {
        Video.init();
        Chat.init();
        Giftlist.init();
        Gift.init();
        Controls.init();
        Light.init();
        Rank.init();
        User.init();
        Share.init();
    },
    addEvent: function() {
        /* $("#dragLine").KSubfield({
        		_axes: "y",
        		_axesElement: "#upChat,#downChat",
        		_topHeight: 170,
        		_bottomHeight: i
        })	 */
    }

};
var Select = {

};
$(function() {
        $.fn.extend({
            showGiftList: function(t) {
                return this.each(function(e) {
                        var a = $("li", this),
                            i = new Array;
                        a.each(function(e) {
                            return e < t.num ? void $(this).show() : void $(this).hide()
                        });
                        if (t.num - a.size() % t.num < t.num) {
                            for (var e = 0; e < t.num - a.size() % t.num; e++) {
                                i.push("<li " + (a.size() < t.num ? "" : 'style="display:none"') + "></li>");
                            }
                        }

                        $(this).append(i.join(""))
                    }),
                    this
            }
        });
        $(".gift-group .gift-wrap .con ul").showGiftList({
            num: 6
        });
        $(".left-arrow, .right-arrow").click(function() {
            var t = $(this).hasClass("right-arrow"),
                e = $(".gift-wrap:visible"),
                a = $("li:visible:" + (t ? "last" : "first"), e),
                i = a.index();
            if (!(!t && 0 >= i || t && i >= $("li", e).size() - 1)) {
                $("li:visible", e).hide();
                for (var n = 1; 6 >= n; n++) {
                    var o = t ? i + n : i - n;
                    $("li:eq(" + o + ")", e).show()
                }
            }
        })
        $("#LF-nav-bg *:not('.clearfix')").click(function(event) {
            if (event.target == this) {
                $("#popup_info").remove();
            }
            event.stopPropagation();
        })

        Initial.init();

    })
    /**
     * 获取layer居中的位置
     */
var getMiddlePos = function(obj) {
        this.objPop = obj;
        this.winW = oPos.windowWidth();
        this.winH = oPos.windowHeight();
        this.dScrollTop = oPos.scrollY();
        this.dScrollLeft = oPos.scrollX();
        this.dWidth = $('#' + this.objPop).width(), dHeight = $('#' + this.objPop).height();
        this.dLeft = (this.winW / 2) - (this.dWidth) / 2 + this.dScrollLeft;
        this.dTop = (this.winH / 2) - (this.dHeight / 2) + this.dScrollTop;
        return { "pl": this.dLeft, 'pt': this.dTop };
    }
    /**
     * 判断浏览器
     */
var Sys = {};
var Gift_obj = {};
var Gift_numobj = {};
var ua = navigator.userAgent.toLowerCase();
Sys.ie = (s = ua.match(/msie ([\d.]+)/)) ? true : false;
Sys.ie6 = (s = ua.match(/msie ([0-6]\.+)/)) ? s[1] : false;
Sys.ie7 = (s = ua.match(/msie ([7]\.+)/)) ? s[1] : false;
Sys.ie8 = (s = ua.match(/msie ([8]\.+)/)) ? s[1] : false;
Sys.firefox = (s = ua.match(/firefox\/([\d.]+)/)) ? true : false;
Sys.chrome = (s = ua.match(/chrome\/([\d.]+)/)) ? true : false;
Sys.opera = (s = ua.match(/opera.([\d.]+)/)) ? s[1] : false;
Sys.safari = (s = ua.match(/version\/([\d.]+).*safari/)) ? s[1] : false;
Sys.ie6 && document.execCommand("BackgroundImageCache", false, true);
Sys.ispro = ""; //是否推广url过来
String.prototype.hasString = function(a) {
    if (typeof a == "object") {
        for (var b = 0, c = a.length; b < c; b++)
            if (!this.hasString(a[b]))
                return false;
        return true
    } else if (this.indexOf(a) != -1)
        return true
};

/**
 * 计算位置
 */
var dom = document.documentElement || document.body;
var oPos = {
    width: function(a) { return parseInt(a.offsetWidth) },
    height: function(a) { return parseInt(a.offsetHeight) },
    pageWidth: function() { return document.body.scrollWidth || document.documentElement.scrollWidth },
    pageHeight: function() { return document.body.scrollHeight || document.documentElement.scrollHeight },
    windowWidth: function() { var a = document.documentElement; return self.innerWidth || a && a.clientWidth || document.body.clientWidth },
    windowHeight: function() { var a = document.documentElement; return self.innerHeight || a && a.clientHeight || document.body.clientHeight },
    scrollX: function() {
        var b = document.documentElement;
        return self.pageXOffset || b && b.scrollLeft || document.body.scrollLeft
    },
    scrollY: function() {
        var b = document.documentElement;
        return self.pageYOffset || b && b.scrollTop || document.body.scrollTop
    },
    popW: function() { return Math.max(dom.clientWidth, dom.scrollWidth) },
    popH: function() { return Math.max(dom.clientHeight, dom.scrollHeight) }
}
var mousePosition = function(e) {
    var e = e || window.event;
    return { x: e.clientX + oPos.scrollX(), y: e.clientY + oPos.scrollY() }
}



function beforeSearch() {
    //获取输入框的值
    var uid = $("#searchfriend").val();
    searchMember(uid);
}


function searchMember(uid) {
    var searchInfo = uid;
    if (searchInfo == "请输入用户id" || searchInfo == "") {
        alert("请输入用户id！");
        return;
    };

    //将searchResult清空
    $(".searchResult").html("");

    $.ajax({
        type: 'GET',
        url: '/index.php?g=Home&m=User&a=searchMember',
        data: { keyword: searchInfo },
        dataType: 'json',
        success: function(data) {
            $("#searchfriend").val("");
            if (data.code == 1) {
                var msg = "查询失败！";
                $(".searchResult").html(msg);
                $(".searchResult").css("height", 25);
                $("#contractlist11").css('height', 416); //改变陌生人列表高度，让左右对齐
                $("#momogrouplist").css("height", 412).css("overflow", 'auto');
                $("#momogrouplistUL").css("height", 412).css("overflow", 'auto');

            } else if (data.code == 0) {
                var array = data['info'];
                //判断陌生人列表中是否存在该id的li，如果存在，将input清空
                var momoLilength = $("#momogrouplistUL").children("#" + array['id']).length;

                if (momoLilength == 0) {
                    var msg = "<li class='searchUser' type='chat' class='offline' className='offline' onclick=chooseContactDivClick(this) id='" + array['id'] + "'><img style='float:left;' width='' src='" + array['avatar'] + "'><span class='chatUserName'>" + array['user_nicename'] + "</span><div class='clearboth'></div><li>";

                    $(".searchResult").append(msg);
                    $(".searchResult").addClass('searchMsg');
                    $(".searchResult").height(60);
                    $("#momogrouplistUL").css("height", 378).css("overflow", 'auto');
                    $("#momogrouplist").css('height', 376);
                    $("#contractlist11").css('height', 380);


                } else {
                    $("#searchfriend").val("");
                    $(".searchResult").html();
                    $(".searchResult").height(0).removeClass('searchMsg');
                    $("#contractlist11").css('height', 440);
                    $("#momogrouplistUL").css("height", 437).css("overflow", 'auto');
                    alert("该用户已经在列表中");
                }



            } else if (data.code == 2) {
                alert("请先登录再操作");
                $(this).login();
                return;
            }


        },
        error: function() {
            var msg = "查询失败！!";
            $(".searchResult").html(msg);
            $(".searchResult").css("height", 25);
            $(".accordion-group").css("height", 416);
            $("#contractlist11").css('height', 416); //改变陌生人列表高度，让左侧聊天人列表高度和右侧聊天窗口高度对齐
            $("#momogrouplist").css("height", 412).css("overflow", 'auto');
            $("#momogrouplistUL").css("height", 412).css("overflow", 'auto');


        }
    });

}