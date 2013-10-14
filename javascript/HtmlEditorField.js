/*!
 * Copyright (c) 2013, Redema AB - http://redema.se/
 * License: http://opensource.org/licenses/BSD-3-Clause
 */

(function ($) {
	$.entwine('ss', function ($) {
		$('form.htmleditorfield-mediaform .ss-htmleditorfield-file.image').entwine({
			getAttributes: function () {
				return {
					'src' : this.find(':input[name=URL]').val(),
					'alt' : this.find(':input[name=AltText]').val(),
					'title' : this.find(':input[name=Title]').val(),
					'class' : this.find(':input[name=CSSClass]').val()
				};
			}
		});
	});
})(jQuery);

