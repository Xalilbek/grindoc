<?php

use PowerOfAttorney\Setting;

session_start();
include_once '../../../class/class.functions.php';
include_once DIRNAME_INDEX . 'modules/module_builder/model.php';
include_once DIRNAME_INDEX . 'prodoc/component/Form.php';
include_once DIRNAME_INDEX . 'prodoc/model/PowerOfAttorney/Setting.php';
require_once DIRNAME_INDEX . 'prodoc/includes/internal_document.php';
$user = new User();

if (!$user->get_session()) {
    header("Location: login.php");
    exit;
}

$userId = (int)$_SESSION['erpuserid'];

$id = getInt('id');
$noEndDate = getInt('no_end_date');
$type = getInt('type');

$fields = [
    [
        "IsRequired" => true,
        "InputType" => "id",
        "ColumnName" => "from_user_id",
        "IsRequiredErrorMessage" => "Vəkalətnamə verən şəxsi seçmədiniz"
    ],
    [
        "IsRequired" => true,
        "InputType" => "id",
        "ColumnName" => "to_user_id",
        "IsRequiredErrorMessage" => "Vəkalətnamə alan şəxsi seçmədiniz"
    ],
    [
        "IsRequired" => true,
        "InputType" => "text.date",
        "ColumnName" => "start_date",
        "IsRequiredErrorMessage" => "Dövrün başlanma tarixini seçmədiniz"
    ],
    [
        "IsRequired" => $type === 2 || ($type === 1 && $noEndDate === 0),
        "InputType" => "text.date",
        "ColumnName" => "end_date",
        "IsRequiredErrorMessage" => "Dövrün bitmə tarixini seçmədiniz"
    ],
    [
        "IsRequired" => true,
        "InputType" => "commaSeparatedIds",
        "ColumnName" => "allowed_docs",
        "IsRequiredErrorMessage" => "Sənədləri seçmədiniz"
    ],
    [
        "InputType" => "id",
        "ColumnName" => "parallelism"
    ],
    [
        "InputType" => "id",
        "ColumnName" => "allowed_to_work_with_subordinate_users_docs"
    ],
    [
        "InputType" => "text",
        "ColumnName" => "note"
    ],
    [
        "IsRequired" => true,
        "InputType" => "id",
        "ColumnName" => "type"
    ],
    [
        "InputType" => "arrayOfIds",
        "ColumnName" => "poa_ids"
    ],
];

$form = new Form($fields);
$form->check();
$dataToBeInserted = $form->collectDataToBeInserted();

try {
    pdo()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    pdo()->beginTransaction();

    if ($type === 1 && $noEndDate === 1) {
        $dataToBeInserted['end_date'] = null;
    }
    $poas =array();
    if ($id) {
        unset($dataToBeInserted['poa_ids']);
        $poa = new \PowerOfAttorney\Setting($id);
        $poa->edit($dataToBeInserted);
        createRelation($id);

    } else {

        if(count($dataToBeInserted['poa_ids'])>0){
            $poas = DB::fetchAll("SELECT tb_prodoc_power_of_attorney.*, v_users.user_name , v_daxil_olan_senedler.document_number
                                FROM tb_prodoc_power_of_attorney 
                                LEFT JOIN v_users ON  from_user_id = USERID   
                                LEFT  JOIN v_daxil_olan_senedler ON tb_prodoc_power_of_attorney.document_id = v_daxil_olan_senedler.id
                                WHERE to_user_id  = ".DB::quote($dataToBeInserted['from_user_id']));

            for ($i=0; $i<count($poas); $i++){
                $poas[$i]['poa_checked'] = $dataToBeInserted['poa_ids'][$i];

            }
        }
        unset($dataToBeInserted['poa_ids']);

        $id = addPowerOfAttorneys($dataToBeInserted,$poas);

    }



    pdo()->commit();
    print json_encode(array("status" => "ok",'id'=>$id));
} catch (\PowerOfAttorney\PowerOfAttorneyException $e) {
    print json_encode([
        'status' => 'error',
        'errors' => [nl2br($e->getMessage())]
    ]);
    pdo()->rollBack();

} catch (Exception $e) {
    print json_encode([
        'status' => 'error',
        'errors' => ['Səhv var']
    ]);

    pdo()->rollBack();
}


function addPowerOfAttorneys($dataToBeInserted, $poas){
    global $user;

    $powerOfAttorneys[] = $dataToBeInserted;
    if(count($poas)>0){
        foreach($poas as $poa){
            $allowed_docs = DB::fetchColumnArray('SELECT doc_type FROM tb_prodoc_power_of_attorney_allowed_doc WHERE power_of_attorney_id = '.$poa['id']);
            if($poa['poa_checked']){

                $powerOfAttorneys[]=[
                    "allowed_docs"=>$allowed_docs,
                    "from_user_id" =>$poa['from_user_id'],
                    'to_user_id' =>$dataToBeInserted['to_user_id'],
                    'start_date' =>$dataToBeInserted['start_date'],
                    "end_date"=>$dataToBeInserted['end_date'],
                    "parallelism"=>$poa['parallelism'],
                    "allowed_to_work_with_subordinate_users_docs"=>$poa['allowed_to_work_with_subordinate_users_docs'],
                    "note"=>$dataToBeInserted['note'],
                    "type"=>$poa['type'],
                ];
            }
        }

    }
;

    $base_power_of_attorney_id =0;

    foreach ($powerOfAttorneys as $key => $poa){
        $powerOfAttorney = PowerOfAttorney\Setting::create($poa, $user);
        $id = $user->createInternalDocumentNumber($powerOfAttorney->getId(), 'power_of_attorney', NULL);
        if($key==0)
            $base_power_of_attorney_id = $id;
        createRelation($id);
    }

    return $base_power_of_attorney_id;

}