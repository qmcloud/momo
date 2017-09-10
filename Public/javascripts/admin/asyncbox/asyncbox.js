/*
 * AsyncBox jQuery Popup-Plugin v1.5 Beta
 * Copyright 2012, Wuxinxi007
 * Date: 2012-3-13 http://asyncui.com
 */
window.popup=window.asyncbox = {
	//动画效果
	Flash : true,
	//遮挡 select (ie6)
	inFrame : true,
	//初始索引值
	zIndex : 1987,
	//自适应最小宽度
	minWidth : 330,
	//自适应最大宽度
	maxWidth : 700,
	//模态层
	Cover : {
		//透明度
		opacity : 0.7,
		//背景颜色
		background : '#000'//DCE2F1
	},
	//按钮文本
	Language : {
		//action 值 ok
		OK     : '确定(O)',
		//action 值 no
		NO     : '否(N)',
		//action 值 yes
		YES    : '是(Y)',
		//action 值 cancel
		CANCEL : '取消(C)',
		//action 值 close
		CLOSE  : '关闭(C)'
	}
};
(function(a) {
	function c(a) {
		return F.getElementById(a)
	}
	function d() {
		var a = F.body,
		b = F.documentElement;
		return {
			x: Math.max(a.scrollWidth, b.clientWidth),
			y: Math.max(a.scrollHeight, b.clientHeight),
			top: Math.max(b.scrollTop, a.scrollTop),
			left: Math.max(b.scrollLeft, a.scrollLeft),
			width: b.clientWidth,
			height: b.clientHeight
		}
	}
	function e(a) {
		var b = a.style,
		c = "documentElement.scroll";
		Y ? (b.removeExpression("top"), b.removeExpression("left"), b.setExpression("top", "eval(" + c + "Top + " + (a.offsetTop - N.scrollTop) + ') + "px"'), b.setExpression("left", "eval(" + c + "Left + " + (a.offsetLeft - N.scrollLeft) + ') + "px"')) : b.position = "fixed"
	}
	function f() {
		for (var a = 0, b = arguments.length; a < b; a += 1) Q && arguments[a] && Q.appendChild(arguments[a]);
		return Q
	}
	function g(a) {
		var b = [],
		c;
		for (c in a) b.push(c + "=" + a[c]);
		return b.join("&")
	}
	function h(a) {
		if (a.args) {
			var b = "",
			c = "",
			d = document.createElement("a");
			return d.href = a.url,
			c = d.href,
			b = typeof a.args == "string" ? a.args: g(a.args),
			c.indexOf("#") >= 0 && (c = c.substr(0, c.indexOf("#"))),
			c.indexOf("?") >= 0 && (c = c.substr(0, c.indexOf("?"))),
			c + d.search + (d.search ? "&" + b: "?" + b) + d.hash
		}
		return a.url
	}
	function i(b) {
		j(a.extend({
			title: "操作提示",
			content: "",
			top: -1,
			right: -1,
			bottom: -1,
			left: -1,
			width: "auto",
			height: "auto",
			args: !1,
			node: {},
			wrap: !1,
			"float": !1,
			timer: !1,
			ctrlbar: {
				close: !0
			},
			buttons: !1,
			pageMode: !1,
			htmlMode: !1,
			inputMode: !1,
			drag: !0,
			cache: !1,
			fixed: !1,
			reset: !1,
			flash: !1,
			modal: !1,
			scroll: !0,
			onload: a.noop,
			unload: a.noop,
			callback: a.noop
		},
		b))
	}
	function j(b) {
		a(function() {
			if ($) {
				var d = b.id,
				e = c(d);
				m(b);
				if (e) {
					e.style.zIndex = D.zIndex++;
					if (typeof b.content == "object") {
						var f = b.content,
						g = c(b.id + "_content");
						a.each(L,
						function(a, c) {
							c.id == b.id && (c.wrap = {
								key: f,
								value: f.innerHTML
							},
							g.innerHTML = c.wrap.value, f.innerHTML = "")
						})
					}
					a(e).show()
				} else {
					a(Q).append("<div id=" + d + ' class="asyncbox" style="top:-5000px;left:-5000px;z-index:' + D.zIndex+++'">' + n(b) + "</div>"),
					b.abo = a("#" + d)[0];
					var h = 0,
					i, j = b.abo.getElementsByTagName("*"),
					l = j.length;
					for (; h < l; h++) i = j[h].className,
					i && (i = i.split("ab_border ab_")[1] || i.split("ab_")[1], b.node[i] = j[h]);
					if (Y && b.node.lt.currentStyle.png) {
						var e, o, p, q = b.node.outer;
						for (var r = 0; r < 3; r++) for (var u = 0; u < 3; u++) e = q.rows[r].cells[u],
						p = e.currentStyle.png,
						p && (o = e.runtimeStyle, o.backgroundImage = "none", o.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='" + $ + "skins/" + p + "',sizingMethod='scale')")
					}
					v(b),
					t(b),
					a("#" + d).mousedown(function(a) {
						a.which == 1 && (this.style.zIndex = D.zIndex++)
					}),
					s(b),
					b.drag && k(b),
					b.timer && (b.timeobj = window.setTimeout(function() {
						b.timeobj && c(b.id) && D.close(b.id)
					},
					b.timer * 1e3)),
					L.push(b),
					w(b)
				}
			}
		})
	}
	function k(b) {
		function f(a) {
			y && (y = !1, x.display = "block"),
			u = a.clientX - s,
			v = a.clientY - t,
			u < o ? u = o: u > r && (u = r),
			v < n ? v = n: v > p && (v = p),
			v < n && (v = n),
			u < o && (u = o),
			els.top = v + "px",
			els.left = u + "px"
		}
		function g() {
			l(!1),
			b.drag.clone && (D.Flash ? q(i, {
				top: el.offsetTop,
				left: el.offsetLeft,
				fixed: b.fixed
			}) : (j.top = el.offsetTop + "px", j.left = el.offsetLeft + "px"), x.display = "none"),
			Y && b.fixed && e(i),
			X && el.releaseCapture ? (el.releaseCapture(), el.onmousemove = null, el.onmouseup = null) : a(F).unbind("mousemove", f).unbind("mouseup", g)
		}
		var h = b.id,
		i = el = c(h),
		j = els = el.style,
		k,
		m,
		n,
		o,
		p,
		r,
		s,
		t,
		u,
		v,
		n,
		o,
		p,
		r,
		w = T,
		x = w.style,
		y = !1;
		a("#" + h + "_header").css({
			cursor: "move"
		}),
		a("#" + h + "_header").mousedown(function(c) {
			c.which == 1 && c.target.tagName != "A" && (k = d(), l(b, !0), el = i, els = i.style, m = {
				top: el.offsetTop,
				left: el.offsetLeft,
				width: el.offsetWidth,
				height: el.offsetHeight
			},
			b.drag.clone && (!Y && b.fixed && (x.position = "fixed"), x.top = m.top + "px", x.left = m.left + "px", x.width = m.width - 2 + "px", x.height = m.height - 2 + "px", el = w, els = w.style, y = !0), s = c.clientX - m.left, t = c.clientY - m.top, !Y && b.fixed ? (n = 0, o = 0, p = k.height - m.height, r = k.width - m.width) : (n = k.top, o = k.left, p = k.height + n - m.height, r = k.width + o - m.width), X && el.setCapture ? (el.setCapture(), el.onmousemove = function(a) {
				f(a || event)
			},
			el.onmouseup = g) : a(F).bind("mousemove", f).bind("mouseup", g)),
			c.preventDefault()
		})
	}
	function l(a, b) {
		var c = U,
		d = c.style;
		a ? b && (d.display = "block") : d.display = "none"
	}
	function m(a) {
		a.modal && (J.push(D.zIndex), K.push(a.id), D.cover(!0, D.zIndex))
	}
	function n(a) {
		return [D.inFrame && Y || X && a.float ? '<iframe frameborder="0" src="about:blank" style="position:absolute;top:0px;left:0px;z-index:-1;width:100%;height:100%;_width:expression(this.parentNode.offsetWidth);_height:expression(this.parentNode.offsetHeight);opacity:0;filter:alpha(opacity=0)"></iframe>': "", '<table class="ab_outer" border="0" cellspacing="0" cellpadding="0">', "<tbody>", "<tr>", '<td class="ab_border ab_lt"></td>', '<td class="ab_border ab_t"></td>', '<td class="ab_border ab_rt"></td>', "</tr>", "<tr>", '<td class="ab_border ab_l"><div></div></td>', '<td valign="top" class="ab_c">', a.title ? '<div class="ab_title" id="' + a.id + '_header">' + a.title + (a.ctrlbar.close ? '<a id="' + a.id + '_close" class="ab_close" href="javascript:void(0)" title="' + D.Language.CLOSE + '"></a>': "") + "&nbsp;</div>": "", a.pageMode ? "": a.inputMode ? '<div class="' + G + 'prompt">' + "<ul>" + "<li>" + a.inputMode.tips + "</li>" + "<li>" + (a.textType == "text" ? '<input type="text" id="' + a.id + '_Text" value="' + a.inputMode.content + '" size="60" />': "") + (a.textType == "textarea" ? '<textarea cols="60" rows="10" id="' + a.id + '_Text">' + a.inputMode.content + "</textarea>": "") + (a.textType == "password" ? '<input type="password" id="' + a.id + '_Text" value="' + a.inputMode.content + '" size="40" />': "") + "</li>" + "</ul>" + "</div>": a.htmlMode ? '<div id="' + a.id + '_content" style="overflow:' + (a.scroll ? "auto": "hidden") + '">' + o(a) + "</div>": '<div id="' + a.id + '_content" style="overflow:hidden;overflow-y:auto"><div class="' + a.icon + '"><span></span>' + a.content + "</div></div>", a.pageMode ? '<iframe marginWidth="0" marginHeight="0" frameborder="0" id="' + a.id + '_content" name="' + a.id + '_content" width="100%" src="' + h(a) + '" scrolling="' + (a.scroll ? "auto": "no") + '"></iframe>': "", a.buttons ? '<div class="ab_footbar" id="' + a.id + '_btnsbar">' + r(a) + "</div>": "", "</td>", '<td class="ab_border ab_r"><div></div></td>', "</tr>", "<tr>", '<td class="ab_border ab_lb"></td>', '<td class="ab_border ab_b"></td>', '<td class="ab_border ab_rb"></td>', "</tr>", "</tbody>", "</table>"].join("")
	}
	function o(a) {
		var b = a.content;
		return typeof b == "object" && b ? (a.wrap = {
			key: b,
			value: b.innerHTML
		},
		b.innerHTML = "", a.wrap.value) : b
	}
	function p(a) {
		a.wrap && (a.wrap.key.innerHTML = a.wrap.value, a.wrap = !1)
	}
	function q(b, c) {
		a(b).animate(c, 300,
		function() {
			Y && c.fixed && e(b)
		})
	}
	function r(b) {
		if (b.buttons) {
			var c = [];
			return a.each(b.buttons,
			function(a, d) {
				c.push('<a id="', b.id, "_", d.result, '" href="javascript:void(0)"><span>&nbsp;', d.value, "&nbsp;</span></a>")
			}),
			c.join("")
		}
	}
	function s(b) {
		b.inputMode ? a("#" + b.id + "_Text").focus().select() : b.buttons && a("#" + b.id + "_btnsbar").find("a")[0].focus()
	}
	function t(b) {
		var d, e = D.btn.CLOSE.concat(b.buttons);
		a.each(e,
		function(e, f) {
			a("#" + b.id + "_" + f.result).click(function(e) {
				var g = a(this);
				return g.attr("disabled", "disabled"),
				b.inputMode ? d = b.callback(f.result, c(b.id + "_Text").value) : b.pageMode ? D.opener(b.id) ? d = b.callback(f.result, D.opener(b.id), b.abo.returnValue) : d = !0 : b.htmlMode ? d = b.callback(f.result, b.abo.returnValue) : d = b.callback(f.result),
				(typeof d == "undefined" || d) && D.close(b.id),
				g.removeAttr("disabled"),
				e.preventDefault(),
				!1
			})
		})
	}
	function u(a) {
		var b = c(a + "_content");
		if (b) {
			try {
				var d = E.contentWindow.document;
				d.write(""),
				d.clear(),
				d.close()
			} catch(e) {}
			b.src = "about:blank"
		}
	}
	function v(a) {
		x(a),
		z(a)
	}
	function w(b) {
		if (b.pageMode) {
			var c = a("#" + b.id + "_content"),
			d = c[0],
			e = b.abo;
			e.options = b,
			e.data = b.data,
			e.opener = b.opener || window,
			e.close = function() {
				D.close(this.id)
			},
			e.join = function(a) {
				return D.opener(a)
			},
			e.buttons = b.buttons ? a("#" + b.id + "_btnsbar").find("a") : "undefined",
			d.api = e,
			c.one("load",
			function() {
				try {
					if (b.width == "auto" && b.height == "auto") {
						var c = a(this).contents();
						asyncbox.resizeTo(b.id, c.width(), c.height())
					}
					b.onload(d.contentWindow)
				} catch(e) {}
			})
		} else b.htmlMode && b.onload()
	}
	function x(b) {
		var e = b.id,
		f = c(e),
		g = f.style,
		h = d(),
		i = a("#" + e + "_content");
		b.pageMode || b.htmlMode ? (typeof b.width == "number" && i.width(b.width), typeof b.height == "number" && i.height(b.height), typeof b.width == "string" && b.width != "auto" && i.width(parseInt(b.width) - f.offsetWidth + i[0].offsetWidth), typeof b.height == "string" && b.height != "auto" && i.height(parseInt(b.height) - f.offsetHeight + i[0].offsetHeight)) : f.offsetWidth < D.minWidth && !b.inputMode ? i.width(D.minWidth - f.offsetWidth + i.outerWidth()) : f.offsetWidth > D.maxWidth && i.width(D.maxWidth - f.offsetWidth + i.outerWidth())
	}
	function y(a, b) {
		var c = a.abo,
		f = c.style,
		g, h, i, j = d(),
		k = c.offsetWidth,
		l = c.offsetHeight,
		m = j.top,
		n = j.left,
		o = j.width,
		p = j.height;
		a.center ? i = (p - l) / 2 : i = l > p / 2 ? (p - l) / 2 : p * .382 - l / 2,
		!Y && a.fixed ? (g = i, h = (o - k) / 2, a.top >= 0 && (g = a.top), a.left >= 0 && (h = a.left), a.right >= 0 && (h = o - k - a.right), a.bottom >= 0 && (g = p - l - a.bottom), g = g < 0 ? 0 : g, h = h < 0 ? 0 : h) : (g = m + i, h = n + (o - k) / 2, a.top >= 0 && (g = m + a.top), a.left >= 0 && (h = n + a.left), a.right >= 0 && (h = n + o - k - a.right), a.bottom >= 0 && (g = m + p - l - a.bottom), g = g < m ? m: g, h = h < n ? n: h),
		b ? q(c, {
			top: g,
			left: h
		}) : (f.top = g + "px", f.left = h + "px", a.fixed && e(c))
	}
	function z(a) {
		y(a, null, !1),
		a.reset && A(a)
	}
	function A(b) {
		var c = new Object;
		c.id = b.id,
		c.top = b.top,
		c.right = b.right,
		c.bottom = b.bottom,
		c.left = b.left,
		c.flash = b.flash,
		M.push(c),
		M.length > 0 && !H && (a(E).bind("resize", bb), H = !0)
	}
	function B(b, c, d) {
		return a.grep(b,
		function(a) {
			return d ? a[d] != c: a != c
		})
	}
	function C(a, b, c, d) {
		var e = G + d,
		f = {
			id: e,
			icon: e,
			title: b,
			reset: !0,
			modal: !0,
			content: a,
			callback: c
		};
		if (d == "alert" || "success" || "error") f.buttons = D.btn.OK;
		switch (d) {
		case "confirm":
			f.buttons = D.btn.OKCANCEL;
			break;
		case "warning":
			f.buttons = D.btn.YESNOCANCEL
		}
		i(f)
	}
	var D = asyncbox,
	E = window,
	F = document,
	G = "asyncbox_",
	H = !1,
	I = !1,
	J = [],
	K = [],
	L = [],
	M = [],
	N = F.documentElement,
	O,
	P = document.createElement("link"),
	Q = document.createElement("licai"),
	R = document.createElement("ab_skins"),
	S = document.createElement("ab_modal"),
	T = document.createElement("ab_clone"),
	U = document.createElement("ab_cover"),
	V = N.clientWidth,
	W = N.clientHeight,
	X = !!E.ActiveXObject,
	Y = X && !E.XMLHttpRequest,
	Z,
	$ = function(a, b, c, d) {
		c = a.length;
		for (; b < c; b++) {
			d = document.querySelector ? a[b].src: a[b].getAttribute("src", 4);
			if (d.substr(d.lastIndexOf("/")).indexOf("asyncbox") >= 0) break
		}
		return d = d.split("?"),
		Z = d[1],
		d[0].substr(0, d[0].lastIndexOf("/") + 1) || !1
	} (document.getElementsByTagName("script"), 0),
	_ = function(a) {
		if (Z) {
			var b = Z.split("&"),
			c = 0,
			d = b.length,
			e;
			for (; c < d; c++) {
				e = b[c].split("=");
				if (a === e[0]) return e[1]
			}
		}
		return null
	},
	ab = _("skin") || "default";
	if (document.compatMode == "CSS1Compat" && $) {
		var ba = "expression(documentElement.",
		cb = ["position:", Y ? "absolute;": "fixed;", Y ? "top:" + ba + "scrollTop);": "top:0px;", Y ? "left:" + ba + "scrollLeft);": "left:0px;", Y ? "width:" + ba + "clientWidth);": "width:100%;", Y ? "height:" + ba + "clientHeight);": "height:100%;", "background:" + D.Cover.background + ";opacity:" + D.Cover.opacity + ";filter:alpha(opacity=" + D.Cover.opacity * 100 + ");", "display:none;overflow:hidden;"].join("");
		Q.setAttribute("version", "1.5"),
		Q.setAttribute("url", "http://blog.51edm.org"),
		R.innerHTML = "<ul><li><a><b></b></a></li></ul>",
		R.style.cssText = "position:absolute;top:-5000px;left:-5000px",
		S.style.cssText = cb,
		S.setAttribute("unselectable", "on"),
		S.onmousedown = function() {
			return ! 1
		},
		T.style.cssText = "position:absolute;z-index:" + (D.zIndex + 900) + ";display:none",
		U.style.cssText = cb + "cursor:move;background:#fff;opacity:0;filter:alpha(opacity=0);z-index:" + (D.zIndex + 1e3),
		a(function() {
			if (!document.getElementsByTagName("frameset").length) {
				try {
					Y && document.execCommand("BackgroundImageCache", !1, !0)
				} catch(a) {}
				if (Y && document.body.currentStyle["backgroundAttachment"] != "fixed") {
					var b = document.getElementsByTagName("html")[0];
					b.style.cssText = "background-image:url(about:blank);background-attachment:fixed"
				}
				document.body.appendChild(f(R, S, T, U)),
				D.inFrame && Y && (S.innerHTML = '<div unselectable="on" style="width:100%;height:100%"><iframe width="100%" height="100%" src="about:blank" style="position:absolute;z-index:-1;opacity:0;filter:alpha(opacity=0)"></iframe></div>'),
				Y && e(R)
			}
		})
	}
	var bb = function() {
		if (V != N.clientWidth || W != N.clientHeight) V = N.clientWidth,
		W = N.clientHeight,
		a.each(M,
		function(a) {
			var b = {},
			d = M[a];
			b.abo = c(d.id),
			b.id = d.id,
			b.top = d.top,
			b.left = d.left,
			b.right = d.right,
			b.bottom = d.bottom,
			D.Flash && d.flash ? y(b, !0) : y(b, !1)
		})
	};
	D.btn = {
		OK: [{
			value: D.Language.OK,
			result: "ok"
		}],
		NO: [{
			value: D.Language.NO,
			result: "no"
		}],
		YES: [{
			value: D.Language.YES,
			result: "yes"
		}],
		CLOSE: [{
			title: D.Language.CLOSE,
			result: "close"
		}],
		CANCEL: [{
			value: D.Language.CANCEL,
			result: "cancel"
		}]
	},
	D.btn.OKCANCEL = D.btn.OK.concat(D.btn.CANCEL),
	D.btn.YESNO = D.btn.YES.concat(D.btn.NO),
	D.btn.YESNOCANCEL = D.btn.YES.concat(D.btn.NO).concat(D.btn.CANCEL),
	D.cover = function(b, c) {
		var d = a(S),
		e = S.style;
		b ? (I = b, e.zIndex = c || D.zIndex, D.Flash ? d.fadeTo(500, D.Cover.opacity) : d.show()) : (I = b, D.Flash ? d.fadeOut(300) : d.hide(), J = [])
	},
	D.close = function(d) {
		var e = c(d);
		e && (M.length > 0 && (M = B(M, d, "id")), H && M.length == 0 && (a(E).unbind("resize", bb), H = !1, M = []), a.each(L,
		function(f, g) {
			if (g.id == d) {
				if (I) for (b in K) K[b] == d && (K = B(K, d), J.length > 1 && K.length != 0 ? (J.pop(), D.cover(!0, J[J.length - 1])) : D.cover(!1));
				g.wrap && (c(g.id + "_content").innerHTML = "", p(g)),
				g.timeobj = null,
				g.cache ? a(e).hide() : (L.length > 0 && (L = B(L, d, "id")), g.pageMode && u(d), a(e).remove()),
				g.unload()
			}
		}))
	},
	D.resizeTo = function(b, d, f) {
		var g = c(b);
		(g && g.offsetWidth != d || g.offsetHeight != f) && a.each(L,
		function(a, c) {
			if (c.id == b) {
				var h = {
					abo: g,
					id: b,
					width: d,
					height: f,
					top: c.top,
					left: c.left,
					right: c.right,
					bottom: c.bottom,
					pageMode: c.pageMode,
					htmlMode: c.htmlMode
				};
				x(h),
				y(h, !1),
				c.fixed && e(g)
			}
		})
	},
	D.framer = function(a) {
		return c(a).contentWindow
	},
	D.opener = function(a) {
		return D.framer(a + "_content")
	},
	D.reload = function(a, b) {
		var d = c(a + "_content");
		try {
			d.src = b || D.opener(a).location.href
		} catch(e) {
			d.src = d.src
		}
	},
	D.exist = function(a) {
		var b = c(a);
		return b && b.style.display != "none" ? !0 : !1
	},
	D.alert = function(a, b, c) {
		C(a, b, c, "alert")
	},
	D.confirm = function(a, b, c) {
		C(a, b, c, "confirm")
	},
	D.prompt = function(a, b, c, d, e) {
		var f = {
			id: G + "prompt",
			title: a,
			reset: !0,
			modal: !0,
			inputMode: {
				tips: b || "",
				content: c || ""
			},
			textType: d,
			buttons: D.btn.OKCANCEL,
			callback: e
		};
		i(f)
	},
	D.open = function(a, b) {
		a.id = a.id || G + D.zIndex,
		a.content = "",
		a.pageMode = !0,
		a.opener = b,
		i(a)
	},
	D.html = function(a) {
		a.id = a.id || G + D.zIndex,
		a.url = "",
		a.htmlMode = !0,
		i(a)
	},
	D.success = function(a, b, c) {
		C(a, b, c, "success")
	},
	D.warning = function(a, b, c) {
		C(a, b, c, "warning")
	},
	D.error = function(a, b, c) {
		C(a, b, c, "error")
	}
})(jQuery);