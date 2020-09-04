<?php

session_start();
include_once '../../../class/class.functions.php';
include_once DIRNAME_INDEX . 'modules/module_builder/model.php';
include_once DIRNAME_INDEX . 'prodoc/component/Form.php';
include_once DIRNAME_INDEX . 'prodoc/model/Exception/BaseException.php';
require_once DIRNAME_INDEX . 'prodoc/includes/internal_document.php';
require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument/PartlamamishTekSursat.php';

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
        "InputType" => "id",
        "ColumnName" => "te_id",
    ],
    [
        "InputType" => "id",
        "ColumnName" => "hesabat_id",
    ],
    [
        "InputType" => "text",
        "ColumnName" => "te_adi",
    ],

    [
        "InputType" => "date",
        "ColumnName" => "hesabatin_tarixi",
    ],
    [
        "InputType" => "id",
        "ColumnName" => "rayon_id",
    ],
    [
        "InputType" => "text",
        "ColumnName" => "bashlangic_noqtenin_koordinatlari",
    ],
    [
        "InputType" => "text",
        "ColumnName" => "sherq",
    ],
    [
        "InputType" => "text",
        "ColumnName" => "shimal",
    ],
    [
        "InputType" => "id",
        "ColumnName" => "dkps",
    ],
    [
        "InputType" => "id",
        "ColumnName" => "kps",
    ],
    [
        "InputType" => "text",
        "ColumnName" => "phs_tesviri",
    ],
    [
        "InputType" => "id",
        "ColumnName" => "veziyyet_zererleshdirilmish",
    ],
    [
        "InputType" => "id",
        "ColumnName" => "veziyyet_isharelenmish",
    ],
    [
        "InputType" => "id",
        "ColumnName" => "veziyyet_aparilmish",
    ],
    [
        "InputType" => "text",
        "ColumnName" => "aparilmish_qurgunun_xususiyyetleri",
    ],
    [
        "InputType" => "text",
        "ColumnName" => "chirklenmish_erazinin_tesviri",
    ],
    [
        "InputType" => "id",
        "ColumnName" => "veziyyet_aparilmish",
    ],

];

$qurgular=[

  [
      "InputType" => "array",
      "ColumnName" => "qurgunun_novu",
  ],
  [
      "InputType" => "array",
      "ColumnName" => "modeli",
  ],
  [
      "InputType" => "array",
      "ColumnName" => "miqdari",
  ],
  [
      "InputType" => "arrayOfIds",
      "ColumnName" => "surprizli",
  ],
  [
      "InputType" => "arrayOfIds",
      "ColumnName" => "tele_meftili",
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

try {
    pdo()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    pdo()->beginTransaction();


   $dataToBeInserted['sheher_id'] = getInt('sheher_rayon_id');

    if ($id) {
        $poa = new \Model\InternalDocument\PartlamamishTekSursat($id);
        $poa->edit($dataToBeInserted);
        $report_id = DB::fetchColumn("SELECT id FROM tb_prodoc_partlamamish_tek_sursat WHERE document_id =".$id);
        DB::query("DELETE FROM tb_prodoc_partlamamish_tek_sursat_qurgular WHERE sened_id=".$report_id);
        foreach ($qurgularToBeInserted['qurgunun_novu'] as $key=> $qurgu){
            SQL::insert('tb_prodoc_partlamamish_tek_sursat_qurgular', [

                'qurgunun_novu'  => $qurgularToBeInserted['qurgunun_novu'][$key],
                'modeli'   =>       $qurgularToBeInserted['modeli'][$key],
                'miqdari'    =>     $qurgularToBeInserted['miqdari'][$key],
                'surprizli'    =>   $qurgularToBeInserted['surprizli'][$key],
                'tele_meftili'    =>      $qurgularToBeInserted['tele_meftili'][$key],
                'sened_id'    =>    $report_id
            ]);

        }
    } else {
        $document = $user->createInternalDocumentNumber(
            NULL,
            'hesabat_yarat',
            NULL,
            true
        );
        $dataToBeInserted['document_id'] = $document->getId();

        $tapsiriqEmrindenDushub = (int)$dataToBeInserted['te_id'] > 0;
        if ($tapsiriqEmrindenDushub) {
            // vars required for createRelation
            $_POST['related_document_menu'] = ['incoming'];
            $_POST['related_document_id'] = [$dataToBeInserted['te_id']];
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
                $dataToBeInserted['te_id'] = $teler[0];
            }
        }

        $pts = \Model\InternalDocument\PartlamamishTekSursat::create($dataToBeInserted, $user);

        createRelation($document->getId());
        $last_id=DB::fetchColumn("SELECT MAX(id) FROM tb_prodoc_partlamamish_tek_sursat");
        foreach ($qurgularToBeInserted['qurgunun_novu'] as $key=> $qurgu){

            SQL::insert('tb_prodoc_partlamamish_tek_sursat_qurgular', [

                'qurgunun_novu'  => $qurgularToBeInserted['qurgunun_novu'][$key],
                    'modeli'   =>       $qurgularToBeInserted['modeli'][$key],
                    'miqdari'    =>    $qurgularToBeInserted['miqdari'][$key],
                    'surprizli'    =>   $qurgularToBeInserted['surprizli'][$key],
                    'tele_meftili'    =>      $qurgularToBeInserted['tele_meftili'][$key],
                    'sened_id'    =>    $last_id
            ]);

        }

        $id= $pts->getId();
        $documentId = $document->getId();
        $daxilOlanSenedForm->saveFiles($documentId, 'daxil_olan_senedler', PRODOC_FILES_SAVE_PATH);
    }

    pdo()->commit();
    print json_encode([
        'status' => 'success',
        'id' => $id
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