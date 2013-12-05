/*!
 * Copyright (c) 2013, Redema AB - http://redema.se/
 * License: http://opensource.org/licenses/BSD-3-Clause
 */

function PageAggregate_ready($) {
	$('.pageaggregate-searchform ul#Form_SearchForm_Tags li').each(function () {
		var $this = $(this);
		var $input = $this.find('input');
		var $label = $this.find('label');
		
		if ($input.is(':checked')) {
			$label.addClass('checked');
		}
		$label.click(function () {
			$(this).toggleClass('checked');
		});
	});
}
