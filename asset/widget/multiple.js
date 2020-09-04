;(function ($, _) {

	function addNewItem(params, itemsContainer, event) {
		var templateData = Object.create(null);
		templateData.isFirst = 0 === itemsContainer.find('[data-function="item"]').length;

		var itemHtml = $(_.template($('#' + params.itemTemplateId).html())(templateData));

		if (_.isFunction(params.beforeAppend)) {
			var result = params.beforeAppend.call(this, itemHtml, event, {
				isFirst: templateData.isFirst
			});

			if (false === result) {
				return false;
			}
		}


		params.prepend ?
			itemsContainer.append(itemHtml) :
			itemsContainer.prepend(itemHtml);

		if (0 === itemHtml.index() && false === params.prepend) {
			itemHtml.find('[data-function="action-remove"]').remove();
		}

		if (_.isFunction(params.afterAppend)) {
			params.afterAppend(itemHtml);
		}
	}

	$.fn.extend({
		multiple: function (params) {
			var container      = $(this),
				itemsContainer = container.find('[data-function="container"]');

			if (_.isUndefined(params)) {
				params = {};
			}

			_.checkRequiredProps(params, ['itemTemplateId']);

			params = _.defaults(params, {
				initialItem: true,
				prepend: false
			});

			container.on('click', '[data-function="action-add"]', function(e) {
				addNewItem(params, itemsContainer, e);
			});

			if (params.initialItem) {
				addNewItem(params, itemsContainer, null);
			}

            itemsContainer.on('click', '[data-function="action-remove"]', function() {
                var item = $(this).closest('[data-function="item"]');

                if (_.isFunction(params.beforeDelete)) {
                    var result = params.beforeDelete.call(this, item);

                    if (false === result) {
                        return false;
                    }
                }

                item.remove();

                if (_.isFunction(params.afterDelete)) {
                    params.afterDelete.call(this);
                }
            });
		}
	});

})($, _);