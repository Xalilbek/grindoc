<?php
session_start();
include_once '../../../class/class.functions.php';
include_once DIRNAME_INDEX . 'prodoc/component/Form.php';
require_once DIRNAME_INDEX . 'prodoc/model/Task/Task.php';
require_once DIRNAME_INDEX . 'prodoc/service/Confirmation.php';
require_once DIRNAME_INDEX . 'prodoc/model/TestiqleyecekShexs.php';
require_once DIRNAME_INDEX . 'prodoc/model/Document.php';
require_once DIRNAME_INDEX . 'prodoc/model/PowerOfAttorney/PowerOfAttorney.php';

use PowerOfAttorney\PowerOfAttorney;

$user = new User();

if (!$user->get_session()) {
    print "daxil_olmayib";
    exit;
}

$sessionUserId = $_SESSION['erpuserid'];

$id  = getRequiredPositiveInt('id');
$tip = get('tip');
$executor = get('executor');

if ('daxil_olan_sened' === $tip) {
    $sql = "
        SELECT
            document.created_by,
            document.emeliyyat_tip,
            doc_type.extra_id,
            document.id,
            document.tip
        FROM tb_daxil_olan_senedler AS document
        LEFT JOIN tb_prodoc_inner_document_type doc_type
         ON doc_type.id = document.internal_document_type_id 
        WHERE document.id = $id
    ";
    $document = DB::fetch($sql);

    if (Document::TIP_DAXILI === (int)$document['tip']) {
        $tip = $document['extra_id'];

        require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument.php';
        $tblName = InternalDocument::getTableNameByType($tip);
        $internalDocument = DB::fetchOneBy($tblName, [
            'document_id' => $id
        ]);
        $internalDocumentId = $internalDocument['id'];

        $iDoc = new InternalDocument($id);

        $powerOfAttorney = new PowerOfAttorney($iDoc, $user->getId(), new User());

        $currentApprovingUsersInternal = ProdocInternal::getCurrentApprovingUsers($id);

        $confirmingUserIdInternal = $powerOfAttorney->canExecute(
            array_keys($currentApprovingUsersInternal),
            true
        );

        $currentOperation = NULL;
        if (FALSE !== $confirmingUserIdInternal) {
            $currentOperation = $currentApprovingUsersInternal[$confirmingUserIdInternal];
        }

        if (count($currentApprovingUsersInternal) > 0) {
            $derkenardadir = false;

            foreach ($currentApprovingUsersInternal as $currentApprovingUser) {
                if ($currentApprovingUser['emeliyyat_tip'] === 'derkenar') {
                    $derkenarYazan = $currentApprovingUser;
                    $derkenardadir = true;
                    break;
                }
            }

            if ($derkenardadir) {
                $tip = 'daxil_olan_sened';

                require_once DIRNAME_INDEX . 'prodoc/includes/internal_document.php';
                senedinReyMuelifiniTeyinEt($id, $derkenarYazan);
            }
        }
    }
}

if ($tip == 'daxil_olan_sened')
{
	require_once 'emeliyyat_duymeleri/emeliyyat_duymeleri_daxil_olan_sened.php';
}
else if ($tip == 'chixan_sened')
{
    require_once 'emeliyyat_duymeleri/emeliyyat_duymeleri_chixan_sened.php';
}
else if ($tip == 'task_command')
{
    require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument/TaskCommand.php';
    showConfirmationButtons(\Model\InternalDocument\TaskCommand::class, $id, $tip);
}
else if ($tip == 'hesabat_yarat')
{
    require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument/PartlamamishTekSursat.php';
    showConfirmationButtons(\Model\InternalDocument\PartlamamishTekSursat::class, $id, $tip);
}
else if ($tip == 'create_act')
{
    require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument/Act.php';
    showConfirmationButtons(\Model\InternalDocument\Act::class, $id, $tip);
}
else if ($tip == 'protask_taprsiriq_child')
{
    require_once DIRNAME_INDEX . 'protask/ajax/dashboard/emeliyyat_duymeleri.php';
}

else if (
    $tip == 'arayish' ||
    $tip == 'mezuniyet_erizesi' ||
    $tip == 'ezamiyyet_erizesi' ||
    $tip == 'ezamiyet_emri' ||
    $tip == 'umumi_forma' ||
    $tip == 'icra_sexsin_deyisdirilmesi' ||
    $tip == 'icra_muddeti_deyisdirilmesi' ||
    $tip == 'satin_alma' ||
    $tip == 'elave_razilashdirma' ||
    $tip == 'mezuniyet_emri' ||
    $tip == 'ezamiyet_emri' ||
    $tip == 'ishe_qebul_emri' ||
    $tip == 'emek_muqavilesine_xitam_emri' ||
    $tip == 'mezuniyyete_gore_kompensasiya' ||
    $tip == 'ish_taphsirigi' ||
    $tip == 'icaze' ||
    $tip == 'xestelik_vereqi' ||
    $tip == 'ishe_qebul_erizesi' ||
    $tip == 'bashqa_ishe_kechirtme_erizsesi' ||
    $tip == 'ishe_xitam_erizesi' ||
    $tip == 'ezamiyyet_erizesi' ||
    $tip == 'mezuniyet_erizesi' ||
    $tip == 'teqdimat' ||
    $tip == 'emek_muqavilesi' ||
    $tip == 'power_of_attorney' ||
    $tip === 'xitam_erizesi'
)
{
    // $changeNotifStatus = ProdocInternal::updateNotifications($tip, $internalDocumentId, $sessionUserId);
    // $edits = ProdocInternal::deyisdirib($internalDocumentId, $tip);

    $tip =  '#'.$tip;

    require_once DIRNAME_INDEX . 'prodoc/ajax/dashboard/emeliyyat_duymeleri/emeliyyat_duymeleri_daxili.php';

//    $buttonlar = $user->template_yukle(
//        'formlar/buttonlar',
//        array(
//            'sid'=>$internalDocumentId,
//            'sid_basic' => $id,
//            'project_id'=>(getProjectName()===TS)? 1 :0,
//            'type'=>$tip,
//            'tesdiqBtn'=>(int)$testiqEdenData['tesdiqBtn'],
//            'imtinaBtn'=>(int)$testiqEdenData['imtinaBtn'],
//            'editBtn'=>(int)((int)$document['created_by'] == $sessionUserId),
//            'status'=>(int)$internalDocument['status'],
//            "edits"=>$edits,
//            'approveBtnTitle' => getApproveBtnTitle($testiqEdenData['emeliyyat_tip']),
//            'approveType' => getApproveType($testiqEdenData['emeliyyat_tip']),
//            'testiq_id' => $testiqEdenData['id'],
//            'daxil_olan_sened_id' => $id
//        ),
//        'prodoc'
//    );
}

else if ($tip == 'avans_telebi')
{
    require_once 'emeliyyat_duymeleri/daxili_senedler/advance_request.php';
}
else if ($tip == 'avans_hesabati')
{
    require_once 'emeliyyat_duymeleri/daxili_senedler/advance_report.php';
}
else if ($tip == 'emekhaqqi_avansi')
{
    require_once 'emeliyyat_duymeleri/daxili_senedler/salary_advance.php';
}


function getApproveType($emeliyyat_tip)
{
    $testiqEmeliyyatlari = [
        "melumatlandirma",
        "hr_approve",
        "tesdiqleme"
    ];

    if (in_array($emeliyyat_tip, $testiqEmeliyyatlari)) {
        return "tesdiqleme";
    } else {
        return $emeliyyat_tip;
    }
}

?>
