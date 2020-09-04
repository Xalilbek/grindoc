<?php
require_once DIRNAME_INDEX . 'prodoc/model/Appeal/Appeal.php';
require_once DIRNAME_INDEX . 'prodoc/service/Confirmation.php';
require_once DIRNAME_INDEX . 'prodoc/model/PowerOfAttorney/PowerOfAttorney.php';

use PowerOfAttorney\PowerOfAttorney;

$sessionUserId = $_SESSION['erpuserid'];

$incomingDocument = new Document($id);
$sened = $incomingDocument->getInfo();

$aciq_derkenarin_mesul_shexsleri = $relatedAcceptedTasks = [];
$incomingDocumentConfirmation = null;
$appealConfirmation = null;

$powerOfAttorney = new PowerOfAttorney($incomingDocument, $user->getId(), new User());
$incomingDocument->setPowerOfAttorney($powerOfAttorney);

$wasRejectedAppeal = false;


if ((int)$sened['state'] === Document::STATE_AUTHOR_ACCEPTED) {
    $confirmationService = new Service\Confirmation\Confirmation($incomingDocument);
    $confirmingUsers = $confirmationService->getCurrentApprovingUsers();

    $confirmingUserId = $powerOfAttorney->canExecute(array_keys($confirmingUsers), true);
    if (false !== $confirmingUserId) {
        $incomingDocumentConfirmation = [
            'confirmation' => $confirmingUsers[$confirmingUserId]
        ];
    }

    if (is_null($incomingDocumentConfirmation)) {
        $aciq_derkenarin_mesul_shexsleri = Task::getAciqDerkenarinMesulShexsleriniByDaxilOlanSenedId($id);

        $sql = sprintf("
				SELECT t1.id
				FROM v_prodoc_muraciet AS t1
				WHERE t1.daxil_olan_sened_id IN (%s)
			", $incomingDocument->getId());

        foreach (DB::fetchColumnArray($sql) as $appealId) {
            $appeal = new Appeal($appealId);

            $wasRejectedAppeal = $appeal->wasRejected();

            $confirmationService = new Service\Confirmation\Confirmation($appeal);
            $currentConfirmingUsers = $confirmationService->getCurrentApprovingUsers();

            $confirmingUserId = $powerOfAttorney->canExecute(array_keys($currentConfirmingUsers), true);
            if (false !== $confirmingUserId) {
                $appealConfirmation = [
                    'appeal' => $appeal,
                    'confirmationId' => $currentConfirmingUsers[$confirmingUserId]['id']
                ];
                break;
            }
        }
    }
}

$isheTikTitle = getProjectName() === TS ? "Şərhlə bağla" : "İşə tik";

/**
 * @param Document $incomingDocument
 * @return string|void
 */
function warningBtn(Document $incomingDocument)
{
    if (!$incomingDocument->senedNezaretEdeBiler()) {
        return;
    }

    return '<button type="button" class="btn btn-circle btn-warning sened_nezaret_et">
                    <i class="fa fa-warning"></i>
                </button>';
}

/**
 * @param Document $incomingDocument
 * @return string|void
 */
function legvEtBtn(Document $incomingDocument)
{
    if (!$incomingDocument->legvEdeBiler()) {
        return;
    }
    return '<div class="btn-group">
                <button type="button" class="btn btn-outline btn-circle red-intense sened_legv_et">'.dsAlt("2616legv", "Ləğv et").'</button>
            </div>';
}

function silBtn(Document $incomingDocument)
{
    if (!$incomingDocument->canDelete()) {
        return;
    }

    return '<div class="btn-group">
            <button type="button" class="btn btn-outline btn-circle red-intense sened_sil">'.dsAlt("2616sil", "Sil").'</button>
        </div>';

}

/**
 * @param Document $incomingDocument
 * @return string|void
 */
function editBtn(Document $incomingDocument)
{
    if (!$incomingDocument->canEdit()) {
        return;
    }

    $type = (int)$incomingDocument->getData()['tip'];

    if (Document::TIP_DAXILI === $type) {
        require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument.php';

        $extraId = InternalDocument::getExtraIdById($incomingDocument->getData()['internal_document_type_id']);

        $link = sprintf('id=%s&daxil_olan_sened_id=%s&hide_header=1&dsType=%s&module=prodoc_daxili_senedler#%s',
            DB::fetchColumn(sprintf('SELECT id FROM %s WHERE document_id = %s',
                InternalDocument::getTableNameByType($extraId),
                $incomingDocument->getId()
            )),
            $incomingDocument->getId(),
            $extraId,
            $extraId
        );
    } else {
        $link = sprintf('module=prodoc_daxil_olan_senedler&id=%s',
            $incomingDocument->getId()
        );
    }

    return sprintf('
		<div class="btn-group" role="group">
			<a type="button" class="btn btn-outline btn-circle blue-steel" href="?%s">
				'.dsAlt("2616duzelish", "Ləğv et").'
			</a>
		</div>
    ', $link);
}

function ImtinaBtn(Document $incomingDocument, $taskId = 0)
{
    if ($taskId) {
        $task = new Task($taskId);
        $extra = $task->canCancel();
    } else {
        $extra = $incomingDocument->canCancel();
    }

    if (!$extra) {
        return '';
    }
    if ($taskId) {
        return '<button data-id=' . $taskId['id'] . ' type="button" class="btn btn-outline btn-circle red-intense derkenar_imtina_et">İmtina et</button>';
    } else {
        return '<button type="button" class="btn btn-outline btn-circle red-intense
         rey_muelifi_imtina" onclick=\'templateYukle("rey_muelifi_imtina","İmtina",{"sid": ' . $incomingDocument->getId() . ' },0,true,"red");\'>
         '.dsAlt("2616imtina", "İmtina").'
         </button>';
    }
}

if ((int)$sened['status'] === Document::STATUS_BAGLI && (int)$sened['netice'] > 0) {
    print '';
} else {
    if ((int)$sened['state'] === Document::STATE_IN_TRASH) {
        print '';
    } else {
        if (
        !is_null($incomingDocumentConfirmation)
        ) {

            ?>
            <div class="btn-group btn-group-justified" role="group"
                 data-id="<?= $incomingDocumentConfirmation['confirmation']['id'] ?>">
                <?php if ($incomingDocumentConfirmation['confirmation']['tip'] === "umumi_shobe_netice" || $incomingDocumentConfirmation['confirmation']['tip'] === "qeydiyyatchi_netice"): ?>
                    <?php if($sened['status']!=1): ?>
                        <div class="" role="group">
                            <button type="button" class="btn btn-outline btn-circle red-intense"
                                    onclick='templateYukle("umumi_shobe_netice","Nəticə",{"sid": "<?php print $incomingDocumentConfirmation['confirmation']['id']; ?>"},0,true,"red");'>
                                <i class="fa fa-commenting"></i>  <?= dsAlt("2616netice_qeyd", "Nəticə qeyd et")?>
                            </button>
                        </div>
                    <?php endif; ?>

                <?php else: ?>
                    <div class="" role="group">
                        <button type="button" class="btn btn-outline btn-circle green-meadow sened_testiq">
                            <i class="fa fa-check"></i> <?= dsAlt("2616tanish_ol", "Tanış ol")?>
                        </button>
                    </div>
                <?php endif; ?>
            </div>
            <?php
        } elseif (
            !is_null($appealConfirmation) && !$wasRejectedAppeal
        ) {
            ?>
            <div role="group" data-id="<?= $appealConfirmation['confirmationId'] ?>">

                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline btn-circle green-meadow sened_testiq">
                        <i class="fa fa-check"></i>  <?= dsAlt("2616testiqle", "Təsdiqlə")?>
                    </button>
                </div>

                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline btn-circle red sened_imtina">
                        <i class="fa fa-check"></i> <?= dsAlt("2616qeydiyyat_pencereleri_imtina", "İmtina et")?>
                    </button>
                </div>
            </div>
            <?php
        } elseif (
            (int)$sened['state'] === Document::STATE_INSPECTED &&
            $powerOfAttorney->canExecute((int)$sened['rey_muellifi'])
        ) {
            ?>
            <div class="" role="group">
                <?php print editBtn($incomingDocument); ?>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline btn-circle green-meadow rey_muelifi_qebul qebul_et">
                        <?= dsAlt("2616qebul_et", "Qəbul et")?>
                    </button>
                </div>
                <?php print ImtinaBtn($incomingDocument); ?>
                <!--        --><?php //print warningBtn($incomingDocument);
                ?>
            </div>
            <?php
        } else {
            if (
                (int)$sened['state'] === Document::STATE_IN_INSPECTION &&
                $powerOfAttorney->canExecute((int)$sened['yoxlayan_shexs'])
            ) {
                ?>
                <div class="" role="group">
                    <?php print editBtn($incomingDocument); ?>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline btn-circle green-meadow yoxlayici_qebul qebul_et">
                            <?= dsAlt('2616tesdiq', 'Təsdiq')?>
                        </button>
                    </div>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline btn-circle red-intense yoxlayici_imtina"
                                onclick='templateYukle("yoxlayici_imtina","İmtina",{"sid": "<?php print $id; ?>" },0,true,"red");'>
                            <?= dsAlt('2616imtina', 'İmtina')?>
                        </button>
                    </div>
                </div>
                <?php
            } else {
                if (
                    (int)$sened['state'] === Document::STATE_NUMBER_REQUIRED &&
                    $powerOfAttorney->canExecute((int)$sened['yoxlayan_shexs'])
                ) {

                    ?>
                    <div class="" role="group">
                        <?php print editBtn($incomingDocument); ?>
                        <script>
                            $(function () {
                                templateYukle("yoxlayici_number_required", "Nömrə", {"sid": "<?php print $id; ?>"}, 0, true, "red");
                            });
                        </script>
                    </div>
                    <?php
                } else {
                    if (
                        (
                            (int)$sened['state'] === Document::STATE_AUTHOR_ACCEPTED
                        )
                        &&
                        false !== ($executorId = $powerOfAttorney->canExecute(array_keys($aciq_derkenarin_mesul_shexsleri),
                            true))
                    ) {
                        $task = $aciq_derkenarin_mesul_shexsleri[$executorId];

                        ?>
                        <div class="" role="group">
                            <div class="btn-group" role="group">
                                <button data-id="<?= $task['id'] ?>" type="button"
                                        class="btn btn-outline btn-circle green-meadow derkenar_qebul_et qebul_et">
                                    <?= dsAlt('2616qebul_et', 'Qəbul et')?>
                                </button>
                            </div>
                            <div class="btn-group" role="group">
                                <button data-id="<?= $task['id'] ?>" type="button"
                                        class="btn btn-outline btn-circle red-intense derkenar_imtina_et">
                                    <?= dsAlt('2616qeydiyyat_pencereleri_imtina', 'İmtina et')?>
                                </button>
                            </div>
                        </div>
                        <?php
                    } else {
                        if ((int)$sened['state'] === Document::STATE_CANCELED) {
                            print legvEtBtn($incomingDocument);
                            print silBtn($incomingDocument);
                            print editBtn($incomingDocument);
                        } else {
                            print editBtn($incomingDocument);
                        }
                    }
                }
            }
        }
    }
}


print derkenarBtn($incomingDocument, $tip,$executor);

if(getProjectName()===TS||((int)$sened['state'] === Document::STATE_AUTHOR_ACCEPTED))
    print ImtinaBtn($incomingDocument);
print senedHazirlaBtn($incomingDocument, $tip,$executor);
print isheTikBtn($incomingDocument, $tip,$executor);
//var_dump($aciq_derkenarin_mesul_shexsleri);exit();
print tanishOlBtn($incomingDocument);





function derkenarBtn(Document $incomingDocument, $tip, $executor=0)
{
    if (!$incomingDocument->derkenarYazaBiler()) {
        return '';
    }

    $myTask = $incomingDocument->getMyTask($executor);


    if (is_null($myTask)) {
        return '<a class="btn btn-outline btn-circle yellow-crusta" href="index.php?module=prodoc_derkenar&sid=' . $incomingDocument->getId() . '&executor='.$executor.'&tip=' . $tip . '&parentTaskId=0">
                    '.dsAlt("2616qeydiyyat_pencereleri_derkenar", "Dərkənar").'
                </a>';
    } else {

        return '<a data-parentTaskId="' . $myTask->getId() . '" class="btn btn-outline btn-circle yellow-crusta alt_derkenar" href="index.php?module=prodoc_derkenar&sid=' . $incomingDocument->getId() . '&executor='.$executor.'&tip=' . $tip . '&parentTaskId=' . $myTask->getId() . '">
                    '.dsAlt("2616qeydiyyat_pencereleri_alt_derkenar", "Alt dərkənar").'
                </a>';
    }
}

function isheTikBtn(Document $incomingDocument, $tip, $executor=0, $taskId = 0)
{
    $isheTikTitle = getProjectName() === TS ? dsAlt('2616sherhle_bagla', "Şərhlə bağla") : dsAlt('2616ishe_tik', "İşə tik");
    if (!$incomingDocument->isheTikeBiler()) {

        return '';
    }
    $myTask = $incomingDocument->getMyTask($executor);


    if(getProjectName()!==TS&&is_null($myTask)){
        return '<button type="button" class="ishetik btn btn-outline btn-circle blue-sharp" onclick=\'templateYukle("ishe_tik","' . $isheTikTitle . '",{"tip": "' . $tip . '", "daxil_olan_sened_id": "' . $incomingDocument->getId() . '", "taskId": "' .$taskId . '","executor":"'.$executor.'" },40,true,"green-meadow");\'>
                            ' . $isheTikTitle . '
                        </button>';
    }

    if (!is_null($myTask)){

        return '<button type="button" class="ishetik btn btn-outline btn-circle blue-sharp" onclick=\'templateYukle("ishe_tik","' . $isheTikTitle . '",{"tip": "' . $tip . '", "daxil_olan_sened_id": "' . $incomingDocument->getId() . '", "taskId": "' . $myTask->getId() . '","executor":"'.$executor.'" },40,true,"green-meadow");\'>
                            ' . $isheTikTitle . '
                        </button>';
    }
    return '';
}

function senedHazirlaBtn($incomingDocument, $tip, $executor=0, $taskId = 0)
{
    if (!$incomingDocument->senedHazirlayaBiler()) {
        return '';
    }

    $myTask = $incomingDocument->getMyTask($executor);

    if (!is_null($myTask)) {
        return '<a data-bildirish-goster="" class="btn btn-outline btn-circle green-seagreen daxil_olan_sened_sened_hazirla" href="index.php?module=prodoc_xaric_olan_senedler&daxil_olan_sened_id=' . $incomingDocument->getId() . '&executor='.$executor.'&taskId=' . $myTask->getId() . '">'.dsAlt('2616sened_hazirla', "Sənəd hazırla").'</a>';

    }else{
        return '<a data-bildirish-goster="" class="btn btn-outline btn-circle green-seagreen daxil_olan_sened_sened_hazirla" href="index.php?module=prodoc_xaric_olan_senedler&daxil_olan_sened_id=' . $incomingDocument->getId() . '&executor='.$executor.'&taskId=0">'.dsAlt('2616sened_hazirla', "Sənəd hazırla").'</a>';

    }

}

function tanishOlBtn($incomingDocument)
{

    $document = $incomingDocument->getInfo();

    if ((int)$document['state'] !== Document::STATE_AUTHOR_ACCEPTED) {
        return '';
    }

    foreach ($incomingDocument->getTaskIds() as $taskId) {

        $task = new Task($taskId);
        if ($task->mayBeFamiliar()) {
            $lastmayBeFamiliarConfirmation = $task->getLastmayBeFamiliarApprovingUser();
            return '<div role="group" data-id="' . $lastmayBeFamiliarConfirmation['id'] . '" >
	                    <button type="button" class="btn btn-outline btn-circle green-meadow sened_testiq">
							<i class="fa fa-check"></i> '
                                .dsAlt("2616tanish_ol", "Tanış ol")
						.'</button>';
        }
    }


    return '';


}

?>