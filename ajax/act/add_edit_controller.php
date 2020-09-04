<?php

session_start();
include_once '../../../class/class.functions.php';
include_once DIRNAME_INDEX . 'modules/module_builder/model.php';
include_once DIRNAME_INDEX . 'prodoc/component/Form.php';
include_once DIRNAME_INDEX . 'prodoc/model/Exception/BaseException.php';
require_once DIRNAME_INDEX . 'prodoc/includes/internal_document.php';
require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument/Act.php';

$user = new User();

if (!$user->get_session()) {
    header("Location: login.php");
    exit;
}

$userId = (int)$_SESSION['erpuserid'];

$id = getInt('document_id');


$fields = [
    [
        "IsRequired" => false,
        "InputType" => "text",
        "ColumnName" => "act_type",
    ],
    [
        "InputType" => "date",
        "ColumnName" => "created_at",
    ],
    [
        "InputType" => "id",
        "ColumnName" => "sheher_id",
        "Title" => "Şəhər"
    ],

    [
        "InputType" => "id",
        "ColumnName" => "kend_id",
    ],
    [
        "InputType" => "text",
        "ColumnName" => "nov",
    ],
    [
        "InputType" => "id",
        "ColumnName" => "miqdar",
    ],
    [
        "InputType" => "text",
        "ColumnName" => "yerustu_erazi",
    ],
    [
        "InputType" => "text",
        "ColumnName" => "yeralti_erazi",
    ],
    [
        "InputType" => "text",
        "ColumnName" => "neqliyyat_nomresi",
    ],
    [
        "InputType" => "text",
        "ColumnName" => "mehv_etme",
    ],
    [
        "IsRequired"=>true,
        "Title"=> "Qeyd",
        "InputType" => "text",
        "ColumnName" => "qeyd",
    ],
    [
        "InputType" => "id",
        "ColumnName" => "tehvil_veren",
    ],
    [
        "InputType" => "text",
        "ColumnName" => "tehvil_alan_orqan",
    ],
    [
        "InputType" => "text",
        "ColumnName" => "tehvil_alan_shexs",
    ],
    [
        "InputType" => "id",
        "ColumnName" => "task_command_id",
    ],
];



$qurgular=[

    [
        "InputType" => "array",
        "ColumnName" => "tipi",
    ],
    [
        "InputType" => "array",
        "ColumnName" => "chapi",
    ],
    [
        "InputType" => "array",
        "ColumnName" => "novu",
    ],
    [
        "InputType" => "arrayOfIds",
        "ColumnName" => "miqdari",
    ],
    [
        "InputType" => "array",
        "ColumnName" => "qeydi",
    ]
];

$imzalayan_shexsler= [
  [
      "InputType"=>"array",
      "ColumnName"=>"imzalayan_shexs"
  ]
];
$daxilOlanSenedFields = [
    [
        "Title" => "Sənəd",
        "InputType" => "file",
        "ColumnName" => "sened_fayl"
    ]
];

$form = new Form($fields);
$form->check();
$dataToBeInserted = $form->collectDataToBeInserted();

$qurgular_form = new Form($qurgular);
$qurgular_form->check();
$qurgularToBeInserted = $qurgular_form->collectDataToBeInserted();

$daxilOlanSenedForm = new Form($daxilOlanSenedFields);
$daxilOlanSenedForm->check();
$dataToBeInsertedDaxilOlanSened = $daxilOlanSenedForm->collectDataToBeInserted();

$imzalayanlar = new Form($imzalayan_shexsler);
$imzalayanlar->check();
$dataToBeInsertedImzalayanlar = $imzalayanlar->collectDataToBeInserted();

try {
    pdo()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    pdo()->beginTransaction();

    if ($id) {
        $poa = new \Model\InternalDocument\Act($id);
        $poa->edit($dataToBeInserted);
        $report_id = DB::fetchColumn("SELECT id FROM tb_prodoc_aktlar WHERE document_id =".$id);
        DB::query("DELETE FROM tb_prodoc_akt_tipleri WHERE internal_document_id=".$report_id);
        foreach ($qurgularToBeInserted['qurgunun_novu'] as $key=> $qurgu){

            SQL::insert('tb_prodoc_akt_tipleri', [

                'tipi'  => $qurgularToBeInserted['tipi'][$key],
                'chapi'   =>       $qurgularToBeInserted['chapi'][$key],
                'miqdari'    =>    (int)$qurgularToBeInserted['miqdari'][$key],
                'novu'    =>   $qurgularToBeInserted['novu'][$key],
                'qeydi'    =>      $qurgularToBeInserted['qeydi'][$key],
                'internal_document_id'    =>    $report_id
            ]);

        }

    } else {
        $document = $user->createInternalDocumentNumber(
            NULL,
            'create_act',
            NULL,
            true
        );
        $dataToBeInserted['document_id'] = $document->getId();

        $tapsiriqEmrindenDushub = (int)$dataToBeInserted['task_command_id'] > 0;
        if ($tapsiriqEmrindenDushub) {
            // vars required for createRelation
            $_POST['related_document_menu'] = ['incoming'];
            $_POST['related_document_id'] = [$dataToBeInserted['task_command_id']];
            $_POST['ishe_tik'] = ['0'];
            $_POST['netice'] = ['0'];
            $_POST['bind_to_document'] = '1';
        } else {
            $teler = elaqeliSenedleriGotur('internal', 'task_command');
            $telerinSayi = count($teler);

            if ($telerinSayi === 0) {
                throw new BaseException('Tapşırıq əmrini seçmədiniz!');
            }

            if ($telerinSayi > 1) {
                throw new BaseException('Birdən çox tapşırıq əmri seçə bilmərsiniz!');
            }

            if (!$tapsiriqEmrindenDushub) {
                $dataToBeInserted['task_command_id'] = $teler[0];
            }
        }

        $pts = \Model\InternalDocument\Act::create($dataToBeInserted, $user);

        foreach ($dataToBeInsertedImzalayanlar['imzalayan_shexs'] as $imzalayan_shexs){

            SQL::insert('tb_prodoc_akt_imzalayan_shexsler', [

                'user_id'  => $imzalayan_shexs,
                'document_id'   =>       $document->getId(),
                'internal_document_id' => $pts->getInternalDocumentId()
            ]);

        }

        createRelation($document->getId());
        $last_id=DB::fetchColumn("SELECT MAX(id) FROM tb_prodoc_aktlar");

        foreach ($qurgularToBeInserted['tipi'] as $key=> $qurgu){

            SQL::insert('tb_prodoc_akt_tipleri', [

                'tipi'  => $qurgularToBeInserted['tipi'][$key],
                'chapi'   =>       $qurgularToBeInserted['chapi'][$key],
                'miqdari'    =>    isset($qurgularToBeInserted['miqdari'][$key]) ? (int)$qurgularToBeInserted['miqdari'][$key] : '0',
                'novu'    =>   $qurgularToBeInserted['novu'][$key],
                'qeydi'    =>      $qurgularToBeInserted['qeydi'][$key],
                'internal_document_id'    =>    $last_id
            ]);

        }

        $id= $pts->getInternalDocumentId();
        $documentId = $document->getId();
        $daxilOlanSenedForm->saveFiles($documentId, 'daxil_olan_senedler', PRODOC_FILES_SAVE_PATH);
    }

    pdo()->commit();
    print json_encode([
        'status' => 'success',
        'id' => $documentId
    ]);
    exit();
} catch (BaseException $e) {
    print json_encode([
        'status' => 'error',
        'errors' => [nl2br($e->getMessage())]
    ]);
} catch (Exception $e) {
    print json_encode([
        'status' => 'error',
        'errors' => ['Səhv var' . $e->getMessage()]
    ]);
} finally {
    pdo()->rollBack();
}