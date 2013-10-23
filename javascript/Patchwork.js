/*!
 * Copyright (c) 2013, Redema AB - http://redema.se/
 * License: http://opensource.org/licenses/BSD-3-Clause
 */

jQuery.noConflict();

function PatchworkDocumentReady($) {
	$.walkExternalAnchors(function (i, el) {
		$(el).click(function (event) {
			event.preventDefault();
			event.stopPropagation();
			window.open($(this).attr('href'), '_blank');
		});
	});
	$('form').bootstrapFrontEndForm();
}
jQuery(document).ready(PatchworkDocumentReady);

function PatchworkWindowLoad($) {
}
jQuery(window).load(PatchworkWindowLoad);
