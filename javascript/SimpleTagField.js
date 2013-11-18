/*!
 * Combine the best parts of silverstripe-autocomplete by
 * Damian Mooyman (tractorcow) and silverstripe-tagfield
 * by Ingo Schommer (chillu).
 * 
 * @see https://github.com/tractorcow
 * @see https://github.com/tractorcow/silverstripe-autocomplete/
 * @see https://github.com/chillu/
 * @see https://github.com/chillu/silverstripe-tagfield/
 * 
 * @see http://jqueryui.com/autocomplete/#multiple-remote
 * 
 * Copyright (c) 2013, Redema AB - http://redema.se/
 * License: http://opensource.org/licenses/BSD-3-Clause
 */

jQuery(document).ready(function ($) {
	$('.field.simpletag input.text').live('focus', function () {
		function split(tags) {
			return tags.split(/,\s*/);
		}
		function extractLast(term) {
			return split(term).pop();
		}
		
		var $input = $(this);
		if ($input.attr('data-loaded') != 'true') {
			$input.attr('data-loaded', 'true');
			$input.autocomplete({
				source: function(request, response) {
					$.getJSON($input.attr('data-source'), {
						term: extractLast(request.term)
					}, response );
				},
				focus: function () {
					return false;
				},
				search: function() {
					var term = extractLast(this.value);
					if (term.length < $input.attr('data-min-length')) {
						return false;
					}
				},
				select: function (event, ui) {
					var terms = split(this.value);
					terms.pop();
					terms.push(ui.item.value);
					terms.push("");
					this.value = terms.join(", ");
					return false;
				}
			});
		}
	});
});

