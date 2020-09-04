<?php

class Form
{
    private $fields = array();
    private $fieldsLen;
    private $params = [];

    public function __construct(array $fields, array $params = [])
    {
        $this->fields = $fields;
        $this->fieldsLen = count($fields);
        $this->params = $params;

        if (!isset($this->params['checkAsId'])) {
            $this->params['checkAsId'] = false;
        }
    }

    /**
     * Assigns NULL value to non-existing form fields
     * For example, to checkboxes
     */
    public function initFormFields()
    {
        for ($i = 0; $i < $this->fieldsLen; ++$i)
        {
            $type = $this->fields[$i]['InputType'];
            $name = $this->fields[$i]['ColumnName'];
            if ('file' === $type) {
                continue;
            }

            if (!isset($_POST[$name])) {
                $_POST[$name] = null;
            }
        }
    }

    public function normalizeFormFields()
    {
        for ($i = 0; $i < $this->fieldsLen; ++$i)
        {
            $type = $this->fields[$i]['InputType'];
            $name = $this->fields[$i]['ColumnName'];

            switch ($type) {
                case 'id':
                    $id = (int)$_POST[$name];

                    if ($id > 0) {
                        $_POST[$name] = $id;
                    } else {
                        $_POST[$name] = null;
                    }

                    break;
                default:
                    continue;
            }
        }
    }

    public function check()
    {
        $fields =& $this->fields;

        $this->initFormFields();
        $this->normalizeFormFields();
        $this->checkFieldsForCorrectness();

        $errors = [];
        for ($i = 0, $errorIndex = 0, $len = count($fields); $i < $len; ++$i)
        {
            $fieldInformation = $fields[$i];
            $columnName = $fieldInformation['ColumnName'];
            $formElementType  = $fieldInformation['InputType'];
            $isRequired = isset($fieldInformation['IsRequired']) && (int)$fieldInformation['IsRequired'];

            if ($formElementType === 'file') {
                $noFiles = !isset($_FILES[$columnName]) ||
                    !is_array($_FILES[$columnName]['name']) ||
                    count($_FILES[$columnName]['name']) === 0 ||
                    empty($_FILES[$columnName]['name'][0]);

                if ($noFiles && isset($fieldInformation['IsRequired']) && (int)$fieldInformation['IsRequired']) {
                    $this->addError($errors, $errorIndex, $fieldInformation );
                    continue;
                }
            } else {
                $formElementValue = $_POST[$columnName];

                if ($formElementType === "arrayOfIds" || $formElementType === "arrayOfInts") {
                    $empty = false;

                    if (!isset($_POST[$columnName]) || !is_array($_POST[$columnName])) {
                        $empty = true;
                    } else {
                        foreach ($_POST[$columnName] as $input) {
                            if ($this->params['checkAsId']) {
                                if ( (int)$input <= 0)
                                    $empty = true;
                            } else {
                                if ( strlen(trim($input)) <= 0)
                                    $empty = true;
                            }
                        }

                        // FIXME
                        if (count($formElementValue) === 0) {
                            $empty = true;
                        }

                    }

                    if ($isRequired && $empty) {
                        $this->addError($errors, $errorIndex, $fieldInformation );
                        //$errors[] = sprintf("%s. \"%s\" vacib sahədi", $errorIndex, $fieldInformation['Title']);
                        continue;
                    }
                }

                else if ($formElementType === "array") {
                    $empty = false;

                    if ($_POST[$columnName] === NULL || !is_array($_POST[$columnName])) {
                        $empty = true;
                    } else {
                        foreach ($_POST[$columnName] as $el) {
                            if ('' === $el) {
                                $empty = true;
                            }
                        }
                    }

                    if ($isRequired && $empty) {
                        $this->addError($errors, $errorIndex, $fieldInformation );
                    }
                }

                else if ($formElementType === "commaSeparatedIds") {
                    $empty = false;

                    if (!isset($_POST[$columnName]) || !is_string($_POST[$columnName])) {
                        $empty = true;
                    } else {
                        $arrayOfIds = array_filter(explode(',', $_POST[$columnName]), function($element) {
                            return abs((int)$element) > 0;
                        });

                        if (count($arrayOfIds) === 0)
                            $empty = true;
                    }

                    if ($isRequired && $empty) {
                        $this->addError($errors, $errorIndex, $fieldInformation );
                        continue;
                    }
                }

                else if ($formElementType === "id") {
                    if ($isRequired && is_null($formElementValue)) {
                        $this->addError($errors, $errorIndex, $fieldInformation );
                        continue;
                    }
                }

                else if (empty($_POST[$columnName]) && isset($fieldInformation['IsRequired']) && (int)$fieldInformation['IsRequired']) {
                    $this->addError($errors, $errorIndex, $fieldInformation );
                    //$errors[] = sprintf("%s. \"%s\" vacib sahədi", $errorIndex, $fieldInformation['Title']);
                    continue;
                }
            }

            if (array_key_exists('customValidation', $fieldInformation) && is_callable($fieldInformation['customValidation'])) {
                $result = $fieldInformation['customValidation']($fieldInformation);

                if (false === $result) {
                    $errorIndex++;
                    $this->addError($errors, $errorIndex, $fieldInformation );
                }
            }
        }



        if (count($errors) > 0) {
            print json_encode([
                'status' => 'error',
                'errors' => $errors
            ]);
            exit();
        }
    }



    private function addError(&$errors, &$errorIndex, $fieldInformation)
    {
        $errorIndex++;

        if (array_key_exists('IsRequiredErrorMessage', $fieldInformation)) {
            $errors[] = sprintf("%s. %s", $errorIndex, $fieldInformation['IsRequiredErrorMessage']);
        } else {
            $errors[] = sprintf("%s. \"%s\" vacib sahədi", $errorIndex, $fieldInformation['Title']);
        }
    }

    public function checkFieldsForCorrectness()
    {

    }

    public function collectDataToBeInserted()
    {
        $fields =& $this->fields;

        $dataToBeInserted = [];

        for ($i = 0, $len = count($fields); $i < $len; ++$i)
        {
            $fieldInformation = $fields[$i];
            $columnName = $fieldInformation['ColumnName'];
            $formElementType  = $fieldInformation['InputType'];

            if ($formElementType === 'file') {
                continue;
            }

            $formElementValue = isset($_POST[$columnName]) ? $_POST[$columnName] : NULL;
            $dataToBeInserted[$columnName] = convertValueToSQLFormat($formElementType, $formElementValue);
        }

        return  $dataToBeInserted;
    }



    public function saveFiles($moduleEntryId, $moduleNamePrefix, $savePath, $params = [])
    {
        $fields =& $this->fields;

        for ($i = 0, $leni = count($fields); $i < $leni; ++$i)
        {
            $fieldInformation = $fields[$i];
            $columnName = $fieldInformation['ColumnName'];
            if ($fieldInformation['InputType'] !== 'file') {
                continue;
            }

            if (!isset($_FILES[$columnName])) {
                continue;
            }
            $files = saveFiles($columnName, $savePath, true, false, $params);

            for ($j = 0, $lenj = count($files); $j < $lenj; ++$j) {

                    SQL::insert('tb_files', [
                        'module_name'           => $moduleNamePrefix . '_' . $columnName,
                        'module_entry_id'       => $moduleEntryId,
                        'file_original_name'    => $files[$j]['file_original_name'],
                        'file_actual_name'      => $files[$j]['file_actual_name'],
                        'created_by'            => (int)$_SESSION['erpuserid'],
                    ]);
            }

        }
    }

    public function updateFields($fields)
    {
        $this->fields = $fields;
    }
}