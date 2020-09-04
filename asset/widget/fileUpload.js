;(function ($, _) {

	$.fn.extend({
		fileUpload: function (params) {
			var container  = $(this),
				newFileBtn = container.find('.add-file-btn'),
				fileList   = container.find('.list-of-files');

			if (_.isUndefined(params)) {
				params = {};
			}

			_.checkRequiredProps(params, ['name']);

			newFileBtn.on('click', function() {
				var fileInputContainer = $(s.sprintf(
					'<div class="file">' +
					'<input type="file" name="%s[]" style="display: none;">' +
					'<span class="file-description">' +
					'<i class="fa fa-file-o"></i> ' +
					'<span class="file-name"></span> ' +
					'<span class="file-size"></span>' +
					'</span> ' +
					'<i style="cursor: pointer" class="fa fa-trash text-danger delete"></i>' +
					'</div>'
				, params.name));

				var fileInput = fileInputContainer.find('input');
				fileInput
					.trigger('click')
					.on('change', function () {
						var files = $(this).get(0).files;
						Array.prototype.forEach.call(files, function(file) {
							fileInputContainer.find('.file-name').text(file.name);
						});
						fileList.append(fileInputContainer);

						if (_.isFunction(params.afterAppend)) {
							params.afterAppend.call(this);
						}
					})
			});
            fileList.on('click', '.delete', function() {
                $(this).closest('div.file').remove();
                if (_.isFunction(params.afterDelete)) {
                    params.afterDelete.call(this);
                }
            });

		},
	});

})($, _);