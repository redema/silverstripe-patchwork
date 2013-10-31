/*!
 * Copyright (c) 2013, Redema AB - http://redema.se/
 * License: http://opensource.org/licenses/BSD-3-Clause
 */

(function ($) {
	$.fn.bootstrapFrontEndForm = function () {
		return this.each(function () {
			var $this = $(this);
			
			$this.addClass('bootstrap-form');
			
			$this.find([
				'button',
				'input[type=submit]',
				'input[type=reset]',
				'input[type=image]'
			].join(', '))
				.addClass('btn')
				.addClass('btn-default');
			
			$this.find([
				'input[type=text]',
				'input[type=password]',
				'input[type=email]',
				'input[type=datetime]',
				'input[type=datetime-local]',
				'input[type=date]',
				'input[type=month]',
				'input[type=time]',
				'input[type=week]',
				'input[type=number]',
				'input[type=url]',
				'input[type=search]',
				'input[type=search]',
				'input[type=tel]',
				'input[type=color]',
				'input[type=checkbox]',
				'select',
				'textarea'
			].join(', '))
				.addClass('form-control')
				.each(function () {
					var $this = $(this);
					var $parent = $this.parents('.field');
					var $label = $parent.find('label');
					$parent.addClass('form-group');
					var placeholderEnabledFields = [
						'text',
						'password',
						'email',
						'datetime',
						'datetime-local',
						'date',
						'month',
						'time',
						'week',
						'number',
						'url',
						'search',
						'search',
						'tel',
						'color'
					];
					if ($.inArray($this.attr('type'), placeholderEnabledFields) != -1)
						$this.attr('placeholder', $label.html());
				});
			
			// DOMNodeInserted is used to support IE9, but obviously MutationObserver
			// would be a better choice otherwise.
			// 
			// https://developer.mozilla.org/en-US/docs/Web/API/MutationObserver
			$('body').on('DOMNodeInserted', '#' + $this.attr('id'), function (event) {
				var $target = $(event.target);
				var classes = {
					'required': ['alert', 'alert-info'],
					'warning': ['alert', 'alert-warning'],
					'good': ['alert', 'alert-success'],
					'bad': ['alert', 'alert-danger']
				};
				
				for (var key in classes) {
					if ($target.hasClass(key))
						$target.addClass(classes[key].join(' '));
				}
			});
		});
	};
})(jQuery);

