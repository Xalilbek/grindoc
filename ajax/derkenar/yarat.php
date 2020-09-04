<?php
session_start();
include_once '../../../class/class.functions.php';
include_once DIRNAME_INDEX . 'modules/module_builder/model.php';
require_once DIRNAME_INDEX . 'prodoc/component/Form.php';
require_once DIRNAME_INDEX . 'prodoc/model/Task/Task.php';
require_once DIRNAME_INDEX . 'prodoc/model/Document.php';
require_once DIRNAME_INDEX . 'prodoc/service/Confirmation.php';
require_once DIRNAME_INDEX . 'prodoc/model/Document.php';
require_once DIRNAME_INDEX . 'prodoc/model/TestiqleyecekShexs.php';
require_once DIRNAME_INDEX . 'prodoc/Privilegiya.php';
require_once DIRNAME_INDEX . 'prodoc/includes/internal_document.php';

$user = new User();

if (!$user->get_session()) {
    header("Location: login.php");
    exit;
}

$sened_tipi  = getInt('sened_tip', 0);
$derkenar_id  = getInt('derkenar_id', 0);
$parentTaskId = get('parentTaskId');
$duzelishdi   = $derkenar_id > 0;

$isSubTask = (int)$parentTaskId > 0;

$fields = [
    [
        "InputType" => $duzelishdi || (int)$parentTaskId ? "select" : "arrayOfIds",
        "ColumnName" => "mesul_shexsler",
        "Title" => "Məsul şəxslər",
        "IsRequired" => $sened_tipi==1? false : true
    ],
    [
        "InputType" => "checkbox",
        "ColumnName" => "nezaretde_saxlanilsin"
    ],
    [
        "InputType" => "checkbox",
        "ColumnName" => "daxili_nezaret"
    ],
    [
        "InputType" => "text.date",
        "ColumnName" => "son_icra_tarixi"
    ],
    [
        "InputType" => "id",
        "ColumnName" => "poa_user_id"
    ],
    [
        "InputType" => "text",
        "ColumnName" => "derkenar_metn",
        "Title" => "Dərkənarın mətni",
        "IsRequired" => true
    ],
    [
        "InputType" => "id",
        "ColumnName" => "daxil_olan_sened_id",
        "IsRequired" => true
    ],
    [
        "InputType" => "id",
        "ColumnName" => "derkenar_id"
    ],
    [
        "InputType" => "arrayOfIds",
        "ColumnName" => "kuratorlar",
    ],
    [
        "InputType" => "array",
        "ColumnName" => "melumat",
        "Title" => "Məlumatlandırıcı şəxslər:",
        "IsRequired" => $sened_tipi==2? false : true
    ],
    [
        "InputType" => $duzelishdi || (int)$parentTaskId ? "select" : "array",
        "ColumnName" => "ishtrakchi_shexsler",
        "Title" => "Həm icraçı"
    ],
    [
        "InputType" => "checkbox",
        "ColumnName" => "son_icra_tarixi_var"
    ],
    [
        "InputType" => "id",
        "ColumnName" => "specifiesResult",
        "IsRequired" => false,
        // "IsRequiredErrorMessage" => "Nəticəni qeyd edəcək şəxsi seçmədiniz"
    ],
];

$form = new Form($fields);

$form->check();
$dataToBeInserted = $form->collectDataToBeInserted();

$userId = $_SESSION['erpuserid'];
$id= $dataToBeInserted['daxil_olan_sened_id'];
$mesul_shexsler = $dataToBeInserted['mesul_shexsler'];
$ishtirakchilar= $dataToBeInserted['ishtrakchi_shexsler'];
$melumat= $dataToBeInserted['melumat'];
$kuratorlar= $dataToBeInserted['kuratorlar'];
$nezeret = $dataToBeInserted['daxili_nezaret'];
$derkenarMetn =  $dataToBeInserted['derkenar_metn'];

$derkenar_metn = DB::fetchOneBy('tb_derkenar_metnler', [
    'name' => $dataToBeInserted['derkenar_metn'],
    'deleted' => 0
]);

$derkenar_metn_id = $derkenar_metn['id'];
if ($derkenar_metn_id == NULL){
    $derkenar_metn_id = 0;
}
$daxil_olan_sened_id = $id;

try {
    pdo()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    pdo()->beginTransaction();

    $group_derkenar = DB::fetchColumn("SELECT MAX([group]) from v_derkenar");

    if(is_null($group_derkenar)){
        $group_derkenar =1;
    }
    else{
        $group_derkenar= (int)$group_derkenar+1;
    }
    $dataToBeInserted['group']=$group_derkenar;

    if ($derkenar_id>0) {
        $sened_tipi=DB::fetchColumn("Select sened_tip from v_daxil_olan_senedler where id=".$daxil_olan_sened_id);
    }

    pdof()->query("UPDATE tb_daxil_olan_senedler SET mektub_nezaretdedir = '$nezeret' WHERE id='$id'");
    if(derkenarYazilibsa() == 0)
    {
        pdof()->query("UPDATE tb_daxil_olan_senedler SET sened_tip = '$sened_tipi' WHERE id='$id'");
    }

    if ($sened_tipi==1){
        $dataToBeInserted['mesul_shexs'] = null;
        $dataToBeInserted['derkenar_metn_id'] = $derkenar_metn_id;
        $dataToBeInserted['parentTaskId'] = $parentTaskId;
        $dataToBeInserted['specifiesResult'] = NULL;
        $dataToBeInserted['kuratorlar'] = array();
        $dataToBeInserted['ishtrakchi_shexsler'] = array();
        $dataToBeInserted['mesul_shexsler'] = array();
        $dataToBeInserted['derkenar_metn_id'] = $derkenar_metn_id;
        $dataToBeInserted['derkenar_metn'] = $derkenarMetn;

        forInformation($duzelishdi,$dataToBeInserted, $user, $id,$melumat,$derkenar_id);
    }else{
        $dataToBeInserted['melumat'] = array();
    }

    if ($duzelishdi) {


        $sechilmish_mesul_shexsler = DB::fetchAll("SELECT
                                                                mesul_shexs,
                                                                CONCAT ( Adi, ' ', Soyadi ) AS user_ad 
                                                            FROM
                                                                v_derkenar
                                                                LEFT JOIN v_users ON USERID = mesul_shexs 
                                                            WHERE
                                                                id NOT IN (".$derkenar_id.") AND
                                                                daxil_olan_sened_id = ".$daxil_olan_sened_id);

        foreach ($sechilmish_mesul_shexsler as $sechilmish){
            if($sechilmish['mesul_shexs']==$mesul_shexsler){

                $errors[]= sprintf("\"%s\" artıq bu sənəddə məsul şəxs kimi qeydə alınıb.", $sechilmish['user_ad']);
                if(count($errors)>0){
                    print json_encode([
                        'status' => 'error',
                        'errors' => $errors
                    ]);

                    exit();
                }
            }
        }

        DB::query("UPDATE tb_derkenar 
                        SET derkenar_metn_id = ".$derkenar_metn_id." 
                        where [group] = (Select [group] from v_derkenar where id =".$derkenar_id.")");
        $shexsler="Select user_id from v_derkenar_elave_shexsler where tip='ishtrakchi' AND user_id is not null  AND derkenar_id=".$derkenar_id;


        $task = new Task($derkenar_id);
        $dataToBeInserted['mesul_shexs'] = $mesul_shexsler;
        $dataToBeInserted['derkenar_metn_id'] = $derkenar_metn_id;
        $dataToBeInserted['derkenar_metn'] = $derkenarMetn;
        $dataToBeInserted['parentTaskId'] = $parentTaskId;

        $task->edit($dataToBeInserted);

        $ishtirakchi_notify=pdof()->query("Select created_by,  CONCAT(Adi,'  ', Soyadi) as user_name, document_number 
        from v_daxil_olan_senedler left JOIN tb_users on rey_muellifi=tb_users.USERID where id=".$id)->fetch();

        foreach ($kuratorlar as $kurator){

            $user->sendNotifications( true, true,
                'kurasiya',
                $ishtirakchi_notify[1], "",
                $id,
                $kurator,
                "kurasiya",
                "",
                "",
                "",
                $ishtirakchi_notify[2],
                "daxil_olan_sened",
                "kurasiya"
            );
        }
    } else {
        if ($isSubTask) {
            $dataToBeInserted['mesul_shexs'] = $mesul_shexsler;
            $dataToBeInserted['derkenar_metn_id'] = $derkenar_metn_id;
            $dataToBeInserted['derkenar_metn'] = $derkenarMetn;
            $dataToBeInserted['parentTaskId'] = $parentTaskId;
            $mesul= $_POST['mesul_shexsler'];


            $errors =array();
            $sechilmish_mesul_shexsler = DB::fetchAll("SELECT
                                                                mesul_shexs,
                                                                CONCAT ( Adi, ' ', Soyadi ) AS user_ad 
                                                            FROM
                                                                v_derkenar
                                                                LEFT JOIN v_users ON USERID = mesul_shexs 
                                                            WHERE
                                                                daxil_olan_sened_id = ".$daxil_olan_sened_id);

            foreach ($sechilmish_mesul_shexsler as $sechilmish){
                if($sechilmish['mesul_shexs']==$mesul_shexsler){

                    $errors[]= sprintf("\"%s\" artıq bu sənəddə məsul şəxs kimi qeydə alınıb.", $sechilmish['user_ad']);
                    if(count($errors)>0){
                        print json_encode([
                            'status' => 'error',
                            'errors' => $errors
                        ]);

                        exit();
                    }
                }
            }






            $task = Task::create($dataToBeInserted, $user);
            $taskIds[]=$task->getId();
            $sql = pdof()->query("Select created_by,
                                                (SELECT CONCAT(Adi,' ' , Soyadi) FROM v_derkenar
                                                LEFT JOIN v_users ON USERID = v_derkenar.mesul_shexs WHERE id = (
                                                 SELECT parentTaskId  FROM v_derkenar WHERE  id = ". $task->getId()."
                                                )
                                                ) as user_name,
                                            document_number from v_daxil_olan_senedler 
                                            left JOIN tb_users on rey_muellifi=tb_users.USERID where id=".$id)->fetch();

            $ms = $user->getPersName($_SESSION['erpuserid']);

            $user->sendNotifications( true, true,
                'alt_derkenar_gonderildi',
                $ms, "",
                $id,
                $mesul,
                "alt_derkenar_gonderildi",
                "",
                "",
                "",
                $sql[2],
                "daxil_olan_sened",
                "alt_derkenar"
            );
            $user->sendNotifications( true, true,
                'alt_derkenar_gonderdi',
                $sql[1], "",
                $id,
                $sql[0],
                "alt_derkenar_gonderdi",
                "",
                "",
                "",
                $sql[2],
                "daxil_olan_sened",
                "alt_derkenar"
            );

            $executorsOfDocument = executorsOfTask($taskIds,'ishtrakchi',true);

            foreach ($executorsOfDocument as $ishtirakchi){

                $user->sendNotifications( true, true,
                    'ishtirakchi_derkenar',
                    $sql[1], "",
                    $id,
                    $ishtirakchi,
                    "ishtirakchi_derkenar",
                    "",
                    "",
                    "",
                    $sql[2],
                    "daxil_olan_sened",
                    "derkenar"
                );
            }
        } else {
            $daxil_olan_sened_id = $dataToBeInserted['daxil_olan_sened_id'];

            $responsiblePersonsNum = count($mesul_shexsler);
            $relatedTasksCount = (int)DB::fetchColumn("SELECT COUNT(*) FROM tb_derkenar WHERE daxil_olan_sened_id = {$daxil_olan_sened_id}");

            $privilege = new \Privilegiya();
            $privResponsibleMoreThanOne = $privilege->getByExtraId('netice_bir_nece_icrachi_olduqda');

            if (
                $responsiblePersonsNum > 1 &&
                1 == (int)$privResponsibleMoreThanOne &&
                0 === $relatedTasksCount &&
                0 === (int)$dataToBeInserted['specifiesResult']
            ) {
                throw new Exception('Nəticəni qeyd edəcək şəxsi seçmədiniz');
            }

            if ($responsiblePersonsNum > 1 && (0 == (int)$privResponsibleMoreThanOne || $relatedTasksCount > 0)) {
                $dataToBeInserted['specifiesResult'] = 0;
            } else if ($responsiblePersonsNum === 1) {
                $privResponsibleIsOne = $privilege->getByExtraId('netice_bir_icrachi_olduqda');

                if (1 === $privResponsibleIsOne) {
                    $dataToBeInserted['specifiesResult'] = $mesul_shexsler[0];
                }
            }
            $sechilmish_mesul_shexsler = DB::fetchAll("SELECT
                                                                mesul_shexs,
                                                                CONCAT ( Adi, ' ', Soyadi ) AS user_ad 
                                                            FROM
                                                                v_derkenar
                                                                LEFT JOIN v_users ON USERID = mesul_shexs 
                                                            WHERE
                                                                daxil_olan_sened_id = ".$daxil_olan_sened_id);

            $specifiesResult = (int)$dataToBeInserted['specifiesResult'];
            $errors =array();
            foreach ($mesul_shexsler as $mesul_shexs){

                foreach ($sechilmish_mesul_shexsler as $sechilmish){
                    if($sechilmish['mesul_shexs']==$mesul_shexs){

                        $errors[]= sprintf("\"%s\" artıq bu sənəddə məsul şəxs kimi qeydə alınıb.", $sechilmish['user_ad']);

                    }
                }
            }
            if(count($errors)>0){
                print json_encode([
                    'status' => 'error',
                    'errors' => $errors
                ]);

                exit();
            }


            $taskIds=array();
            foreach ($mesul_shexsler as $mesul_shexs) {
                $dataToBeInserted['mesul_shexs'] = $mesul_shexs;
                $dataToBeInserted['derkenar_metn_id'] = $derkenar_metn_id;
                $dataToBeInserted['derkenar_metn'] = $derkenarMetn;
                $dataToBeInserted['parentTaskId'] = $parentTaskId;

                if ((int)$mesul_shexs === $specifiesResult) {
                    $dataToBeInserted['specifiesResult'] = 1;
                } else {
                    $dataToBeInserted['specifiesResult'] = NULL;
                }

                $task = Task::create($dataToBeInserted, $user);
                $taskIds[]=$task->getId();
            }

            $ishtirakchi_shexsler=pdof()->query("Select created_by,  CONCAT(Adi, Soyadi) as user_name, document_number from v_daxil_olan_senedler left JOIN tb_users on rey_muellifi=tb_users.USERID where id=".$id)->fetch();


            foreach ($kuratorlar as $kurator){

                $user->sendNotifications( true, true,
                    'kurasiya',
                    $ishtirakchi_shexsler[1], "",
                    $id,
                    $kurator,
                    "kurasiya",
                    "",
                    "",
                    "",
                    $ishtirakchi_shexsler[2],
                    "daxil_olan_sened",
                    "kurasiya"
                );
            }

            $sql = pdof()->query("Select created_by,  CONCAT(Adi,' ', Soyadi) as user_name, document_number from 
            v_daxil_olan_senedler left JOIN tb_users on rey_muellifi=tb_users.USERID where id=".$id)->fetch();
            foreach ($mesul_shexsler as $mesul){
                $user->sendNotifications( true, true,
                    'derkenar_gonderildi',
                    $sql[1], "",
                    $id,
                    $mesul,
                    "derkenar_gonderildi",
                    "",
                    "",
                    "",
                    $sql[2],
                    "daxil_olan_sened",
                    "derkenar",
                    $taskIds[0]
                );
            }
            $user->sendNotifications( true, true,
                'derkenar_gonderdi',
                $sql[1], "",
                $id,
                $sql[0],
                "derkenar_gonderdi",
                "",
                "",
                "",
                $sql[2],
                "daxil_olan_sened",
                "derkenar",
                $taskIds[0]
            );
        }

    }

    pdo()->commit();
    $user->success_msg();
} catch (Exception $e) {
    pdo()->rollBack();
    print json_encode([
        'status' => 'error',
        'errors' => [$e->getMessage()]
    ]);
}

function derkenarYazilibsa()
{
    global $id;

    $sql = "SELECT COUNT(tb2.daxil_olan_sened_id)
                 FROM  v_derkenar tb2 
                 WHERE tb2.daxil_olan_sened_id = '$id'
                ";

    return $sened_tipi = DB::fetchColumn(sprintf($sql));
}

function forInformation($editing,$dataToBeInserted,$user,$id,$melumat,$derkenar_id){
    if ($editing){
        $shexsler="Select user_id from tb_derkenar_elave_shexsler where tip='melumat' AND derkenar_id=".$derkenar_id;
        $melumat_shexsler= DB::fetchAll($shexsler);

        $kohne_melumat= array();
        foreach ($melumat_shexsler as $key=> $melumat){

            $kohne_melumat[]=(int)$melumat_shexsler[$key][0];
        }
        $task = new Task($derkenar_id);
        $task->edit($dataToBeInserted);


    }
    else{
        $dataToBeInserted['status']=NULL;
        Task::create($dataToBeInserted, $user);
    }

    pdo()->commit();
    $user->success_msg();

}