;(function ($, _) {

	var themeWrappers = {
		'bootstrap': '<div class="row">' +
			'<div class="col-md-%s">%s</div>' +
			'<div class="col-md-%s">%s</div>' +
		'</div>'
	};

	var themeClassses = {
		'bootstrap': ['form-control']
	};

	function setElementAttributes() {

	}

	$.fn.extend({
		dateWithDay: function (params) {
			var container  = $(this);

			var dateInputHTML = '<input type="text">';
			var dayInputHTML  = '<input type="number">';

			_.defaults(params, {
				dateInput: {},
				dayInput: {}
			});

			if (_.isArray(params.dateInput.attrs)) {
				setElementAttributes(dateInputHTML, params.dateInput.attrs);
			}

			if (_.isArray(params.dayInput.attrs)) {
				setElementAttributes(dayInputHTML, params.dayInput.attrs);
			}
		}
	});

})($, _);