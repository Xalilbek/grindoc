<?php
session_start();
include_once '../../../class/class.functions.php';
require_once DIRNAME_INDEX . 'prodoc/model/TestiqleyecekShexs.php';
require_once DIRNAME_INDEX . 'prodoc/service/Confirmation.php';
require_once DIRNAME_INDEX . '/modules/module_builder/model.php';
require_once DIRNAME_INDEX . '/prodoc/component/Form.php';

$user = new User();

if(!$user->get_session()) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['erpuserid'];

$testiqleme_id = get('id');

try {
    DB::beginTransaction();
    $testiqleme = DB::fetchById('tb_prodoc_formlar_tesdiqleme', $testiqleme_id);

    if (FALSE === $testiqleme) {
        throw new Exception('T not found');
    }

    $requireApprove = true;

    if ($testiqleme['emeliyyat_tip'] === 'umumi_shobe_netice') {
        $form = [
            [
                "IsRequired" => true,
                "InputType" => "id",
                "ColumnName" => "netice",
                "Title" => "Nəticə"
            ]
        ];

        $daxilOlanSenedForm = new Form($form);
        $daxilOlanSenedForm->check();
        $dataToBeInsertedDaxilOlanSened = $daxilOlanSenedForm->collectDataToBeInserted();

        $id = $testiqleme['daxil_olan_sened_id'];
        DB::update('tb_daxil_olan_senedler', [
            'netice' => $dataToBeInsertedDaxilOlanSened['netice']
        ], $id);
    }
    else if ($testiqleme['emeliyyat_tip'] === 'qiymetlendirme' && isset($_POST['mallarObj'])) {
        require_once DIRNAME_INDEX . 'prodoc/includes/internal_document.php';
        $id = $testiqleme['daxil_olan_sened_id'];

        $mallarObj = json_decode($_POST['mallarObj'], true);
        evaluation($mallarObj);
    }
    else if ($testiqleme['emeliyyat_tip'] === 'neticeni_qeyd_eden_sexs') {

        $tam_tehvil = getInt('tam_tehvil');
        if ($tam_tehvil === 0) {
            $requireApprove = false;
        }

        $form = [
            [
                "InputType" => "array",
                "ColumnName" => "malin_id"
            ],
            [
                "InputType" => "array",
                "ColumnName" => "miqdar"
            ],
            [
                "InputType" => "array",
                "ColumnName" => "serh"
            ],
        ];

        $satinAlmaSenedForm = new Form($form);
        $satinAlmaSenedForm->check();
        $satinAlmaInsertedSened = $satinAlmaSenedForm->collectDataToBeInserted();

        $malin_id  = $satinAlmaInsertedSened['malin_id'];
        $miqdar    = $satinAlmaInsertedSened['miqdar'];
        $serh      = $satinAlmaInsertedSened['serh'];

        $tableDatasCount = count($malin_id);

        for ($i = 0; $i < $tableDatasCount; $i++)
        {
            $satinAlmaInsertedSened['malin_id']    = $malin_id[$i];
            $satinAlmaInsertedSened['miqdar']      = $miqdar[$i];
            $satinAlmaInsertedSened['serh']        = $serh[$i];

            DB::update('tb_prodoc_satinalma_sifaris_xidmet', [
                    'netice_miqdar'  => $satinAlmaInsertedSened['miqdar'],
                    'netice_serh'    => $satinAlmaInsertedSened['serh'],
                ], $satinAlmaInsertedSened['malin_id']);

            $name =  sprintf("document_%s", $i);
            if (!isset($_FILES[$name])) {
                continue;
            }

            $files = saveFiles($name, PRODOC_FILES_SAVE_PATH, false);
            for ($j = 0, $lenj = count($files); $j < $lenj; ++$j) {
                SQL::insert('tb_files', [
                    'module_name'           => 'satinAlma_netice',
                    'module_entry_id'       => $satinAlmaInsertedSened['malin_id'],
                    'file_original_name'    => $files[$j]['file_original_name'],
                    'file_actual_name'      => $files[$j]['file_actual_name'],
                    'created_by'            => (int)$_SESSION['erpuserid'],
                ]);
            }
        }

        $id = $testiqleme['daxil_olan_sened_id'];
        DB::query("UPDATE tb_daxil_olan_senedler SET state=".Document::STATE_NONE." WHERE state = ".Document::STATE_CANCELED." AND id = ".$id);
    }

    if ($requireApprove) {
        $_POST['testiq_id']  = $testiqleme_id;
        ob_start();
        require_once DIRNAME_INDEX . 'ajax/prodoc/formlar/form_approve.php';
        ob_clean();
    }

    DB::commit();
    $user->success_msg();
} catch (Exception $e) {
    DB::rollBack();
    $user->error_msg($e->getMessage());
}
