function showClosedOrToBeClosedDocuments(docs)
{
	if (!docs.length) {
		return;
	}

	var docsWithCompletedOperations = [];
	docs.forEach(function(doc) {
		if (doc.operationsCompleted)
			docsWithCompletedOperations.push(doc.number);
	});

	if (docsWithCompletedOperations.length === 0) {
		return;
	}

	var text = '';
	if (docs[0].isClosed) {
		text = "Bağlanan daxil olan sənədlər: " + docsWithCompletedOperations.join(', ');
	} else {
		text = "Bağlanacağ daxil olan sənədlər: " + docsWithCompletedOperations.join(', ');
	}

	swals({
		title: '',
		text: text,
		type: "info",
		confirmButtonClass: "btn btn-circle btn-default btn-outline",
		confirmButtonText: "Bağla"
	});
}

function reserveDocumentNumber(docNumberMainContainer, documentNumberRelatedData, afterReserve) {
	var documentNumberInput = docNumberMainContainer.find('[name="document_number[]"]');
	var reservedDocumentNumberInput = docNumberMainContainer.find('[name="reserved_document_number_id[]"]');
	var documentMumberLoadingIcon = docNumberMainContainer.find('.document-number-loading-icon');
	var documentNumberSettingIdInput = docNumberMainContainer.find('[name="setting_id[]"]');

	documentMumberLoadingIcon.fadeIn();
	$.post('prodoc/ajax/reserve_doc_number.php', documentNumberRelatedData, function(result) {
		if ('error' === result.status) {
			toastr["error"](result.error_msg);
			return;
		}

		documentNumberInput.val(result.number.documentNumber);
		reservedDocumentNumberInput.val(result.number.id);
		documentNumberSettingIdInput.val(result.setting.id);

		docNumberMainContainer.find('.reserve-number').prop('checked', true);
		$.uniform.update();

		if (_.isFunction(afterReserve)) {
			afterReserve.call(this, result, docNumberMainContainer);
		}

		documentMumberLoadingIcon.fadeOut();

		toastr["success"]('Nömrə bron olundu');
	}, 'json');
}

function freeDocumentNumber(docNumberMainContainer, afterFree) {
	var documentNumberInput = docNumberMainContainer.find('[name="document_number[]"]');
	var reservedDocumentNumberInput = docNumberMainContainer.find('[name="reserved_document_number_id[]"]');
	var documentMumberLoadingIcon = docNumberMainContainer.find('.document-number-loading-icon');

	if (+reservedDocumentNumberInput.val() === 0) {
		// no reserved number
		return;
	}

	var data = {
		id: +reservedDocumentNumberInput.val()
	};

	documentMumberLoadingIcon.fadeIn();
	$.post('prodoc/ajax/free_doc_number.php', data, function(result) {
		if ('error' === result.status) {
			toastr["error"](result.error_msg);
			return;
		}

		documentNumberInput.val('-');
		reservedDocumentNumberInput.val(0);

		if (_.isFunction(afterFree)) {
			afterFree.call(this, result, docNumberMainContainer);
		}

		docNumberMainContainer.find('.reserve-number').prop('checked', false);
		$.uniform.update();

		documentMumberLoadingIcon.fadeOut();

		toastr["warning"]('Nömrə brondan çixarıldı');
	}, 'json');
}

function outgoingDocumentReserveDocumentNumber(docNumberMainContainer, documentNumberRelatedData)
{
	reserveDocumentNumber(docNumberMainContainer, documentNumberRelatedData, function(result, docNumberMainContainer) {
		correctDocumentNumbersState(docNumberMainContainer);
	});
}

function outgoingDocumentFreeDocumentNumber(docNumberMainContainer)
{
	freeDocumentNumber(docNumberMainContainer, function(result, docNumberMainContainer) {
		correctDocumentNumbersState(docNumberMainContainer);
	});
}

function outgoingDocumentMakeNumberTheSame(mainContainer)
{
	var prevNumbersData = getOutgoingDocumentsNumberData();
	correctDocumentNumbersState(mainContainer);
	var currentNumberData = getOutgoingDocumentsNumberData();

	var urn = getUnusedReservedNumbers(prevNumbersData, currentNumberData);

	if (urn.length > 0) {
		var postData = {};
		postData.id = urn[0];

		$.post('prodoc/ajax/free_doc_number.php', postData, function(result) {
			if ('error' === result.status) {
				toastr["error"](result.error_msg);
				return;
			}

			toastr["warning"]('Nömrə brondan çixarıldı');
		}, 'json');
	}
}

function getUnusedReservedNumbers(prevNumbersData, currentNumberData)
{
	var prevReservedNumbers = [];
	prevNumbersData.forEach(function(prevND) {
		if (+prevND.reserved_document_number_id === 0) {
			return;
		}

		prevReservedNumbers.push(prevND.reserved_document_number_id);
	});

	var currentReservedNumbers = [];
	currentNumberData.forEach(function(prevND) {
		if (+prevND.reserved_document_number_id === 0) {
			return;
		}

		currentReservedNumbers.push(prevND.reserved_document_number_id);
	});

	return _.difference(prevReservedNumbers, currentReservedNumbers);
}

function outgoingDocumentMakeNumberUnique(mainContainer)
{
	correctDocumentNumbersState(mainContainer);
}

function correctDocumentNumbersState(mainContainer)
{
	var cavabSenedIndex = mainContainer.closest('.cavab-senedi').index();

	var numbersData = getOutgoingDocumentsNumberData();
	numbersData = correctOutgoingDocumentNumbersData(numbersData, cavabSenedIndex);
	setOutgoingDocumentsNumberData(numbersData);
}

function getOutgoingDocumentsNumberData()
{
	var numbersData = [];
	$('.cavab-senedleri > .cavab-senedi').each(function(i, e) {
		var currentData = {};

		currentData.reserved_document_number_id = +$(e).find('[name="reserved_document_number_id[]"]').val();
		currentData.setting_id = +$(e).find('[name="setting_id[]"]').val();
		currentData.is_unique  = +$(e).find('[name="is_unique[]"]').is(':checked');
		currentData.document_number = $(e).find('[name="document_number[]"]').val();

		numbersData[i] = currentData;
	});

	return numbersData;
}

function setOutgoingDocumentsNumberData(numbersData)
{
	numbersData.forEach(function(numberData, index) {
		setOutgoingDocumentNumberData(numberData, index);
	});
}

function setOutgoingDocumentNumberData(data, containerIndex)
{
	var currentODContainer = $('.cavab-senedleri > .cavab-senedi:eq(' + containerIndex + ')');

	currentODContainer
		.find('[name="reserved_document_number_id[]"]')
		.val(data.reserved_document_number_id)
	;

	if (+data.reserved_document_number_id === 0) {
		data.document_number = '-';
	}

	currentODContainer
		.find('.document-number-text')
		.text(data.document_number)
	;

	currentODContainer
		.find('[name="document_number[]"]')
		.val(data.document_number)
	;

	var reserveCheckboxChecked = +data.reserved_document_number_id > 0;

	currentODContainer
		.closest('.cavab-senedi')
		.find('.reserve-number-from-info')
		.prop('checked', reserveCheckboxChecked)
	;

	currentODContainer
		.closest('.cavab-senedi')
		.find('.reserve-number')
		.prop('checked', reserveCheckboxChecked)
	;

	var isUnique = +data.is_unique > 0;

	currentODContainer
		.closest('.cavab-senedi')
		.find('.unique-num')
		.prop('checked', isUnique)
	;

	currentODContainer
		.closest('.cavab-senedi')
		.find('.unique-num-from-info')
		.prop('checked', isUnique)
	;

	$.uniform.update();
}

function correctOutgoingDocumentNumbersData(numbersData, initiator)
{
	numbersData = correctUniqueNumbers(numbersData);
	numbersData = correctSameNumbers(numbersData, initiator);

	return numbersData;
}

function correctSameNumbers(numbersData, initiatorIndex)
{
	var settings = {};

	if (
		!_.isUndefined(initiatorIndex) &&
		!_.isUndefined(numbersData[initiatorIndex]) &&
		+numbersData[initiatorIndex].is_unique === 0
	) {
		settings[numbersData[initiatorIndex].setting_id] = numbersData[initiatorIndex];
	}

	for (var i = 0; i < numbersData.length; ++i) {
		if (!_.isUndefined(settings[numbersData[i].setting_id])) {
			continue;
		}

		if (+numbersData[i].is_unique === 1) {
			continue;
		}

		var isReserved = +numbersData[i].reserved_document_number_id > 0;
		if (!isReserved) {
			continue;
		}

		settings[numbersData[i].setting_id] = numbersData[i];
	}

	for (i = 0; i < numbersData.length; ++i) {
		if (+numbersData[i].is_unique === 1) {
			continue;
		}

		var currentSettingId = numbersData[i].setting_id;
		if (_.isUndefined(settings[currentSettingId])) {
			numbersData[i].reserved_document_number_id = 0;
		} else {
			numbersData[i].reserved_document_number_id = settings[currentSettingId].reserved_document_number_id;
			numbersData[i].document_number = settings[currentSettingId].document_number;
		}
	}

	return numbersData;
}

function correctUniqueNumbers(numbersData)
{
	for (var i = 0; i < numbersData.length; ++i) {
		if (+numbersData[i].is_unique === 0) {
			continue;
		}

		var isReserved = +numbersData[i].reserved_document_number_id > 0;
		if (!isReserved) {
			continue;
		}

		for (var j = 0; j < numbersData.length; ++j) {
			if (j === i) {
				continue;
			}

			if (numbersData[j].reserved_document_number_id === numbersData[i].reserved_document_number_id) {
				numbersData[i].reserved_document_number_id = 0;
			}
		}
	}

	return numbersData;
}

function showErrors(errorsList, errorsListJQuery)
{
	errorsListJQuery.html('');

	errorsList.forEach(function (error) {
		errorsListJQuery.append('<span>' + error + '</span><br>')
	});

	$('.scroll-to-top').trigger('click');
	errorsListJQuery.slideDown();
	toastr.error('Səhv var!');
}

function showErrorsWithSwals(errorsList, showAll)
{
	if (errorsList.length === 0) {
		return;
	}

	if (showAll === true) {
		// not implemented
		return;
	}

	var firstError = errorsList[0];

	swals({
		title: "",
		text: firstError,
		type: "warning",
		confirmButtonClass: "btn-primary btn-sm",
		confirmButtonText: "Bağla"
	});
}

function showResultSelectIfPossible(docIds, resultSelectJquery, mode)
{
	if (_.isUndefined(resultSelectJquery)) {
		resultSelectJquery = $('.netice_selecti');
	}

	if (!docIds.length) {
		if (mode === 'readonly') {
			resultSelectJquery.select2('val', '');
			resultSelectJquery.select2('readonly', true);
		} else {
			resultSelectJquery.hide();
		}
	}

	$.get('prodoc/ajax/result_select.php', {docIds: JSON.stringify(docIds)}, function (result) {
		if (result.success_msg === "1") {
			if (mode === 'readonly') {
				resultSelectJquery.select2('readonly', false);
			} else {
				resultSelectJquery.show();
			}
		} else {
			if (mode === 'readonly') {
				resultSelectJquery.select2('val', '');
				resultSelectJquery.select2('readonly', true);
			} else {
				resultSelectJquery.hide();
			}
		}
	}, 'json');
}

function getFiles(data)
{
    if( $('.side_body_content').find("input[name='sened_fayl[]']").length &&  $('.side_body_content').find("input[name='sened_fayl[]']")[0].files.length)
    {
        $('.side_body_content').find("input[name='sened_fayl[]']").each(function()
        {
            for(var ii in $(this)[0].files)
            {
                data.append("sened_fayl[]",$(this)[0].files[ii]);
            }
        });
    }

    if( $('.side_body_content').find("input[name='qoshma_fayl[]']").length &&  $('.side_body_content').find("input[name='qoshma_fayl[]']")[0].files.length)
    {
        $('.side_body_content').find("input[name='qoshma_fayl[]']").each(function()
        {
            for(var ii in $(this)[0].files)
            {
                data.append("qoshma_fayl[]",$(this)[0].files[ii]);
            }
        });
    }

    getFilesDropzone(data, 'sened_fayl[]');
    getFilesDropzone(data, 'qoshma_fayl[]');
}

function getFilesDropzone(data, name, path = 'side_body_content') {
    let dropzonePlugin = $('.'+path).find('[data-plugin=dropzone][name="'+name+'"]');
    if (dropzonePlugin.length) {
        let fileFieldName = dropzonePlugin.attr('name');
        Dropzone.forElement(dropzonePlugin.get(0)).files.forEach((file) => {
            data.append(fileFieldName, file);
        });
    }
}

function getFilesChixanDropzone(data, name) {
	$('.cavab-senedi').each(function(i, e) {
		let dropzonePlugin = $(this).find('[name="'+name+'"]'),
			 fileFieldName = dropzonePlugin.attr('name');

		if (dropzonePlugin.length) {
			Dropzone.forElement(dropzonePlugin.get(0)).files.forEach((file) => {
				data.append(fileFieldName + '_' + i + '[]', file);
			});
		}
	});
}

function getValidatorFile() {
	var status = false;

	$('[name="sened_fayl[]"]').find('.dz-preview').each(function () {
		let allFiles = $(this).find('.dz-details .dz-filename span').text().split('.');

		if(jQuery.inArray("pdf", allFiles) > 0){
            status = true;
		}
    });

    return status;
}

function getValidatorFileXO(name) {

	var status = true;

    name.find('.dz-preview').each(function () {
	   let allFiles = $(this).find('.dz-details .dz-filename span').text().slice(-3);

	   if(allFiles == "pdf")
	   {
           status = false;
	   }
   	});

	return status;
}

function getMultiSelectValues(el) {
	el = $(el);
	var lastInput  = el.find('[data-function="item"]:first input');
	var firstInput  = el.find('[data-function="item"]:last input.select');

	if (lastInput.select2('data')) {
		firstInput.select2('data', lastInput.select2('data'));
		lastInput.select2('data', null);
	}

	el.find('[data-function="item"]').find(".select2-container .select2-choice abbr").css("display", "none")
}

function CheckForEmptyInput(container, vezife) {
	return Boolean($(container).find('[data-function=item]:first:visible input[vezife=' + vezife + ']').val())
}

function inputValues(value) {
	var arr = [];

	$("[name='"+value+"']").each(function () {
		var value =$(this).val();
		var myarr = value.split("_");
		if (myarr[0]+"_"+myarr[1]==value){
			value=myarr[0];
		}
		if (value != '' && value > 0) {

			arr.push(value)
		}
	})

	return arr;

}

// Remove and append elements with related positions
function loadEtrafliFront(selector, data){
	$.each(data, function (index, val) {
		if ( $("[data-position=" + val +"]").length > 0){
			var elem = $("[data-position=" + val +"]").detach();
			$(selector).append(elem)
		}
	});
}

function sortEtrafliFront(selector, key){
	$(selector).sortable({
		containment: $(selector).closest('div').closest('div'),
		axis: 'y',
		update: function (event, ui) {
			var order = 1;
			var obj = {};
			$(selector + ' > div').each(function (key,value) {
				obj[value.id] = order;
				order++;
			});

			$.get('prodoc/ajax/dashboard/user_interface_button_position.php',
				{
					'key': key,
					tabs: obj
				},
				function (response) {
				});
		}
	});
}

function getImageUrlByType(file, type){
	let typesToImageMap = {
		"application/pdf": "pdf_logo.png",
		"application/vnd.ms-excel": "excel_logo.png",
		"application/vnd.openxmlformats-officedocument.wordprocessingml.document": "word_logo.png",
		"application/msword": "word_logo.png",
		"application/x-rar-compressed": "rar_logo.png",
		"image/gif": "gif_logo.png",
		"image/jpeg": "jpeg_logo.png",
		"image/png": "png_logo.png"
	};

	if (typesToImageMap.hasOwnProperty(type)) {
		$(file.previewElement).find(".dz-image img").attr("src", "prodoc/asset/icons/"+typesToImageMap[type]);
	}

}

function showImage(file) {
	var reader = new FileReader();
	reader.readAsDataURL(file);
	reader.onload = function () {
		var file_id = file.upload.fayl_id;
		$("#ui_gallery_images").find(".imgHolder").remove();
		$("#ui_gallery_images").html('<img data-image="" src="" alt="" class="imgHolder">');

		$('.file-upload').find(".imgHolder")
			.attr('src', reader.result)
			.attr('data-image', reader.result)
			.attr('data-file-id',file_id)
		;

		$("#ui_gallery_images").unitegallery(
			{
				tile_enable_shadow: true,
				tile_link_newpage: false,
				tile_shadow_color: "#DDDDDD",
				tile_show_link_icon: true,
				tile_image_effect_reverse: true,
				tiles_space_between_cols: 20
			});
	};
	reader.onerror = function (error) {
		console.log('Error: ', error);
	};
}

function showFile(files){
	var file = files.name;
	var ind = file.lastIndexOf(".");
	var ext = file.substr(ind + 1);
	var exts = ['png', 'jpg', 'jpeg', 'gif'];
	var file_id = files.upload.fayl_id;

	if (exts.includes(ext)) {
		showImage(files);
	}
	else if ("pdf" === ext) {
		$('#pdf_viewer').attr('src','prodoc/ajax/file/get_file_by_id.php?file_id='+file_id);
		$('#pdf_viewer').attr('data-file-id',file_id);
	}
	else if ("docx" === ext) {
		$('#pdf_viewer').attr('src','prodoc/ajax/file/convert_docx_to_pdf.php?file_id='+file_id);
		$('#pdf_viewer').attr('data-file-id',file_id);
	}
}
