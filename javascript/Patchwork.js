/*!
 * Copyright (c) 2013, Redema AB - http://redema.se/
 * License: http://opensource.org/licenses/BSD-3-Clause
 */

jQuery.noConflict();

function PatchworkDocumentReady($) {
	$('form').bootstrapFrontEndForm();
}
jQuery(document).ready(PatchworkDocumentReady);

function PatchworkWindowLoad($) {
}
jQuery(window).load(PatchworkWindowLoad);
