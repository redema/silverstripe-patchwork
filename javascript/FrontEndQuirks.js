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
})(jQuery);
