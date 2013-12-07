/*!
 * Copyright (c) 2013, Redema AB - http://redema.se/
 * License: http://opensource.org/licenses/BSD-3-Clause
 */

jQuery.noConflict();

(function ($) {
	$(document).ready(function () {
		$.triggerPageTypeCallback(window.patchwork, 'ready');
		$.walkExternalAnchors(function (i, el) {
			$(el).removeAttr('target');
			if (!$(el).hasClass('colorbox')) {
				$(el).click(function (event) {
					event.preventDefault();
					event.stopPropagation();
					window.open($(this).attr('href'), '_blank');
				});
			}
		});
		
		$('a[target="_blank"]').each(function () {
			var $this = $(this);
			if ($this.attr('href').match(window.patchwork.RegExps.ImgURL)) {
				$this.removeAttr('target');
				$this.addClass('colorbox');
			}
		});
		
		$('form').bootstrapFrontEndForm();
		$('.colorbox').colorboxify();
	});
	
	$(window).load(function () {
		$.triggerPageTypeCallback(window.patchwork, 'load');
	});
})(jQuery);
