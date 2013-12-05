/*!
 * Copyright (c) 2013, Redema AB - http://redema.se/
 * License: http://opensource.org/licenses/BSD-3-Clause
 */

jQuery.noConflict();

(function ($) {
	$(document).ready(function () {
		$.triggerPageTypeCallback(window.__patchwork, 'ready');
		$.walkExternalAnchors(function (i, el) {
			if (!$(el).hasClass('colorbox')) {
				$(el).click(function (event) {
					event.preventDefault();
					event.stopPropagation();
					window.open($(this).attr('href'), '_blank');
				});
			}
		});
		
		$('form').bootstrapFrontEndForm();
		$('.colorbox').colorboxify();
	});
	
	$(window).load(function () {
		$.triggerPageTypeCallback(window.__patchwork, 'load');
	});
})(jQuery);
