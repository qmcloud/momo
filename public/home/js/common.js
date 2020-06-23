

/* 礼物 */
(function e(t, n, r) {
    function i(o, u) {
        if (!n[o]) {
            if (!t[o]) {
                var a = typeof require == "function" && require;
                if (!u && a) return a(o, !0);
                if (s) return s(o, !0);
                var f = new Error("Cannot find module '" + o + "'");
                throw f.code = "MODULE_NOT_FOUND",
                f
            }
            var l = n[o] = {
                exports: {}
            };
            t[o][0].call(l.exports,
            function(e) {
                var n = t[o][1][e];
                return i(n ? n: e)
            },
            l, l.exports, e, t, n, r)
        }
        return n[o].exports
    }
    var s = typeof require == "function" && require;
    for (var o = 0; o < r.length; o++) i(r[o]);
    return i
})({
    2 : [function(e, t, n) {
        t.exports = function() {
            function L(e) {
                var t = this,
                n = 3e3,
                r = null;
                this.repeatNumList = [],
                this.active = !0,
                this.repeatId = null,
                this.id = null,
                this.name = null,
                this.avatar = null,
                this.giftType = null,
                this.giftId = null,
                this.giftName = null,
                this.img = null,
                this.addRepeatNum = function(e) {
                    if (t.active) {
                        var n = t.repeatNumList.length; (n == 0 || n > 0 ) && t.repeatNumList.push(e)
                    }
                },
                this.isActive = function() {
                    return t.active
                },
                this.resetCounter = function() {
                    t.active = !0,
                    r && clearTimeout(r),
                    r = setTimeout(function() {
                        t.active = !1,
                        G[t.repeatId] = null,
                        r = null
                    },
                    4000)
                },
                function() {
                    t.repeatId = e.repeatId,
                    t.id = e.id,
                    t.name = e.name,
                    t.avatar = e.avatar,
                    t.giftType = e.giftType,
                    t.giftId = e.giftId,
                    t.giftName = e.giftName,
                    t.img = e.img,
                    t.addRepeatNum(e.repeatNum)
                } (e)
            }
            function A(e) {
                function r() {
                    E || i()
                }
                function i() {
                    o(),
                    E = setTimeout(function() {
                        s() ? (clearTimeout(E), E = null) : i()
                    },
                    N)
                }
                function s() {
                    return x.length == 0
                }
                function o() {
                    function s(e) {
                        setTimeout(function() {
                            e.removeClass("active")
                        },
                        500)
                    }
                    function o() {
                        return C = $(".hjPopGift.hjPopGift_small.first"),
                        k = $(".hjPopGift.hjPopGift_small.last"),
                        C.hasClass("active") && k.hasClass("active") ? [] : k.hasClass("active") ? C: C.hasClass("active") ? k: C
                    }
                    var e = x[0],
                    n = x[1],
                    r = null;
                    T[e].repeatNumList.length > 0 ? (r = $('.hjPopGift.hjPopGift_small[data-repeatid="' + e + '"]'), r.length == 0 && (r = o()), r.length > 0 && (r.attr("data-repeatid", T[e].repeatId), r.attr("data-repeatnum", 0), r.find(".icon-avatar").css("backgroundImage", "url(" + T[e].avatar + ")"), r.find(".nickname").text(T[e].name), r.find(".giftname").text(T[e].giftName), r.find(".icon-gift").css("backgroundImage", "url(" + T[e].img + ")"), r.find(".giftNum").addClass("active").text("x" + T[e].repeatNumList.shift()), s(r.find(".giftNum")), r.addClass("active"), T[e].resetCounter())) : T[e].isActive() || (r = $('.hjPopGift.hjPopGift_small[data-repeatid="' + e + '"]'), x[0] = 0, T[e] = null, r.removeClass("active"), r.removeAttr("data-repeatid"), r.removeAttr("data-repeatNum")),
                    n && T[n].repeatNumList.length > 0 ? (r = $('.hjPopGift.hjPopGift_small[data-repeatid="' + n + '"]'), r.length == 0 && (r = o()), r.length > 0 && (r.attr("data-repeatid", T[n].repeatId), r.attr("data-repeatnum", 0), r.find(".icon-avatar").css("backgroundImage", "url(" + T[n].avatar + ")"), r.find(".nickname").text(T[n].name), r.find(".giftname").text(T[n].giftName), r.find(".icon-gift").css("backgroundImage", "url(" + T[n].img + ")"), r.find(".giftNum").addClass("active").text("x" + T[n].repeatNumList.shift()), s(r.find(".giftNum")), r.addClass("active"), T[n].resetCounter())) : n && !T[n].isActive() && (r = $('.hjPopGift.hjPopGift_small[data-repeatid="' + n + '"]'), x[1] = 0, T[n] = null, r.removeClass("active"), r.removeAttr("data-repeatid"), r.removeAttr("data-repeatnum")),
                    t = [];
                    for (var i = 0; i < x.length; i++) {
                        if (x[i] == 0) continue;
                        t.push(x[i])
                    }
                    x = t
                }
                var t = [],
                n = T[e.repeatId];
                n ? n.addRepeatNum(e.repeatNum) : (T[e.repeatId] = new L(e), x.push(e.repeatId)),
                r()
            }
            function _(e) {
                O.push(e),
                D()
            }
            function D() {
                function e() {
                    var e = O.shift(),
                    t = $(".hjPopGift.hjPopGift_big");
                    t.find("img").attr("src", e.img),
                    t.addClass("active"),
                    setTimeout(function() {
                        t.toggleClass("active out"),
                        setTimeout(function() {
                            t.removeClass("out")
                        },
                        500)
                    },
                    e.imgt * 1e3)
                }
                M || (e(), M = setInterval(function() {
                    O.length > 0 ? e() : (clearInterval(M), M = null)
                },
                5700))
            }
            function j() {
                var e = '<div class="hj_showArea"><ul></ul></div><div class="hj_tip"></div><div class="hj_inputArea"></div>';
                return e
            }
            function F() {
						
                var e = '<div class="hjPopGift hjPopGift_small first">';
                return e += '<i class="icon-avatar" ></i>',
                e += '<div class="nickname"></div>',
                e += '<div class="giftname"></div>',
                e += '<i class="icon-gift" ></i>',
                e += '<div class="giftNum"></div>',
                e += "</div>",
                e += '<div class="hjPopGift hjPopGift_small last">',
                e += '<i class="icon-avatar"></i>',
                e += '<div class="nickname"></div>',
                e += '<div class="giftname"></div>',
                e += '<i class="icon-gift"></i>',
                e += '<div class="giftNum"></div>',
                e += "</div>",
                e += '<div class="hjPopGift hjPopGift_big">',
                e += '<div class="giftMsg"></div>',
                e += "<img/>",
                e += "</div>",
                e
            }
            function I() {
                i.fadeToggle(300)
            }
           
            var e = {},
            t = ["EFBA35"],
            n = {},
            r = null,
            i = null,
            s = null,
            o = null,
            u = null,
            a = null,
            f = null,
            l = null,
            c = {
                showContainer: null
            },
            h = 0,
        
            w = function(e) {
							
            var t = parseInt(e.uid, 10),
                n = q(e.uname),
                r = q(e.uhead),
                i = q(e.ct.giftid),
                s = q(e.ct.giftname),
                o = q(e.ct.gifticon),
                u = q(e.ct.swf),
                ut = q(e.ct.swftime),
                g = q(e.ct.type),
                a = (g=='0'? 1: 0),
                f = (g==0 ? 0:1) ,
                l = e.ct.giftid+'_'+e.uid,
                c = 1,
                h = {};
								 a && a == 1 ? (
										G[l]? G[l]=G[l]+1 : G[l]=1,
										c=G[l]
								 ) : '';

                f && f == 1 ? (h = {
                    id: t,
                    name: n,
                    avatar: r,
                    giftType: 2,
                    giftId: i,
                    giftName: s,
                    img: u,
                    imgt: ut
                },
                _(h)) : a && a == 1 ? (h = {
                    id: t,
                    name: n,
                    avatar: r,
                    giftType: 1,
                    giftId: i,
                    giftName: s,
                    img: o,
                    repeatId: l,
                    repeatNum: c
                },
                A(h)) : h = null
            },
            E = null,
            S = 1,
            x = [],
            T = {},
            G = {},
            N = 500,
            C = null,
            k = null,
            O = [],
            M = null;
            q = function() {
                var e = {
                    "<": "&#60;",
                    ">": "&#62;",
                    '"': "&#34;",
                    "'": "&#39;",
                    "&": "&#38;"
                },
                t = /&(?![\w#]+;)|[<>"']/g,
                n = function(t) {
                    return e[t]
                };
                return function(e) {
                    return String(e || "").replace(t, n)
                }
            } ();
            return !
            function() {
                i = $(".hjPopbox"),
                i.append(j()),
                //r = i.parent(".player-main"),
                r = i.parent(".gift_effect_area"),
                s = i.find(".hj_showArea"),
                o = i.find(".hj_showArea ul"),
                u = i.find(".hj_inputArea"),
                a = i.find(".hj_tip"),
				r.append(F())
            } (),
            e.gift = w,
            window.HJ_PopBox = e,
            e
        } ()
    },
    {}],
},
{},
[2]); 
