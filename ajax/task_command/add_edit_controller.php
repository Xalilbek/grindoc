<?php

session_start();
include_once '../../../class/class.functions.php';
include_once DIRNAME_INDEX . 'modules/module_builder/model.php';
include_once DIRNAME_INDEX . 'prodoc/component/Form.php';
include_once DIRNAME_INDEX . 'prodoc/model/InternalDocument/TaskCommand.php';
include_once DIRNAME_INDEX . 'prodoc/model/Exception/BaseException.php';
require_once DIRNAME_INDEX . 'prodoc/includes/internal_document.php';

$user = new User();

if (!$user->get_session()) {
    header("Location: login.php");
    exit;
}

$userId = (int)$_SESSION['erpuserid'];

$id = getInt('id');

$fields = [
    [
        "IsRequired" => true,
        "InputType" => "text.date",
        "ColumnName" => "document_date",
        "IsRequiredErrorMessage" => "Sənədin tarixini seçmədiniz"
    ],
    [
        "IsRequired" => true,
        "InputType" => "commaSeparatedIds",
        "ColumnName" => "melumat",
        "Title" => "Məlumat"
    ],
    [
        "InputType" => "text",
        "ColumnName" => "movzu",
    ],
    [
        "InputType" => "text",
        "ColumnName" => "girish",
    ],
    [
        "InputType" => "text",
        "ColumnName" => "meqsed",
    ],
    [
        "InputType" => "text",
        "ColumnName" => "xususi_qeydler",
    ],

    [
        "InputType" => "array",
        "ColumnName" => "derkenar_metn",
    ],
    [
        "IsRequired" => true,
        "InputType" => "array",
        "ColumnName" => "kime",
        "IsRequiredErrorMessage" => "Tapşırığın məsul şəxslərini (Kimə) seçmədiniz"
    ],
    [
        "InputType" => "array",
        "ColumnName" => "icra_edilme_tarixi",
    ],
    [
        "InputType" => "array",
        "ColumnName" => "icra_edilme_tarixi_disabled",
    ],
    [
        "IsRequired" => true,
        "InputType" => "id",
        "ColumnName" => "rey_muellifi",
        "IsRequiredErrorMessage" => "Tapşırığı yazan şəxsi (Kimdən) seçmədiniz"
    ],
];

$form = new Form($fields);
$form->check();
$dataToBeInserted = $form->collectDataToBeInserted();

try {
    pdo()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    pdo()->beginTransaction();

    if ($id) {

        $taskCommand =new \Model\InternalDocument\TaskCommand($id);
        $taskCommand->edit($dataToBeInserted, $user);
        $document=$taskCommand;

//        $poa = new \PowerOfAttorney\Setting($id);
//        $poa->edit($dataToBeInserted);
    } else {
        $dataToBeInserted['related_document_id'] = daxilOlanSenedleriGotur();

        $daxSayi = count($dataToBeInserted['related_document_id']);


        if ($daxSayi === 0) {
            throw new BaseException('Tapşırıq əmrini heç bir daxil olan sənədə bağlamadınız!');
        }

        if ($daxSayi > 1) {
            throw new BaseException('Tapşırıq əmrini bir neçə daxil olan sənədə bağlaya bilmərsiniz!');
        }


        $document = $user->createInternalDocumentNumber(
            NULL,
            'task_command',
            NULL,
            true
        );


        $dataToBeInserted['document_id'] = $document->getId();

        if ($daxSayi) {
            $dataToBeInserted['related_document_id'] = $dataToBeInserted['related_document_id'][0];
        } else {
            $dataToBeInserted['related_document_id'] = NULL;
        }


        $taskCommand = \Model\InternalDocument\TaskCommand::create($dataToBeInserted, $user);
        $doc_info = DB::fetch("SELECT created_by_name, document_number, 
                                      (SELECT name FROM tb_prodoc_inner_document_type WHERE id=internal_document_type_id) as internal_document_type 
                                                FROM v_daxil_olan_senedler WHERE id=".$document->getId());




        prodoc_notify("derkenar",'daxil_olan_sened',$dataToBeInserted['rey_muellifi'],
            $document->getId(),'daxil_olan_sened',
            array("user_ad"=>$doc_info['created_by_name'],"document_number"=>$doc_info['document_number'],"user_ad2"=> $doc_info['internal_document_type']));

        if (isset($dataToBeInserted['kime'])&&count($dataToBeInserted['kime'])>0){
            foreach ($dataToBeInserted['kime'] as $kime){

                prodoc_notify("task_command_kime",'daxil_olan_sened',$kime,
                    $document->getId(),'daxil_olan_sened',
                        array("user_ad"=>$doc_info['created_by_name'],"document_number"=>$doc_info['document_number'],"user_ad2"=> $doc_info['internal_document_type']));

            }
        }

        createRelation($document->getId());
    }

    pdo()->commit();
    $user->success_msg("ok", ['id' => $document->getId()]);
} catch (BaseException $e) {
    print json_encode([
        'status' => 'error',
        'errors' => [nl2br($e->getMessage())]
    ]);
} catch (Exception $e) {
    print json_encode([
        'status' => 'error',
        'errors' => ['Səhv var']
    ]);
} finally {
    pdo()->rollBack();
}