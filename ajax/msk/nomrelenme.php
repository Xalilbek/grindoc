<?php
session_start();
include_once '../../../class/class.functions.php';
include_once DIRNAME_INDEX . 'modules/module_builder/model.php';
include_once DIRNAME_INDEX . 'prodoc/component/Form.php';
include_once DIRNAME_INDEX . 'prodoc/model/DocumentNumber/DocumentNumberGeneral/Setting.php';
include_once DIRNAME_INDEX . 'prodoc/Util/ArrayUtils.php';

use Model\DocumentNumber\DocumentNumberGeneral\Setting;

$user = new User();

if(!$user->get_session())
{
    print "daxil_olmayib";
    exit();
}

$pr = (int)$user->checkPrivilegia("msk:msk_prodoc_nomrelenme");
if ($pr !== 2)
{
    print json_encode(array("status"=>"hazir","template"=>htmlspecialchars('<div>Olmaz!</div>',ENT_QUOTES)));
    exit();
}

$direction = get('direction');

$formFields = [
    [
        "InputType" => "id",
        "ColumnName" => "id"
    ],
    [
        "InputType" => "id",
        "ColumnName" => "option_id",
        "IsRequired" => true,
        "Title" => "Seçimlər"
    ],
    [
        "IsRequired" => true,
        "InputType" => "commaSeparatedIds",
        "ColumnName" => "option_values",
        "Title" => "Seçim tipi"
    ],
    [
        "IsRequired" => $direction !== 'internal',
        "InputType" => "commaSeparatedIds",
        "ColumnName" => "document_types",
        "Title" => "Sənəd növü"
    ],
    [
        "IsRequired" => true,
        "InputType" => "text",
        "ColumnName" => "pattern_prefix",
        "Title" => "Nömrənin prefiksi"
    ],
    [
        "IsRequired" => true,
        "InputType" => "text",
        "ColumnName" => "pattern",
        "Title" => "Nömrə"
    ],
    [
        "InputType" => "text",
        "ColumnName" => "direction"
    ]
];

$form = new Form($formFields);
$form->check();
$formData = $form->collectDataToBeInserted();

try {
    if (false === mb_strpos($formData['pattern'], '$seria$')) {
        $user->error_msg(
            sprintf('Daxil etdiyiniz nömrədə (%s) seria ($seria$) qeyd olunmayıb',
                htmlspecialchars($formData['pattern'])
            )
        );
    }

    if (!in_array($formData['direction'], ['incoming', 'outgoing', 'internal'], true)) {
        throw new Exception();
    }

    $duplciations = Setting::getDuplications($formData);

    $duplciationIdList = [];
    foreach ($duplciations as $duplciation) {
        $duplciationIdList[] = $duplciation->getId();
    }

    if ($formData['id']) {
        $duplciationIdList = \Util\ArrayUtils::removeByValue($duplciationIdList, $formData['id']);
    }

    if (count($duplciationIdList)) {
        $user->error_msg(
            "Bu nömrələnmə daha oncə əlavə olunub!",
            ['duplications' => $duplciationIdList]
        );
    }

    if ($formData['id']) {
        $setting = new Setting($formData['id']);
        $setting->edit($formData, false);
    } else {
        $setting = Setting::create($formData, $user, false);
    }
} catch (Exception $e) {
    $user->error_msg($e->getMessage());
}

print json_encode([
    'status' => 'success',
    'id' => $setting->getId()
]);