/*!
 * Copyright (c) 2013, Redema AB - http://redema.se/
 * License: http://opensource.org/licenses/BSD-3-Clause
 */

(function ($) {
	$.extend({
		walkExternalAnchors: function (callback) {
			$([
				'a[href^=http]:not(.not-external)',
				'a[rel=external]',
				'area[href^=http]:not(.not-external)',
				'area[rel=external]'
			].join(', ')).each(callback);
		}
	});
	
	$.fn.colorboxify = function (options) {
		return this.each(function () {
			var $this = $(this);
			var opts = $.extend({}, $.fn.colorboxify.defaults, options);
			
			if ($this.attr('href').match(/\.(png|jpe?g|gif)$/i)) {
				$this.colorbox(opts['colorbox']);
			} else {
				for (var type in opts) {
					if ($this.hasClass(type)) {
						$this.colorbox(opts[type]);
					}
				}
			}
		});
	};
	
	$.fn.colorboxify.defaults = {
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
