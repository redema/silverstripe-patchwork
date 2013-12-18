/*!
 * Copyright (c) 2013, Redema AB - http://redema.se/
 * License: http://opensource.org/licenses/BSD-3-Clause
 */

(function ($) {
	$.extend({
		triggerPageTypeCallback: function (patchwork, type) {
			var callback = patchwork.PageType + '_' + type;
			if (typeof window[callback] === 'function')
				window[callback]($);
		},
		walkExternalAnchors: function (callback) {
			$([
				'a[href^="http://"]:not(.not-external)',
				'a[rel="external"]',
				'area[href^="http://"]:not(.not-external)',
				'area[rel="external"]'
			].join(', ')).each(callback);
		}
	});
	
	$.fn.colorboxify = function (options) {
		return this.each(function () {
			var $this = $(this);
			var opts = $.extend({}, $.fn.colorboxify.defaults, options);
			
			if ($this.attr('href').match(window.patchwork.RegExps.ImgURL)) {
				$this.colorbox(opts['colorbox']);
			} else {
				for (var type in opts) {
					if ($this.hasClass(type)) {
						$this.colorbox(opts[type]);
					}
				}
			}
			
			if (opts.typographyRel && $this.hasClass('cboxElement') && !$this.hasClass('alone')) {
				var $typography = $(this).closest('.typography');
				if ($typography.length) {
					if (!$typography.data(opts.typographyRelName)) {
						$typography.data(opts.typographyRelName, opts.typographyRelName
							+ '-' + Math.random().toString().replace(/[^0-9]/, ''));
					}
					$this.attr('rel', $typography.data(opts.typographyRelName));
				}
			}
		});
	};
	
	$.fn.colorboxify.defaults = {
		typographyRel: true,
		typographyRelName: 'colorboxify-rel',
		'colorbox': {
			maxWidth: '95%',
			maxHeight: '95%'
		},
		'colorbox-youtube': {
			iframe: true,
			innerWidth: 560,
			innerHeight: 315
		},
		'colorbox-vimeo': {
			iframe: true,
			innerWidth: 560,
			innerHeight: 315
		},
		'colorbox-iframe': {
			iframe: true,
			width: "80%",
			height: "80%"
		}
	};
	
})(jQuery);
