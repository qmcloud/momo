/* 送礼榜效果 */
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
    1 : [function(e, t, n) { (function() {
            function n() { 
                e.on("mouseenter",
                function(t) {
                    $(this).find(".active .listitem").length > 3 && e.removeClass("hide")
                }).on("mouseleave",
                function(n) {
                    t.scrollTop(0),
                    e.addClass("hide")
                });
                var n = $(".js-toptab .tabitem");
                n.on("click",
                function() {
                    n.removeClass("active"),
                    t.removeClass("active"),
                    $(this).addClass("active"),
                    e.find("." + $(this).data("key")).addClass("active")
                })
            }
            var e = $(".js-topview"),
            t = e.find(".viewitem");
            n(),
            t.perfectScrollbar()
        })()
    },
    {}]
},
{},
[1]);

/* 回复 */
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
    1 : [function(e, t, n) { (function() {
            var e = '<a href="#" class="reply-comment">回复</a>';
            $("body").on("mouseenter", "#chatArea .tt-msg-item-h5",
            function(t) {
                $(this).find(".tt-msg-content-h5").append(e)
            }).on("mouseleave", "#chatArea .tt-msg-item-h5",
            function(e) {
                $(this).find(".reply-comment").remove()
            }),
            
            $("body").on("click", "#chatArea .reply-comment",
            function(e) {
                e.preventDefault(),
                $(".tt-type-msg").attr("disabled") ? $(".tt-unlogin-tip a").trigger("click") : $(".tt-type-msg").val("@" + $(this).closest(".tt-msg-item-h5").data("user-nickname") + " ").focus()
            })
        })()
    },
    {}]
},
{},
[1]); 



