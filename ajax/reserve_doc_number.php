<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 17.05.2018
 * Time: 16:42
 */
session_start();

require_once '../../class/class.functions.php';
require_once DIRNAME_INDEX . 'modules/module_builder/model.php';
require_once DIRNAME_INDEX . 'prodoc/component/Form.php';
require_once DIRNAME_INDEX . 'prodoc/model/DocumentNumber/DocumentNumberGeneral/DocumentNumberGeneral.php';

use Model\DocumentNumber\DocumentNumberGeneral\DocumentNumberGeneral;

if (!isset($_POST['direction'])) {
    $user->error_msg();
}

$direction =& $_POST['direction'];

$user = new User();

$id = getInt('id');

$document = null;
if ('incoming' === $direction) {
    if (getProjectName() === TS) {
        print json_encode([
            'number'  => '-',
            'setting' => [
                'editable_with_select' => false,
                'editable' => false
            ]
        ]);
        exit();
    }
    $incomingDocumentFields = [
        [
            "IsRequired" => true,
            "InputType" => "text",
            "ColumnName" => "direction"
        ],
        [
            "InputType" => "id",
            "ColumnName" => "tip"
        ],
        [
            "InputType" => "id",
            "ColumnName" => "muraciet_eden"
        ],
        [
            "InputType" => "id",
            "ColumnName" => "mektubun_tipi"
        ],
        [
            "InputType" => "id",
            "ColumnName" => "muraciet_eden_tip_id"
        ],
        [
            "InputType" => "id",
            "ColumnName" => "gonderen_teshkilat"
        ],
    ];
    $incomingDocumentForm = new Form($incomingDocumentFields);
    $incomingDocumentForm->check();

    $formData = $incomingDocumentForm->collectDataToBeInserted();

    $formData['mektubun_tipi']        = zeroToMinusOne($formData['mektubun_tipi']);
    $formData['muraciet_eden_tip_id'] = zeroToMinusOne($formData['muraciet_eden_tip_id']);
    $formData['gonderen_teshkilat']   = zeroToMinusOne($formData['gonderen_teshkilat']);

    $document = new Document(null, [
        'data' => $formData
    ]);
} else if ('outgoing' === $direction) {

    $fields = [
        [
            "InputType" => "id",
            "ColumnName" => "muraciet_tip_id"
        ],
        [
            "InputType" => "id",
            "ColumnName" => "teyinat"
        ],
        [
            "InputType" => "id",
            "ColumnName" => "gonderen_teshkilat"
        ],
        [
            "InputType" => "id",
            "ColumnName" => "gonderen_shexs"
        ],
        [
            "InputType" => "id",
            "ColumnName" => "kim_gonderir"
        ]
    ];
    $form = new Form($fields);
    $form->check();

    $formData = $form->collectDataToBeInserted();

    $formData['muraciet_tip_id']    = zeroToMinusOne($formData['muraciet_tip_id']);
    $formData['teyinat']            = zeroToMinusOne($formData['teyinat']);
    $formData['gonderen_teshkilat'] = zeroToMinusOne($formData['gonderen_teshkilat']);

    $document = new OutgoingDocument(null, [
        'data' => $formData
    ]);
} else if ('internal' === $direction) {
    require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument.php';
    $fields = [
        [
            "InputType" => "text",
            "ColumnName" => "extraId"
        ],
    ];
    $form = new Form($fields);
    $form->check();
    $formData = $form->collectDataToBeInserted();

    $internalDocumentTypeId = $documentType = DB::fetchOneColumnBy('tb_prodoc_inner_document_type', 'id', [
        'extra_id' => $formData['extraId']
    ]);

    $document = new InternalDocument(null, [
        'data' => [
            'internal_document_type_id' => $internalDocumentTypeId
        ]
    ]);
}

if (is_null($document)) {
    $user->error_msg();
}

$documentNumberGeneral = new DocumentNumberGeneral($document, [
    'manualDocumentNumber' => '',
    'editable_with_select' => false
]);

try {
    print json_encode([
        'number'  => $documentNumberGeneral->reserveNumber(),
        'setting' => $documentNumberGeneral->getSetting()->getInfo()
    ]);
} catch (Exception $e) {
    $user->error_msg($e->getMessage());
}

function zeroToMinusOne($value)
{
    if (0 === (int)$value){
        return -1;
    }

    return $value;
}