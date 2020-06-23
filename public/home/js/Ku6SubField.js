/*
 * auther : 辰逸
 * QQ:979314
 * Download by http://down.liehuo.net
 * MSN:zl8762385@hotmail.com
 * 2011.04.26
 * 分栏移动（上下）（左右）
 * *************************************************************调用方法
** 			$('#center').KSubfield({
**				_axes : 'y',//y = pageY, y=page=Y
**				_axesElement : '.top,.bottom' //上下 DIV元素
**			});
**			$('#center1').KSubfield({
**				_axes : 'x',//x = pageX, y=page=Y
**				_axesElement : '.left1,.right1' //左右 DIV元素
**			});
 * ********************* Close Tip
 * $(document).trigger('close.Ku6ShowTip')
 * */
(function ($) {
	$.fn.KSubfield = function (options) {
		var defaults = {
			_axes : 'y',//x = pageX, y=page=Y
			_axesElement : '.top,.bottom', //上下 左右 DIV元素
			_topHeight : 0,
			_bottomHeight: 0
		}

		var s = $.extend({}, defaults, options || {});

		function mouseHandler(e) {
			eventThat = e;//event 事件 提供子方法获取相关数据
			that = $(this);

			if (s._axes == 'x') {
				var parentPage = Element_PageX(); 
			} else {
				var parentPage = Element_PageY(); 
			}

			$(document).bind('mousemove', function (eMove) {
				if (s._axes == 'x') {
					var divPos = eMove.pageX - parentPage.axes;

					var divLeft = parentPage.axesLeft + divPos;
					var divRight = parentPage.axesRight - divPos;
					ElementControl({divA:divLeft, divB:divRight}, parentPage);
				} else {
					var divPos = eMove.pageY - parentPage.axes;

					var divTop = parentPage.axesTop + divPos;
					var divBottom = parentPage.axesBottom - divPos;
					ElementControl({divA:divTop, divB:divBottom}, parentPage);
				}
			})
				
			$(document).bind('mouseup', function (e) {
				reMoveBind();
			});

			return false;
		}

		//bind event
		return this.bind('mousedown', mouseHandler);
		
		//控制元素 X
		function ElementControl_X () {

		}

		//控制元素 Y
		function ElementControl (_thisObj, parentPage) {

			if (_thisObj.divA >= s._topHeight) { // TOP
				setElementWH(parentPage.axesObjA, s._topHeight);
			} else {
				if (_thisObj.divA <= s._bottomHeight) {
					setElementWH(parentPage.axesObjA, s._bottomHeight);
				} else {
					setElementWH(parentPage.axesObjA, _thisObj.divA);
					setCSS(_thisObj.divA);
				}
			}

			if (_thisObj.divB >= s._topHeight) { // BOTTOM
				setElementWH(parentPage.axesObjB, s._topHeight);
			} else {
				if (_thisObj.divB <= s._bottomHeight) {
					setElementWH(parentPage.axesObjB, s._bottomHeight);
				} else {
					setElementWH(parentPage.axesObjB, _thisObj.divB);
				}
			}
		}

		//设置宽高函数
		function setElementWH (_this, _number) {
			if (s._axes == 'x') {
				 _this.width(_number-2);
				//that.css({left:_number});
			} else {
				 _this.height(_number);
				//that.css({top:_number});
			}
		} 

		//设置 触发元素 top left值
		function setCSS (_number) {
			if (s._axes == 'x') {
				that.css({left:_number});
			} else {
				that.css({top:_number});
			}
		}

		// page Y
		function Element_PageY() {
			var PageY   = eventThat.pageY;
			var EleDIV  = s._axesElement.split(",");
			
			return pageObj = {
				axes : PageY,
				axesObjA : $(EleDIV[0]),//TOP
				axesObjB : $(EleDIV[1]),//BOTTOM
				axesTop : $(EleDIV[0]).height(),
				axesBottom : $(EleDIV[1]).height()
			}
		}

		//page X
		function Element_PageX() {
			var PageX   = eventThat.pageX;
			var EleDIV  = s._axesElement.split(',');
			
			return pageObj = {
				axes : PageX,
				axesObjA : $(EleDIV[0]),//Left
				axesObjB : $(EleDIV[1]),//BOTTOM
				axesLeft: $(EleDIV[0]).width(),
				axesRight: $(EleDIV[1]).width()
			}
		}

		//unbind
		function reMoveBind() {
			$(document).unbind('mousemove');
			$(document).unbind('mouseup');	
			$(document).unbind('mousedown');	
			return false;
		}
	}
})(jQuery)
