;(function ($, _) {

	$.fn.extend({
		hideShow: function (params) {
			if (_.isUndefined(params)) {
				params = {};
			}

			var methods = {
				'show': {
					'none': 'show',
					'fade': 'fadeIn',
					'slide': 'slideDown'
				},
				'hide': {
					'none': 'hide',
					'fade': 'fadeOut',
					'slide': 'slideUp'
				}
			};

			var checkboxes  = $(this);
			checkboxes.each(function(index, checkbox) {
				checkbox = $(checkbox);
				checkbox.on('change', function() {
					var methodToBeCalled;
					var checked = checkbox.is(":checked");

					if (!params.animation) {
						params.animation = 'none';
					}

					if (params.showWhenChecked) {
						if (checked) {
							methodToBeCalled = methods['show'][params.animation];
						} else {
							methodToBeCalled = methods['hide'][params.animation];
						}

						if (_.isFunction(params.showWhenChecked)) {
							params.showWhenChecked(checkbox)[methodToBeCalled]()
						} else {
							params.showWhenChecked[methodToBeCalled]();
						}
					}

					if (params.hideWhenChecked) {
						if (checked) {
							methodToBeCalled = methods['hide'][params.animation];
						} else {
							methodToBeCalled = methods['show'][params.animation];
						}

						if (_.isFunction(params.hideWhenChecked)) {
							params.hideWhenChecked(checkbox)[methodToBeCalled]()
						} else {
							params.hideWhenChecked[methodToBeCalled]();
						}
					}

					if (_.isFunction(params.onHide) && !checked) {
						params.onHide(checkbox);
					}

					if (_.isFunction(params.onShow) && checked) {
						params.onShow(checkbox);
					}
				});
			});
		}
	});

})($, _);