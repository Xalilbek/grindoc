<?php

function isPdfFileRequired ()
{
    $result = '';
    $sqlVacibSahePdf = "SELECT value FROM tb_options WHERE option_name = 'pdf_vacib_sahe'";
    $vacibSaheValue = DB::fetch($sqlVacibSahePdf);

    if ($vacibSaheValue['value'] == 1) {
        $result = true;
    } else {
        $result = false;
    }

    return $result;
}

function checkPdfExistense($fieldInformation)
{
    if (false === $fieldInformation['IsRequired']) {
        return true;
    }

    $columnName = $fieldInformation['ColumnName'];

    if (isset($_POST[$columnName]) && is_array($_POST[$columnName])) {
        $fileIds = array_map('intval', $_POST[$columnName]);

        $fileName = "SELECT file_actual_name FROM tb_files WHERE id IN  (" . implode(',', $fileIds) . ")";
        $fileNameValue = DB::fetchAll($fileName);

        foreach ($fileNameValue as $value) {
            $fileType = substr($value['file_actual_name'], -3);


            if ($fileType == 'pdf') {
                return true;
            }
        }
    }

    return false;
}