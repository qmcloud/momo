/*
* jQuery Watermark plugin
* @requires jQuery v1.3 or later
*
* Examples at: http://mario.ec/static/jq-watermark/
* Copyright (c) 2010 Mario Estrada
* Licensed under the MIT license:
* http://www.opensource.org/licenses/mit-license.php
*
*/

;(function ($) {
  var old_ie = /MSIE [1-7]\./.test(navigator.userAgent);
  var hard_left = 4;
  $.watermarker = function () { };
  $.extend($.watermarker, {
    defaults: {
      color: '#999',
      left: 0,
      top: 0,
      fallback: false,
      animDuration: 300,
      minOpacity: 0.6
    },
    setDefaults: function (settings) {
      $.extend($.watermarker.defaults, settings);
    },
    checkVal: function (val, label) {
      if (val.length === 0) {
        $(label).show();
      } else {
        $(label).hide();
      }

      return val.length > 0;
    },
    html5_support: function () {
      var i = document.createElement('input');
      return 'placeholder' in i;
    }
  });

  $.fn.watermark = function (text, options) {
    var elems;
    options = $.extend({}, $.watermarker.defaults, options);
    elems = this.filter('textarea, input:not(:checkbox,:radio,:file,:submit,:reset)');

    if (options.fallback && $.watermarker.html5_support()) {
      return this;
    }

    elems.each(function () {
      var $elem, attr_name, label_text, watermark_container, watermark_label, control_id;
      var e_margin_left, e_top = 0, e_height;

      $elem = $(this);
      control_id = $elem.attr('id');

      if ($elem.attr('data-jq-watermark') === 'processed') {
        return;
      }

      attr_name = $elem.attr('placeholder') !== undefined && $elem.attr('placeholder') !== '' ? 'placeholder' : 'title';
      label_text = text === undefined || text === '' ? $(this).attr(attr_name) : text;
      watermark_container = $('<span class="watermark_container"></span>');
      watermark_label = $('<label class="watermark" for="' + control_id + '">' + label_text + '</label>');

      // If used, remove the placeholder attribute to prevent conflicts
      if (attr_name === 'placeholder') {
        $elem.removeAttr('placeholder');
      }

      watermark_container.css({
        display: 'inline-block',
        position: 'relative'
      });

      if ($elem.attr('data-percent-width') === 'true') {
        watermark_container.css('width', '100%');
      }

      if ($elem.attr('data-percent-height') === 'true') {
        watermark_container.css('height', '100%');
      }

      if (old_ie) {
        watermark_container.css({
          zoom: 1,
          display: 'inline'
        });
      }

      $elem.wrap(watermark_container).attr('data-jq-watermark', 'processed');

      if (this.nodeName.toLowerCase() === 'textarea') {
        e_height = parseInt($elem.css('line-height'), 10);
        e_height = e_height === 'normal' ? parseInt($elem.css('font-size'), 10) : e_height;
        e_top = ($elem.css('padding-top') !== 'auto' ? parseInt($elem.css('padding-top'), 10) : 0);
      } else {
        e_height = $elem.outerHeight();
        if (e_height <= 0) {
          e_height = ($elem.css('padding-top') !== 'auto' ? parseInt($elem.css('padding-top'), 10) : 0);
          e_height += ($elem.css('padding-bottom') !== 'auto' ? parseInt($elem.css('padding-bottom'), 10) : 0);
          e_height += ($elem.css('height') !== 'auto' ? parseInt($elem.css('height'), 10) : 0);
        }
      }

      e_top += ($elem.css('margin-top') !== 'auto' ? parseInt($elem.css('margin-top'), 10) : 0);

      e_margin_left = $elem.css('margin-left') !== 'auto' ? parseInt($elem.css('margin-left'), 10) : 0;
      e_margin_left += $elem.css('padding-left') !== 'auto' ? parseInt($elem.css('padding-left'), 10) : 0;

      watermark_label.css({
        position: 'absolute',
        display: 'block',
        fontFamily: $elem.css('font-family'),
        fontSize: $elem.css('font-size'),
        color: options.color,
        left: hard_left + options.left + e_margin_left,
        top: options.top + e_top,
        height: e_height,
        lineHeight: e_height + 'px',
        textAlign: 'left',
        pointerEvents: 'none'
      });

      $.watermarker.checkVal($elem.val(), watermark_label);

      if (!control_id) {
        watermark_label
          .data('jq_watermark_element', $elem)
          .click(function () {
            $($(this).data('jq_watermark_element')).trigger('click').trigger('focus');
          });
      }

      $elem.before(watermark_label)
        .bind('focus.jq_watermark', function () {
          if (!$.watermarker.checkVal($(this).val(), watermark_label)){
            watermark_label.stop().fadeTo(options.animDuration, options.minOpacity);
          }
        })
        .bind('blur.jq_watermark change.jq_watermark', function () {
          if (!$.watermarker.checkVal($(this).val(), watermark_label)){
            watermark_label.stop().fadeTo(options.animDuration, 1);
          }
        })
        .bind('keydown.jq_watermark, paste.jq_watermark', function (e) {
          $(watermark_label).hide();
        })
        .bind('keyup.jq_watermark', function (e) {
          $.watermarker.checkVal($(this).val(), watermark_label);
        });
    });

    return this;
  };

  $(function () {
    $('.jq_watermark').watermark();
  });
})(jQuery);
