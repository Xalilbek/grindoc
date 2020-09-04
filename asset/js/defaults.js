var defaults = {
    dropzone: {
        url: "prodoc/ajax/file/file_save.php",
        paramName: "chixan_senedler_qoshma_fayl",
        autoProcessQueue: true,
        uploadMultiple: true, // uplaod files in a single request
        parallelUploads: 100, // use it with uploadMultiple
        maxFilesize: 30, // MB
        maxFiles: 100,
        acceptedFiles: ".jpg, .jpeg, .png, .gif, .pdf, .docx, .doc, .rar",
        addRemoveLinks: true,
        dictFileTooBig: "File is to big ({{filesize}}mb). Max allowed file size is {{maxFilesize}}mb",
        dictInvalidFileType: "Invalid File Type",
        dictCancelUpload: "Ləğv et",
        dictRemoveFile: "Sil",
        dictMaxFilesExceeded: "Only {{maxFiles}} files are allowed",
        dictDefaultMessage: "Sənədi seçin və ya bura daşıyın",
        removedfile: function (file) {
            $('[data-file-id=' + file.upload.fayl_id + ']').attr('src','');
            file.previewElement.remove();

            $.post('prodoc/ajax/faylSil.php', {
                'fileID': file.upload.fayl_id,
                'edit': 1
            }, function (response) {
            });
        }
    }
};
