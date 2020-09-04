<?php
session_start();
include_once '../../../class/class.functions.php';
require_once DIRNAME_INDEX . 'prodoc/Privilegiya.php';
include_once DIRNAME_INDEX . 'modules/module_builder/model.php';
include_once DIRNAME_INDEX . 'prodoc/component/Form.php';
include_once DIRNAME_INDEX . 'prodoc/model/Appeal/Appeal.php';
include_once DIRNAME_INDEX . 'prodoc/model/TestiqleyecekShexs.php';
include_once DIRNAME_INDEX . 'prodoc/model/Task/Task.php';
include_once DIRNAME_INDEX . 'prodoc/service/Confirmation.php';
include_once DIRNAME_INDEX . 'prodoc/model/Appeal/Appeal.php';
include_once DIRNAME_INDEX . 'prodoc/model/OutgoingDocument.php';
include_once DIRNAME_INDEX . 'prodoc/model/DocumentNumber/DocumentNumberGeneral/DocumentNumberGeneral.php';
require_once DIRNAME_INDEX . 'prodoc/includes/outgoing_document.php';
require_once DIRNAME_INDEX . 'prodoc/functions/settings.php';
$user = new User();

use Model\DocumentNumber\DocumentNumberGeneral\DocumentNumberGeneral;

if (!$user->get_session()) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['erpuserid'];
$taskId = getInt('taskId', 0);
$id = getInt('id', 0);

$chixanSenedFields = [
    [
        "Title" => 'İmzalayan şəxs',
        "IsRequired" => true,
        "InputType" => "arrayOfIds",
        "ColumnName" => "kim_gonderir"
    ],
    [
        "InputType" => "text",
        "ColumnName" => "qeyd"
    ],
    [
        "Title" => "Məktubun qısa məzmunu",
        "InputType" => "id",
        "ColumnName" => "qisa_mezmun_id"
    ],
    [
        "IsRequired" => true,
        "InputType" => "arrayOfIds",
        "ColumnName" => "teyinat",
        "Title" => "Təyinat"
    ],
    [
        "IsRequired" => true,
        "InputType" => "arrayOfIds",
        "ColumnName" => "gonderen_teshkilat",
        "Title" => "Göndərən təşkilat"
    ],
    [
        "InputType" => "id",
        "ColumnName" => "poa_user_id"
    ],
    [
        "IsRequired" => true,
        "InputType" => "arrayOfIds",
        "ColumnName" => "gonderen_shexs",
        "Title" => "Göndərən şəxs"
    ],
    [
        "IsRequired" => true,
        "InputType" => "arrayOfIds",
        "ColumnName" => "muraciet_tip_id",
        "Title" => "Sənədin tipi"
    ],
    [
        "IsRequired" => isPdfFileRequired() == true && $id == 0,
        "InputType" => "arrayOfIds",
        "ColumnName" => "sened_fayl_0",
        "IsRequiredErrorMessage" => "Pdf faylı seçilməyib!",
        "customValidation" => 'checkPdfExistense'
    ],
    [
        "InputType" => "arrayOfIds",
        "ColumnName" => "muraciet_alt_tip_id",
        "Title" => "Sənədin alt tipi"
    ],
    [
        "Title" => "Ələvə Nömrə",
        "InputType" => "array",
        "ColumnName" => "eleve_nomre"
    ],
    [
        "Title" => "Vərəqlərin sayı",
        "InputType" => "array",
        "ColumnName" => "icra_muddeti_vereq_sayi"
    ],
    [
        "InputType" => "array",
        "ColumnName" => "tarix"
    ],
    [
        "InputType" => "arrayOfIds",
        "ColumnName" => "arayis_user_id",
        "Title" => "A.S.A:"
    ],
    [
        "InputType" => "array",
        "ColumnName" => "arayis_tarixi"
    ],
    [
        "InputType" => "array",
        "ColumnName" => "arayis_ise_qebul_tarixi"
    ],
    [
        "InputType" => "array",
        "ColumnName" => "etibarname_sexs",
        "Title" => "Şəxs:"
    ],
    [
        "InputType" => "array",
        "ColumnName" => "etibarname_kartin_nomresi",
        "Title" => "Kartın nömrəsi:"
    ],
    [
        "InputType" => "array",
        "ColumnName" => "etibarname_avtomobilin_nomresi",
        "Title" => "Avtomobilin nömrəsi:"
    ],
    [
        "InputType" => "array",
        "ColumnName" => "etibarname_yanacagin_miqdari",
        "Title" => "Yanacağın miqdarı:"
    ],
    [
        "InputType" => "array",
        "ColumnName" => "etibarname_vesiqenin_kodu",
        "Title" => "Şəxsiyyət vəsiqəsinin pin kodu:"
    ],
    [
        "InputType" => "array",
        "ColumnName" => "etibarname_vesiqenin_nomresi",
        "Title" => "Şəxsiyyət vəsiqəsinin nömrəsi:"
    ],
    [
        "InputType" => "array",
        "ColumnName" => "etibarname_vesiqeni_teqdim_eden_orqan",
        "Title" => "Vəsiqəni təqdim edən orqan:"
    ],
    [
        "InputType" => "array",
        "ColumnName" => "etibarname_etibarliq_muddet",
        "Title" => "Etibarlılıq müddəti:"
    ],
    [
        "InputType" => "array",
        "ColumnName" => "etibarname_icraci_direktor",
        "Title" => "İcraçı direktor:"
    ],
    [
        "InputType" => "array",
        "ColumnName" => "etibarname_bas_muhasib",
        "Title" => "Baş mühasib:"
    ],
];

$chixanSenedTipi = [
    [
        "Title" => "Etibarnamənin məqsədi",
        "InputType" => "array",
        "ColumnName" => "etibarnamenin_meqsedi"
    ],
    [
        "Title" => "Harada",
        "InputType" => "array",
        "ColumnName" => "harada"
    ],
    [
        "Title" => "Səlahiyyətli şəxs",
        "InputType" => "array",
        "ColumnName" => "selahiyyetli_user_id"
    ],
    [
        "Title" => "İcraçı direktor",
        "InputType" => "array",
        "ColumnName" => "icraci_direktor"
    ],
    [
        "Title" => "Etibarlılıq müddəti",
        "InputType" => "array",
        "ColumnName" => "etibarliq_muddet"
    ],
];


$chixanSenedForm = new Form($chixanSenedFields);
$chixanSenedForm->check();

$chixanSenedTipi = new Form($chixanSenedTipi);
$chixanSenedTipi->check();

$butunTestiqleyenFields = [
    [
        "IsRequired" => false,
        "InputType" => "arrayOfIds",
        "ColumnName" => TestiqleyecekShexs::TIP_RAZILASHDIRAN
    ],
    [
        "IsRequired" => false,
        "InputType" => "commaSeparatedIds",
        "ColumnName" => TestiqleyecekShexs::TIP_CHAP_EDEN
    ],
    [
        "IsRequired" => false,
        "InputType" => "commaSeparatedIds",
        "ColumnName" => TestiqleyecekShexs::TIP_REDAKT_EDEN
    ],
    [
        "IsRequired" => false,
        "InputType" => "commaSeparatedIds",
        "ColumnName" => TestiqleyecekShexs::TIP_VISA_VEREN
    ],
    [
        "IsRequired" => false,
        "InputType" => "arrayOfIds",
        "ColumnName" => TestiqleyecekShexs::KIM_GONDERIR
    ],
];

$butunTestiqleyenForm = new Form($butunTestiqleyenFields);
$butunTestiqleyenForm->check();

$relatedToIncomingDocument = !!getInt('related_to_incoming_document', 0);
$incomingDocumentForm = new Form([
    [
        "IsRequired" => $relatedToIncomingDocument,
        "IsRequiredErrorMessage" => "Daxil olan sənədi seçmədiniz",
        "InputType" => "arrayOfIds",
        "ColumnName" => "daxil_olan_sened_id"
    ],
    [
        "InputType" => "arrayOfInts",
        "ColumnName" => "document_status"
    ],
    [
        "InputType" => "arrayOfInts",
        "ColumnName" => "document_elaqelendirme"
    ],
]);
$incomingDocumentForm->check();

$numberingFields = [
    [
        "InputType" => "array",
        "ColumnName" => "document_number",
        "IsRequired" => true,
        "Title" => "Sənədin daxil olma №-si"
    ],
    [
        "InputType" => "arrayOfInts",
        "ColumnName" => "editable_with_select"
    ],
    [
        "InputType" => "array",
        "ColumnName" => "reserved_document_number_id"
    ],
    [
        "InputType" => "array",
        "ColumnName" => "is_unique"
    ],
];

$numberingForm = new Form($numberingFields);
$numberingForm->check();

$dataToBeInsertedChixanSened = $chixanSenedForm->collectDataToBeInserted();
$dataToBeInsertedChixanSenedTipi = $chixanSenedTipi->collectDataToBeInserted();
$butunTestiqleyenler = $butunTestiqleyenForm->collectDataToBeInserted();
$incomingDocumentData = $incomingDocumentForm->collectDataToBeInserted();
$numberingData = $numberingForm->collectDataToBeInserted();
$senedTipi = getSenedTipi();
$incomingDocumentIds =$incomingDocumentData['daxil_olan_sened_id'];

try {
    DB::exec('SET TRANSACTION ISOLATION LEVEL SERIALIZABLE');
    pdo()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    pdo()->beginTransaction();
    eleveNomreYoxla($dataToBeInsertedChixanSened);

    $isEdit = $id > 0;

    if ($isEdit) {

        if (count($dataToBeInsertedChixanSened['teyinat']) > 1) {
            throw new Exception('There is an error');
        }

        $dataToChixanSened = getTaskId($id);

        if (FALSE !== $dataToChixanSened && (int)$dataToChixanSened['derkenar_id'] > 0) {
            $taskId = $dataToChixanSened['derkenar_id'];
            $incomingDocumentData['daxil_olan_sened_id'] = [$dataToChixanSened['daxil_olan_sened_id']];
            $incomingDocumentData['document_status'] = [(int)$dataToChixanSened['dos_status']];
            $relatedToIncomingDocument = true;
        }

        deleteOldRelation($id);
        deleteOldApprovingUsers($id);
    }

    $outgoingDocuments = chixanSenedleriYarat($dataToBeInsertedChixanSened, $numberingData, $dataToBeInsertedChixanSenedTipi);
    fayllariYarat($outgoingDocuments);
    $confirmingUsers = getConfirmingUsers($butunTestiqleyenler);

    if ($relatedToIncomingDocument) {
        if ($taskId) {
            $netice = neticeniGotur($incomingDocumentData['daxil_olan_sened_id'][0]);

            if (!isset($incomingDocumentData['document_status'][0])) {
                throw new Exception('Document status error');
            }

            $data = [
                'tip' => Appeal::TIP_SENED_HAZIRLA,
                'daxil_olan_sened_id' => $incomingDocumentData['daxil_olan_sened_id'][0],
                'derkenar_id' => $taskId,
                'netice_id' => $netice,
                'outgoingDocuments' => $outgoingDocuments,
                'dos_status' => getIncomingDocumentStatus($incomingDocumentData['document_status'][0]),
                'document_elaqelendirme' => $incomingDocumentData['document_elaqelendirme'][0]
            ];

            $appeal = Appeal::create($data);
        } else {
            for ($i = 0, $len = count($incomingDocumentData['daxil_olan_sened_id']); $i < $len; ++$i) {
                $taskId = NULL;
                $myTask = getMyTaskByDocumentId($incomingDocumentData['daxil_olan_sened_id'][$i]);

                if (!is_null($myTask)) {
                    $taskId = $myTask['id'];
                }

                $netice = neticeniGotur($incomingDocumentData['daxil_olan_sened_id'][$i]);

                if (!isset($incomingDocumentData['document_status'][$i])) {
                    throw new Exception('Document status error');
                }

                $data = [
                    'tip' => Appeal::TIP_SENED_HAZIRLA,
                    'daxil_olan_sened_id' => $incomingDocumentData['daxil_olan_sened_id'][$i],
                    'derkenar_id' => $taskId,
                    'netice_id' => $netice,
                    'outgoingDocuments' => $outgoingDocuments,
                    'dos_status' => getIncomingDocumentStatus($incomingDocumentData['document_status'][$i]),
                    'document_elaqelendirme' => $incomingDocumentData['document_elaqelendirme'][$i]
                ];

                $appeal = Appeal::create($data, $user);
            }
        }

        $confirmingUsers = $appeal->getConfirmingUsers($confirmingUsers, $incomingDocumentData['daxil_olan_sened_id']);

        for ($i = 0, $len = count($outgoingDocuments); $i < $len; ++$i) {
            $outgoingDocument = $outgoingDocuments[$i];

            $confirmation = new \Service\Confirmation\Confirmation($outgoingDocument);
            $confirmation->createConfirmingUsers($confirmingUsers);
        }

    } else {
        $confirmingUsers = getConfirmingUsersSerbers($confirmingUsers);
        for ($i = 0, $len = count($outgoingDocuments); $i < $len; ++$i) {
            $outgoingDocument = $outgoingDocuments[$i];

            $confirmation = new \Service\Confirmation\Confirmation($outgoingDocument);

            $confirmation->createConfirmingUsers($confirmingUsers);
        }
    }

    foreach ($outgoingDocuments as $doc) {
        $confirmationn = new \Service\Confirmation\Confirmation($doc);
        $currentOrder = $confirmationn->getCurrentOrder();
        $confirmationnn = $confirmationn->getCurrentApprovingUsers();
        $id = $doc->getId();
        if (isset($currentOrder)) {

            $sql = "
                 SELECT user_id,tip,  document_number, (SELECT CONCAT(Adi,' ' ,Soyadi) as user_name FROM tb_users WHERE USERID=v_chixan_senedler.created_by) as user_name, 
                related_record_id, teyinat FROM tb_prodoc_testiqleyecek_shexs LEFT JOIN v_chixan_senedler on 
                v_chixan_senedler.id=related_record_id  WHERE [order] = {$currentOrder} AND related_class = 'OutgoingDocument' AND related_record_id ='$id'
            ";
            $users = DB::fetchAll($sql);

            $mesul_shexsler = pdof()->query("SELECT top 1 t1.*, t2.outgoing_document_id, v_daxil_olan_senedler.created_by, rey_muellifi,
            ( SELECT mesul_shexs FROM tb_derkenar WHERE id = ( SELECT parentTaskId FROM tb_derkenar WHERE id = derkenar_id AND parentTaskId > 0 ) ) AS mesul_shexs,
            (SELECT CONCAT(Adi,' ' ,Soyadi) from tb_users where USERID=(Select mesul_shexs from tb_derkenar 
            WHERE id=(SELECT parentTaskId FROM tb_derkenar WHERE id=derkenar_id and parentTaskId>0 ) ))
             as mesul_shexs_ad  FROM
            tb_prodoc_muraciet AS t1 LEFT JOIN tb_prodoc_appeal_outgoing_document AS t2 ON t1.id = t2.appeal_id LEFT JOIN v_daxil_olan_senedler on v_daxil_olan_senedler.id=daxil_olan_sened_id 
    WHERE
        t2.outgoing_document_id = '" . $id . "' 
    ORDER BY
        t2.outgoing_document_id DESC")->fetch();


            $tip = "";

            foreach ($users as $tUser) {
                if ($tUser['tip'] == 'mesul_shexs' && $mesul_shexsler['mesul_shexs'] != 0 && $mesul_shexsler['mesul_shexs'] != '' && $mesul_shexsler['mesul_shexs'] != null) {

                    switch ($tUser['teyinat']) {
                        case '3':
                            $tip .= "aidiyyati_orqan";
                            break;
                        case '4':
                            $tip .= "fiziki_shexs";
                            break;
                        case '5':
                            $tip .= "tabeli_qurum";
                            break;
                        default:
                            exit();
                            break;
                    }
                    $user->sendNotifications(true, true,
                        $tip,
                        $tUser['user_name'], "",
                        $tUser['related_record_id'],
                        $mesul_shexsler['mesul_shexs'],
                        $tip,
                        "",
                        "",
                        "",
                        $tUser['document_number'],
                        "xaric_olan_sened",
                        "derkenar"
                    );

                    $user->sendNotifications(true, true,
                        $tip . "_qeydiyyatci",
                        $tUser['user_name'], $mesul_shexsler['mesul_shexs_ad'],
                        $tUser['related_record_id'],
                        $mesul_shexsler['created_by'],
                        $tip . "_qeydiyyatci",
                        "",
                        "",
                        "",
                        $tUser['document_number'],
                        "xaric_olan_sened",
                        "derkenar"
                    );


                } else if ($tUser['tip'] == TestiqleyecekShexs::TIP_REY_MUELIFI) {

                    switch ($tUser['teyinat']) {
                        case '3':
                            $tip .= "aidiyyati_orqan";
                            break;
                        case '4':
                            $tip .= "fiziki_shexs";
                            break;
                        case '5':
                            $tip .= "tabeli_qurum";
                            break;
                        default:
                            exit();
                            break;
                    }

                    $user->sendNotifications(true, true,
                        $tip,
                        $tUser['user_name'], "",
                        $tUser['related_record_id'],
                        $mesul_shexsler['rey_muellifi'],
                        $tip,
                        "",
                        "",
                        "",
                        $tUser['document_number'],
                        "xaric_olan_sened",
                        "derkenar"
                    );

                    $user->sendNotifications(true, true,
                        $tip . "_qeydiyyatci",
                        $tUser['user_name'], $mesul_shexsler['mesul_shexs_ad'],
                        $tUser['related_record_id'],
                        $mesul_shexsler['created_by'],
                        $tip . "_qeydiyyatci",
                        "",
                        "",
                        "",
                        $tUser['document_number'],
                        "xaric_olan_sened",
                        "derkenar"
                    );


                }
            }
        }
    }

    pdo()->commit();
    $responseArr = array('sened_id' => $id);
    $user->success_msg("Ok!", $responseArr);

} catch (Exception $e) {
    pdo()->rollBack();

    print json_encode([
        'status' => 'error',
        'errors' => [
            $e->getMessage()
        ]
    ]);
    exit();
}

function eleveNomreYoxla($dataToBeInsertedChixanSened)
{
    $eleve_nomre = $dataToBeInsertedChixanSened['eleve_nomre'];

    if (!empty($eleve_nomre[0])) {
        foreach ($eleve_nomre as $value) {
            $prev = preg_match("/^\d{1,6}$/", $value);
            if (!$prev) {
                throw new Exception('Ələvə Nömrə düz qeyd etmədniz!');
            }
        }
    }
}

function getConfirmingUsersSerbers(array $existingConfirmingUsers = [])
{
    if (getProjectName() === TS || getProjectName() === AP) {
        $reyMuelifi = (int)DB::fetchOneColumnBy('tb_prodoc_icrachi_shexsler', 'user_id', [
            'icrachi_tip' => 'rey_muellifi'
        ]);

        $newConfirmingUsers = [];

        $sender = [];
        foreach ($existingConfirmingUsers as $confirmingUser) {
            if ($confirmingUser['type'] === "kim_gonderir") {
                $sender = (int)$confirmingUser['user_id'];

                if ($reyMuelifi !== $sender) {
                    $newConfirmingUsers[] = [
                        'type' => 'kim_gonderir',
                        'user_id' => $sender,
                        'order' => 2
                    ];

                    $newConfirmingUsers[] = [
                        'type' => 'kim_gonderir',
                        'user_id' => $sender,
                        'order' => 5
                    ];
                }
            } else {
                $newConfirmingUsers[] = $confirmingUser;
            }
        }

        if ($reyMuelifi > 0) {
            $newConfirmingUsers[] = [
                'type' => 'sedr',
                'user_id' => $reyMuelifi,
                'order' => 4
            ];
        }

        $newConfirmingUsers[] = [
            'type' => TestiqleyecekShexs::TIP_UMUMI_SHOBE,
            'user_id' => NULL,
            'order' => 3
        ];

        $newConfirmingUsers[] = [
            'type' => TestiqleyecekShexs::TIP_UMUMI_SHOBE,
            'user_id' => NULL,
            'order' => 6
        ];

        return $newConfirmingUsers;
    } else {
        return $existingConfirmingUsers;
    }
}

function chixanSenedleriYarat($dataToBeInsertedChixanSened, $numberingData, $dataToBeInsertedSenedTipi)
{
    global $user, $senedTipi, $id, $incomingDocumentIds;
    unset($dataToBeInsertedChixanSened['sened_fayl_0']);
    $teyinatlar = $dataToBeInsertedChixanSened['teyinat'];
    $gonderen_teshkilatlar = $dataToBeInsertedChixanSened['gonderen_teshkilat'];
    $gonderen_shexslar = $dataToBeInsertedChixanSened['gonderen_shexs'];
    $muraciet_tip_id = $dataToBeInsertedChixanSened['muraciet_tip_id'];
    $muraciet_alt_tip_id = $dataToBeInsertedChixanSened['muraciet_alt_tip_id'];
    $eleve_nomre = $dataToBeInsertedChixanSened['eleve_nomre'];
    $icra_muddeti_vereq_sayi = $dataToBeInsertedChixanSened['icra_muddeti_vereq_sayi'];
    $arayis_user_id = $dataToBeInsertedChixanSened['arayis_user_id'];
    $arayis_tarixi = $dataToBeInsertedChixanSened['arayis_tarixi'];
    $arayis_ise_qebul_tarixi = $dataToBeInsertedChixanSened['arayis_ise_qebul_tarixi'];
    $etibarname_sexs = $dataToBeInsertedChixanSened['etibarname_sexs'];
    $etibarname_kartin_nomresi = $dataToBeInsertedChixanSened['etibarname_kartin_nomresi'];
    $etibarname_avtomobilin_nomresi = $dataToBeInsertedChixanSened['etibarname_avtomobilin_nomresi'];
    $etibarname_yanacagin_miqdari = $dataToBeInsertedChixanSened['etibarname_yanacagin_miqdari'];
    $etibarname_vesiqenin_kodu = $dataToBeInsertedChixanSened['etibarname_vesiqenin_kodu'];
    $etibarname_vesiqenin_nomresi = $dataToBeInsertedChixanSened['etibarname_vesiqenin_nomresi'];
    $etibarname_vesiqeni_teqdim_eden_orqan = $dataToBeInsertedChixanSened['etibarname_vesiqeni_teqdim_eden_orqan'];
    $etibarname_etibarliq_muddet = $dataToBeInsertedChixanSened['etibarname_etibarliq_muddet'];
    $etibarname_icraci_direktor = $dataToBeInsertedChixanSened['etibarname_icraci_direktor'];
    $etibarname_bas_muhasib = $dataToBeInsertedChixanSened['etibarname_bas_muhasib'];
    $tarix = $dataToBeInsertedChixanSened['tarix'];
    $document_number = $numberingData['document_number'];
    $editable_with_select = $numberingData['editable_with_select'];
    $is_unique = $numberingData['is_unique'];
    $reserved_document_number_id = $numberingData['reserved_document_number_id'];

    $tableDatasCount = count($teyinatlar);

    $outgoingDocuments = [];
    $errors = [];

    $settingAndRelatedDocumentNumberId = [];
    for ($i = 0; $i < $tableDatasCount; $i++) {
        $dataToBeInsertedChixanSened['teyinat'] = $teyinatlar[$i];
        $dataToBeInsertedChixanSened['gonderen_teshkilat'] = $gonderen_teshkilatlar[$i];
        $dataToBeInsertedChixanSened['gonderen_shexs'] = $gonderen_shexslar[$i];
        $dataToBeInsertedChixanSened['muraciet_tip_id'] = $muraciet_tip_id[$i];
        $dataToBeInsertedChixanSened['muraciet_alt_tip_id'] = (isset($muraciet_alt_tip_id[$i]) ? $muraciet_alt_tip_id[$i] : NULL);
        $dataToBeInsertedChixanSened['eleve_nomre'] = (isset($eleve_nomre[$i]) ? $eleve_nomre[$i] : NULL);
        $dataToBeInsertedChixanSened['icra_muddeti_vereq_sayi'] = (isset($icra_muddeti_vereq_sayi[$i]) ? $icra_muddeti_vereq_sayi[$i] : NULL);

        $dataToBeInsertedChixanSened['arayis_user_id'] = (isset($arayis_user_id[$i]) ? $arayis_user_id[$i] : NULL);
        $dataToBeInsertedChixanSened['arayis_tarixi'] = convertValueToSQLFormat('text.date', $arayis_tarixi[$i]);
        $dataToBeInsertedChixanSened['tarix']         = convertValueToSQLFormat('text.date', $tarix[$i]);
        $dataToBeInsertedChixanSened['arayis_ise_qebul_tarixi'] = convertValueToSQLFormat('text.date', $arayis_ise_qebul_tarixi[$i]);
        $dataToBeInsertedChixanSened['etibarname_sexs'] = (!empty($etibarname_sexs[$i]) ? $etibarname_sexs[$i] : NULL);
        $dataToBeInsertedChixanSened['etibarname_kartin_nomresi'] = (!empty($etibarname_kartin_nomresi[$i]) ? $etibarname_kartin_nomresi[$i] : NULL);
        $dataToBeInsertedChixanSened['etibarname_avtomobilin_nomresi'] = (!empty($etibarname_avtomobilin_nomresi[$i]) ? $etibarname_avtomobilin_nomresi[$i] : NULL);
        $dataToBeInsertedChixanSened['etibarname_yanacagin_miqdari'] = (!empty($etibarname_yanacagin_miqdari[$i]) ? $etibarname_yanacagin_miqdari[$i] : NULL);
        $dataToBeInsertedChixanSened['etibarname_vesiqenin_kodu'] = (!empty($etibarname_vesiqenin_kodu[$i]) ? $etibarname_vesiqenin_kodu[$i] : NULL);
        $dataToBeInsertedChixanSened['etibarname_vesiqenin_nomresi'] = (!empty($etibarname_vesiqenin_nomresi[$i]) ? $etibarname_vesiqenin_nomresi[$i] : NULL);
        $dataToBeInsertedChixanSened['etibarname_vesiqeni_teqdim_eden_orqan'] = (!empty($etibarname_vesiqeni_teqdim_eden_orqan[$i]) ? $etibarname_vesiqeni_teqdim_eden_orqan[$i] : NULL);
        $dataToBeInsertedChixanSened['etibarname_etibarliq_muddet'] = (!empty($etibarname_etibarliq_muddet[$i]) ? $etibarname_etibarliq_muddet[$i] : NULL);
        $dataToBeInsertedChixanSened['etibarname_icraci_direktor'] = (!empty($etibarname_icraci_direktor[$i]) ? $etibarname_icraci_direktor[$i] : NULL);
        $dataToBeInsertedChixanSened['etibarname_bas_muhasib'] = (!empty($etibarname_bas_muhasib[$i]) ? $etibarname_bas_muhasib[$i] : NULL);

        if ($senedTipi['arayis']['id'] == $muraciet_tip_id[$i] && is_null($dataToBeInsertedChixanSened['arayis_user_id'])) {
            throw new Exception('A.S.A qeyd etmədiniz!');
        }

        if (($senedTipi['etibarname']['id'] == $muraciet_tip_id[$i])) {
            if (is_null($dataToBeInsertedChixanSened['etibarname_sexs'])) {
                $errors[] = 'Şəxs qeyd etmədiniz!';
            }

            if (is_null($dataToBeInsertedChixanSened['etibarname_kartin_nomresi'])) {
                $errors[] = 'Kartın nömrəsi qeyd etmədiniz!';
            }
            if (is_null($dataToBeInsertedChixanSened['etibarname_avtomobilin_nomresi'])) {
                $errors[] = 'Avtomobilin nömrəsi qeyd etmədiniz!';
            }
            if (is_null($dataToBeInsertedChixanSened['etibarname_yanacagin_miqdari'])) {
                $errors[] = 'Yanacağın miqdarı qeyd etmədiniz!';
            }

            if (count($errors) > 0) {
                print json_encode([
                    'status' => 'error',
                    'errors' => $errors
                ]);
                exit();
            }
        }

        $dataToBeInsertedChixanSened['qeyd'] = get('qeyd');

        if ($id) {
            $outgoingDocument = new OutgoingDocument((int)$id);

            if (!$outgoingDocument->duzelishEdeBiler()) {
                throw new Exception();
            }

            unset($dataToBeInsertedChixanSened['poa_user_id']);

            editTableEtibarname($dataToBeInsertedSenedTipi, $id, $i);
            $outgoingDocument->edit($dataToBeInsertedChixanSened);
        } else {
            $outgoingDocument = OutgoingDocument::createWithAdditionalParams($dataToBeInsertedChixanSened, $user, ['note' => $dataToBeInsertedChixanSened['qeyd'],'daxil_olan_sened_id'=>$incomingDocumentIds]);

            if (($senedTipi['etibarname_esas']['id'] == $muraciet_tip_id[$i]))
            {
                createTableEtibarname($dataToBeInsertedSenedTipi, $outgoingDocument->getId(), $i);
            }
        }

        if (getProjectName() !== TS && getProjectName() !== AP && getProjectName() !== AZERCOSMOS) {
            $outgoingDocumentData = $outgoingDocument->getData();
            if ((int)$outgoingDocumentData['document_number_id'] === 0) {
                $documentNumberGeneral = new DocumentNumberGeneral($outgoingDocument, [
                    'manualDocumentNumber' => $document_number[$i],
                    'editable_with_select' => $editable_with_select[$i],
                ]);
                $setting = $documentNumberGeneral->getSetting();

                $isCurrentDocumentNumberUnique = (int)$is_unique[$i];
                $isCurrentDocumentNumberReserved = (int)$reserved_document_number_id[$i] > 0;

                if ($isCurrentDocumentNumberUnique) {
                    if ($isCurrentDocumentNumberReserved) {
                        // number reserved
                        $documentNumberId = (int)$reserved_document_number_id[$i];
                        $documentNumberGeneral->assignReservedNumber($documentNumberId);
                    } else {
                        $documentNumberId = $documentNumberGeneral->assignNumber();
                    }
                } else {
                    if (array_key_exists($setting->getId(), $settingAndRelatedDocumentNumberId)) {
                        DB::update(OutgoingDocument::TABLE_NAME, [
                            'document_number_id' => $settingAndRelatedDocumentNumberId[$setting->getId()]
                        ], $outgoingDocument->getId());

                        $documentNumberId = $settingAndRelatedDocumentNumberId[$setting->getId()];
                    } else {
                        if ($isCurrentDocumentNumberReserved) {
                            // number reserved
                            $documentNumberId = (int)$reserved_document_number_id[$i];
                            $settingAndRelatedDocumentNumberId[$setting->getId()] = $documentNumberId;

                            $documentNumberGeneral->assignReservedNumber($documentNumberId);
                        } else {
                            $documentNumberId = $documentNumberGeneral->assignNumber();
                            $settingAndRelatedDocumentNumberId[$setting->getId()] = $documentNumberId;
                        }
                    }
                }

                $outgoingDocument->setDocumentNumberId($documentNumberId);
            }
        }

        $outgoingDocuments[] = $outgoingDocument;
    }

    return $outgoingDocuments;
}

function createTableEtibarname($dataToBeInserted, $outgoingDocumentsId, $index)
{
    $dataToBeInsertedChixanSened['etibarnamenin_meqsedi'] = (isset($dataToBeInserted['etibarnamenin_meqsedi'][$index]) ? $dataToBeInserted['etibarnamenin_meqsedi'][$index] : NULL);
    $dataToBeInsertedChixanSened['harada']                = (isset($dataToBeInserted['harada'][$index]) ? $dataToBeInserted['harada'][$index] : NULL);
    $dataToBeInsertedChixanSened['selahiyyetli_user_id']  = (isset($dataToBeInserted['selahiyyetli_user_id'][$index]) ? $dataToBeInserted['selahiyyetli_user_id'][$index] : NULL);
    $dataToBeInsertedChixanSened['icraci_direktor']       = (isset($dataToBeInserted['icraci_direktor'][$index]) ? $dataToBeInserted['icraci_direktor'][$index] : NULL);
    $dataToBeInsertedChixanSened['etibarliq_muddet']      = (isset($dataToBeInserted['etibarliq_muddet'][$index]) ? $dataToBeInserted['etibarliq_muddet'][$index] : NULL);
    $dataToBeInsertedChixanSened['outgoing_documents_id'] = $outgoingDocumentsId;


    DB::insert('tb_prodoc_chixan_sened_tipi_etibarname', $dataToBeInsertedChixanSened);
}

function editTableEtibarname($dataToBeInserted, $outgoingDocumentsId, $index)
{
    $dataToBeInsertedChixanSened['etibarnamenin_meqsedi'] = (isset($dataToBeInserted['etibarnamenin_meqsedi'][$index]) ? $dataToBeInserted['etibarnamenin_meqsedi'][$index] : NULL);
    $dataToBeInsertedChixanSened['harada']                = (isset($dataToBeInserted['harada'][$index]) ? $dataToBeInserted['harada'][$index] : NULL);
    $dataToBeInsertedChixanSened['selahiyyetli_user_id']  = (isset($dataToBeInserted['selahiyyetli_user_id'][$index]) ? $dataToBeInserted['selahiyyetli_user_id'][$index] : NULL);
    $dataToBeInsertedChixanSened['icraci_direktor']       = (isset($dataToBeInserted['icraci_direktor'][$index]) ? $dataToBeInserted['icraci_direktor'][$index] : NULL);
    $dataToBeInsertedChixanSened['etibarliq_muddet']      = (isset($dataToBeInserted['etibarliq_muddet'][$index]) ? $dataToBeInserted['etibarliq_muddet'][$index] : NULL);

    DB::update('tb_prodoc_chixan_sened_tipi_etibarname', $dataToBeInsertedChixanSened, $outgoingDocumentsId, 'outgoing_documents_id');
}

function fayllariYarat($outgoingDocuments)
{
    for ($i = 0, $len = count($outgoingDocuments); $i < $len; ++$i) {
        $name = sprintf("sened_fayl_%s", $i);
        $chixanSenedId = $outgoingDocuments[$i]->getId();

        if (isset($_POST[$name]) && is_array($_POST[$name])) {
            $fileIds = array_map('intval', $_POST[$name]);

            foreach ($fileIds as $value){

                DB::update('tb_files',[ 'module_entry_id' => $chixanSenedId ], $value);
            }
        }
    }

    for ($i = 0, $len = count($outgoingDocuments); $i < $len; ++$i) {
        $name = sprintf("qoshma_fayl_%s", $i);
        $chixanSenedId = $outgoingDocuments[$i]->getId();

        if (isset($_POST[$name]) && is_array($_POST[$name])) {
            $fileIds = array_map('intval', $_POST[$name]);

            foreach ($fileIds as $value){

                DB::update('tb_files',[ 'module_entry_id' => $chixanSenedId ], $value);
            }
        }
    }
}exit;

function getConfirmingUsers($butunTestiqleyenler)
{
    $confirmingUsers = [];
    foreach ($butunTestiqleyenler as $tip => $testiqleyenler) {
        if (is_array($testiqleyenler)) {
            for ($j = 0, $len_j = count($testiqleyenler); $j < $len_j; ++$j) {
                $confirmingUsers[] = [
                    'type' => $tip,
                    'user_id' => $testiqleyenler[$j]
                ];
            }
        } else {
            $confirmingUsers[] = [
                'type' => $tip,
                'user_id' => $testiqleyenler
            ];
        }
    }

    return $confirmingUsers;
}

function getSenedTipi()
{
    return DB::fetchAllIndexed(
        "SELECT id, extra_id FROM tb_prodoc_muraciet_tip WHERE silinib = 0 AND (extra_id = 'arayis' OR extra_id = 'etibarname' OR extra_id = 'etibarname_esas')",
        "extra_id"
    );

}

function neticeniGotur($dsId)
{
    $netice = NULL;
    $d = new Document($dsId);
    if ($d->neticeniDaxilEdeBiler()) {
        $netice = getInt('netice');
        if ($netice === 0) {
            throw new Exception('Nəticəni qeyd etmədniz!');
        }
    }

    return $netice;
}

function getIncomingDocumentStatus($closeDoc)
{
    if ((int)$closeDoc) {
        return Document::STATUS_BAGLI;
    } else {
        return Document::STATUS_ACIQ;
    }
}