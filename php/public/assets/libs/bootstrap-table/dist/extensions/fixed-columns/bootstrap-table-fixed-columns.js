'use strict';

(function ($) {
    'use strict';
    // Reasonable defaults

    var PIXEL_STEP = 10;
    var LINE_HEIGHT = 40;
    var PAGE_HEIGHT = 800;

    function normalizeWheel(event) {
        var sX = 0; // spinX
        var sY = 0; // spinY
        var pX = 0; // pixelX
        var pY = 0; // pixelY

        // Legacy
        if ('detail' in event) {
            sY = event.detail;
        }
        if ('wheelDelta' in event) {
            sY = -event.wheelDelta / 120;
        }
        if ('wheelDeltaY' in event) {
            sY = -event.wheelDeltaY / 120;
        }
        if ('wheelDeltaX' in event) {
            sX = -event.wheelDeltaX / 120;
        }

        // side scrolling on FF with DOMMouseScroll
        if ('axis' in event && event.axis === event.HORIZONTAL_AXIS) {
            sX = sY;
            sY = 0;
        }

        pX = sX * PIXEL_STEP;
        pY = sY * PIXEL_STEP;

        if ('deltaY' in event) {
            pY = event.deltaY;
        }
        if ('deltaX' in event) {
            pX = event.deltaX;
        }

        if ((pX || pY) && event.deltaMode) {
            if (event.deltaMode === 1) {
                // delta in LINE units
                pX *= LINE_HEIGHT;
                pY *= LINE_HEIGHT;
            } else {
                // delta in PAGE units
                pX *= PAGE_HEIGHT;
                pY *= PAGE_HEIGHT;
            }
        }

        // Fall-back if spin cannot be determined
        if (pX && !sX) {
            sX = pX < 1 ? -1 : 1;
        }
        if (pY && !sY) {
            sY = pY < 1 ? -1 : 1;
        }

        return {
            spinX: sX,
            spinY: sY,
            pixelX: pX,
            pixelY: pY
        };
    }

    var cachedWidth = null;
    var getScrollBarWidth = function getScrollBarWidth() {
        if (cachedWidth === null) {
            var inner = $('<p/>').addClass('fixed-table-scroll-inner'),
                outer = $('<div/>').addClass('fixed-table-scroll-outer'),
                w1 = void 0,
                w2 = void 0;
            outer.append(inner);
            $('body').append(outer);
            w1 = inner[0].offsetWidth;
            outer.css('overflow', 'scroll');
            w2 = inner[0].offsetWidth;

            if (w1 === w2) {
                w2 = outer[0].clientWidth;
            }

            outer.remove();
            cachedWidth = w1 - w2;
        }
        return cachedWidth;
    };
    //获取原本表格体的滑块宽度
    var getTableBodyScrollBarWidth = function getTableBodyScrollBarWidth(tableBody) {
        return tableBody[0].scrollHeight > tableBody[0].clientHeight ? 15 : 0;
    };
    $.extend($.fn.bootstrapTable.defaults, {
        fixedColumns: false,
        fixedNumber: 0,
        fixedRightNumber: 0
    });
    var BootstrapTable = $.fn.bootstrapTable.Constructor,
        _initBody = BootstrapTable.prototype.initBody,
        _initContainer = BootstrapTable.prototype.initContainer,
        _trigger = BootstrapTable.prototype.trigger,
        _hideLoading = BootstrapTable.prototype.hideLoading,
        _updateSelected = BootstrapTable.prototype.updateSelected;

    BootstrapTable.prototype.fixedColumnsSupported = function () {
        var that = this;
        return that.options.fixedColumns && !that.options.detailView && !that.options.cardView;
    };
    BootstrapTable.prototype.initFixedContainer = function () {
        if (!this.fixedColumnsSupported()) {
            return;
        }

        if (this.options.fixedNumber) {
            this.$tableContainer.find('.fixed-columns').length == 0 && this.$tableContainer.append('<div class="fixed-columns"></div>');
            this.$fixedColumns = this.$tableContainer.find('.fixed-columns');
        }

        if (this.options.fixedRightNumber) {
            this.$tableContainer.find('.fixed-columns-right').length == 0 && this.$tableContainer.append('<div class="fixed-columns-right"></div>');
            this.$fixedColumnsRight = this.$tableContainer.find('.fixed-columns-right');
        }
    };

    BootstrapTable.prototype.initContainer = function () {
        _initContainer.apply(this, Array.prototype.slice.apply(arguments));
        this.initFixedContainer();
    };

    BootstrapTable.prototype.initBody = function () {
        _initBody.apply(this, Array.prototype.slice.apply(arguments));
        if (!this.fixedColumnsSupported()) {
            return;
        }

        if (this.options.showHeader && this.options.height) {
            return;
        }

        this.initFixedColumnsBody();
        this.initFixedColumnsEvents();
    };

    BootstrapTable.prototype.trigger = function () {
        var that = this;

        _trigger.apply(this, Array.prototype.slice.apply(arguments));
        if (arguments[0] === 'pre-body') {
            //如果上来就是cardView 设置表格高度为auto
            if (this.options.cardView) {
                this.$tableBody.css("height", "auto");
            }
        }
        //监听cardView 显示/隐藏fixed部分
        if (arguments[0] === 'toggle') {
            if (arguments[1]) {
                this.$tableBody.css("height", "auto");
                this.$fixedColumns && this.$fixedColumns.hide();
                this.$fixedColumnsRight && this.$fixedColumnsRight.hide();
            } else {
                this.$tableBody.css("height", "100%");
                this.$fixedColumns && this.$fixedColumns.show();
                this.$fixedColumnsRight && this.$fixedColumnsRight.show();
                this.$fixedHeaderRight && this.$fixedHeaderRight.scrollLeft(this.$tableBody.find('table').width());
                this.$fixedBodyRight && this.$fixedBodyRight.scrollLeft(this.$tableBody.find('table').width());
            }
        }
        if (!that.fixedColumnsSupported()) {
            return;
        }
        if (arguments[0] === 'post-header') {
            this.initFixedColumnsHeader();
        } else if (arguments[0] === 'scroll-body') {
            if (this.needFixedColumns && this.options.fixedNumber) {
                this.$fixedBody && this.$fixedBody.scrollTop(this.$tableBody.scrollTop());
            }

            if (this.needFixedColumns && this.options.fixedRightNumber) {
                this.$fixedBodyRight && this.$fixedBodyRight.scrollTop(this.$tableBody.scrollTop());
            }
        } else if (arguments[0] === 'load-success') {
            this.hideLoading();
        }
    };

    BootstrapTable.prototype.updateSelected = function () {
        var that = this;

        _updateSelected.apply(this, Array.prototype.slice.apply(arguments));
        if (!this.fixedColumnsSupported()) {
            return;
        }
        this.$tableBody.find('tr').each(function (i, el) {
            var $el = $(el);
            var index = $el.data('index');
            var classes = $el.attr('class');
            var inputSelector = '[name="' + that.options.selectItemName + '"]';
            var $input = $el.find(inputSelector);
            if (typeof index === 'undefined') {
                return;
            }

            var updateFixedBody = function updateFixedBody($fixedHeader, $fixedBody) {
                var $tr = $fixedBody.find('tr[data-index="' + index + '"]');
                $tr.attr('class', classes);

                if ($input.length) {
                    $tr.find(inputSelector).prop('checked', $input.prop('checked'));
                }
                if (that.$selectAll.length) {
                    $fixedHeader.add($fixedBody).find('[name="btSelectAll"]').prop('checked', that.$selectAll.prop('checked'));
                }
            };
            if (that.$fixedBody && that.options.fixedNumber) {
                updateFixedBody(that.$fixedHeader, that.$fixedBody);
            }

            if (that.$fixedBodyRight && that.options.fixedRightNumber) {
                updateFixedBody(that.$fixedHeaderRight, that.$fixedBodyRight);
            }
        });
    };

    BootstrapTable.prototype.hideLoading = function () {
        _hideLoading.apply(this, Array.prototype.slice.apply(arguments));
        if (this.needFixedColumns && this.options.fixedNumber) {
            this.$fixedColumns.find('.fixed-table-loading').hide();
        }

        if (this.needFixedColumns && this.options.fixedRightNumber) {
            this.$fixedColumnsRight.find('.fixed-table-loading').hide();
        }
    };

    BootstrapTable.prototype.initFixedColumnsHeader = function () {
        var that = this;

        if (this.options.height) {
            this.needFixedColumns = this.$tableHeader.outerWidth(true) < this.$tableHeader.find('table').outerWidth(true);
        } else {
            this.needFixedColumns = this.$tableBody.outerWidth(true) < this.$tableBody.find('table').outerWidth(true);
        }

        var initFixedHeader = function initFixedHeader($fixedColumns, isRight) {
            $fixedColumns.find('.fixed-table-header').remove();
            $fixedColumns.append(that.$tableHeader.clone(true));
            $fixedColumns.find('.fixed-table-header').css('margin-right', "");
            $fixedColumns.css({
                width: that.getFixedColumnsWidth(isRight)
            });
            return $fixedColumns.find('.fixed-table-header');
        };

        if (this.needFixedColumns && this.options.fixedNumber) {
            this.$fixedHeader = initFixedHeader(this.$fixedColumns);
            this.$fixedHeader.css('margin-right', '');
        } else if (this.$fixedColumns) {
            this.$fixedColumns.html('').css('width', '');
        }

        if (this.needFixedColumns && this.options.fixedRightNumber) {
            this.$fixedHeaderRight = initFixedHeader(this.$fixedColumnsRight, true);
            this.$fixedHeaderRight.scrollLeft(this.$fixedHeaderRight.find('table').width());
        } else if (this.$fixedColumnsRight) {
            this.$fixedColumnsRight.html('').css('width', '');
        }

        this.initFixedColumnsBody();
        this.initFixedColumnsEvents();
    };

    BootstrapTable.prototype.initFixedColumnsBody = function () {
        var that = this;

        var initFixedBody = function initFixedBody($fixedColumns, $fixedHeader) {
            $fixedColumns.find('.fixed-table-body').remove();
            $fixedColumns.append(that.$tableBody.clone(true));

            var $fixedBody = $fixedColumns.find('.fixed-table-body');

            var tableBody = that.$tableBody.get(0);
            var scrollHeight = tableBody.scrollWidth > tableBody.clientWidth ? getScrollBarWidth() : 0;
            var paginationHeight = $(".fixed-table-pagination", that.$tableContainer).height();
            if (typeof that.options.height !== 'undefined') paginationHeight = 0;
            var height = that.$tableContainer.outerHeight(true) - scrollHeight - paginationHeight + 1;
            $fixedColumns.css({
                height: height,
                "min-height": "calc(100% - " + (paginationHeight + scrollHeight) + "px)"
            });
            $fixedBody.css({
                height: height - $fixedHeader.height(),
                "min-height": "calc(100% - " + $fixedHeader.height() + "px)",
                overflow: "hidden"
            });

            return $fixedBody;
        };

        if (this.needFixedColumns && this.options.fixedNumber) {
            this.$fixedBody = initFixedBody(this.$fixedColumns, this.$fixedHeader);
        }
        if (this.needFixedColumns && this.options.fixedRightNumber) {
            this.$fixedBodyRight = initFixedBody(this.$fixedColumnsRight, this.$fixedHeaderRight);
            this.$fixedBodyRight.scrollLeft(this.$fixedBodyRight.find('table').width());
            this.$fixedBodyRight.css('overflow-y', 'hidden');
        }
    };

    BootstrapTable.prototype.getFixedColumnsWidth = function (isRight) {

        var visibleFields = this.getVisibleFields();
        var width = 0;
        var fixedNumber = this.options.fixedNumber;
        var marginRight = 0;

        if (isRight) {
            visibleFields = visibleFields.reverse();
            fixedNumber = this.options.fixedRightNumber;
            //右侧固定列距离
            this.$fixedColumnsRight.css('right', getTableBodyScrollBarWidth(this.$tableBody));
        }

        for (var i = 0; i < fixedNumber; i++) {
            width += this.$header.find('th[data-field="' + visibleFields[i] + '"]').outerWidth();
        }

        return width + 1;
    };

    BootstrapTable.prototype.initFixedColumnsEvents = function () {
        var that = this;

        var toggleHover = function toggleHover(e, toggle) {
            var tr = 'tr[data-index="' + $(e.currentTarget).data('index') + '"]';
            var $trs = that.$tableBody.find(tr);

            if (that.$fixedBody) {
                $trs = $trs.add(that.$fixedBody.find(tr));
            }
            if (that.$fixedBodyRight) {
                $trs = $trs.add(that.$fixedBodyRight.find(tr));
            }

            $trs.css('background-color', toggle ? $(e.currentTarget).css('background-color') : '');
        };
        this.$tableBody.find('tr').hover(function (e) {
            toggleHover(e, true);
        }, function (e) {
            toggleHover(e, false);
        });
        var isFirefox = typeof navigator !== 'undefined' && navigator.userAgent.toLowerCase().indexOf('firefox') > -1;
        var mousewheel = isFirefox ? 'DOMMouseScroll' : 'mousewheel';
        var updateScroll = function updateScroll(e, fixedBody) {
            var normalized = normalizeWheel(e);
            var deltaY = Math.ceil(normalized.pixelY);
            var top = that.$tableBody.scrollTop() + deltaY;
            if (deltaY < 0 && top > 0 || deltaY > 0 && top < fixedBody.scrollHeight - fixedBody.clientHeight) {
                e.preventDefault();
            }

            that.$tableBody.scrollTop(top);
            if (that.$fixedBody) {
                that.$fixedBody.scrollTop(top);
            }
            if (that.$fixedBodyRight) {
                that.$fixedBodyRight.scrollTop(top);
            }
        };
        if (this.needFixedColumns && this.options.fixedNumber) {
            this.$fixedBody.find('tr').hover(function (e) {
                toggleHover(e, true);
            }, function (e) {
                toggleHover(e, false);
            });
            this.$fixedBody[0].addEventListener(mousewheel, function (e) {
                //给鼠标滑轮绑定事件
                updateScroll(e, that.$fixedBody[0]);
            });
            //给固定表格的checkbox绑定事件
            this.$fixedBody.find('input[name="' + this.options.selectItemName + '"]').off("click").on('click', function (e) {
                e.stopImmediatePropagation();
                var index = $(e.target).data("index");
                $(that.$selectItem[index]).trigger("click");
            });
            //绑定TD点击事件
            this.$fixedBody.find('> table > tbody > tr[data-index] > td').off('click dblclick').on('click dblclick', function (e) {
                var index = $(this).closest("tr[data-index]").data("index");
                $(that.$selectItem[index]).closest("tr[data-index]").find(">td:eq(" + $(this).index() + ")").trigger("click");
            });
        }
        //给原本表格绑定scroll事件
        $('div.fixed-table-body').off('scroll'); //给所有的body解绑 scroll
        this.$tableBody.off('scroll').on('scroll', function (e) {
            that.$tableHeader.scrollLeft(0);
            if (that.$tableBody.scrollLeft() > 0) {
                that.$tableHeader.scrollLeft(that.$tableBody.scrollLeft());
                if (that.options.showFooter && !that.options.cardView) {
                    that.$tableFooter.scrollLeft(that.$tableBody.scrollLeft());
                }
            }
            var top = that.$tableBody.scrollTop();
            if (that.$fixedBody) {
                that.$fixedBody.scrollTop(top);
            }
            if (that.$fixedBodyRight) {
                that.$fixedBodyRight.scrollTop(top);
            }
        });

        if (this.needFixedColumns && this.options.fixedRightNumber) {
            this.$fixedBodyRight.find('tr').hover(function (e) {
                toggleHover(e, true);
            }, function (e) {
                toggleHover(e, false);
            });
            this.$fixedBodyRight[0].addEventListener(mousewheel, function (e) {
                //给鼠标滑轮绑定事件
                updateScroll(e, that.$fixedBodyRight[0]);
            });
            //给固定表格的checkbox绑定事件
            this.$fixedBodyRight.find('input[name="' + this.options.selectItemName + '"]').off("click").on('click', function (e) {
                e.stopImmediatePropagation();
                var index = $(e.target).data("index");
                $(that.$selectItem[index]).trigger("click");
            });
            //绑定TD点击事件
            this.$fixedBodyRight.find('> table > tbody > tr[data-index] > td').off('click dblclick').on('click dblclick', function (e) {
                var index = $(this).closest("tr[data-index]").data("index");
                $(that.$selectItem[index]).closest("tr[data-index]").find(">td:eq(" + $(this).index() + ")").trigger("click");
            });
        }

        if (this.options.filterControl) {
            $(this.$fixedColumns).off('keyup change').on('keyup change', function (e) {
                var $target = $(e.target);
                var value = $target.val();
                var field = $target.parents('th').data('field');
                var $coreTh = that.$header.find('th[data-field="' + field + '"]');

                if ($target.is('input')) {
                    $coreTh.find('input').val(value);
                } else if ($target.is('select')) {
                    var $select = $coreTh.find('select');
                    $select.find('option[selected]').removeAttr('selected');
                    $select.find('option[value="' + value + '"]').attr('selected', true);
                }

                that.triggerSearch();
            });
        }
    };
})(jQuery);
