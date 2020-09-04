<?php

require_once DIRNAME_INDEX . 'prodoc/model/OutgoingDocument.php';
require_once DIRNAME_INDEX . 'prodoc/model/PowerOfAttorney/PowerOfAttorney.php';

use PowerOfAttorney\PowerOfAttorney;

$outgoingDocument = new OutgoingDocument($id);
$outgoingDocumentInfo = $outgoingDocument->getInfo();

$powerOfAttorney = new PowerOfAttorney($outgoingDocument, $user->getId(), new User());
$outgoingDocument->setPowerOfAttorney($powerOfAttorney);

$confirmation = new \Service\Confirmation\Confirmation($outgoingDocument);
$testiqleyenler = $confirmation->getCurrentApprovingUsers();

if (
	(int)$outgoingDocumentInfo['status'] === OutgoingDocument::STATUS_TESTIQLEMEDE &&
	FALSE !== ($confirmingUserId = $powerOfAttorney->canExecute(array_keys($testiqleyenler), true))
):
	$caritestiqleyen = $testiqleyenler[$confirmingUserId];
	if ($caritestiqleyen['tip'] === TestiqleyecekShexs::TIP_CHAP_EDEN) {
		$btnTitle = 'Çap olundu';
    } else if ($caritestiqleyen['tip'] === TestiqleyecekShexs::TIP_RAZILASHDIRAN) {
        $btnTitle = 'Razılaşdır';
    } else if ($caritestiqleyen['tip'] === TestiqleyecekShexs::TIP_VISA_VEREN) {
        $btnTitle = 'Viza ver';
    } else if ($caritestiqleyen['tip'] === TestiqleyecekShexs::TIP_REDAKT_EDEN) {
        $btnTitle = 'Redaktə et';
    } else if ($caritestiqleyen['tip'] === "umumi_shobe" && $caritestiqleyen['order'] == 6) {
        $btnTitle = 'Qeydiyyatdan keçirt';
    }
    else
    {
        $btnTitle = 'Təstiq et';
    }
?>
	<div class="" role="group" data-id="<?= $caritestiqleyen['id'] ?>">

		<?php if ($caritestiqleyen['tip'] === "umumi_shobe_nomre"): ?>
			<script>
				$(function() {
					templateYukle("yoxlayici_number_required","Nömrə",{"sid": "<?php print $caritestiqleyen['id']; ?>", "tip": "testiqleme"},0,true,"red");
				});
			</script>
		<?php else: ?>
			<div class="btn-group" role="group">
				<button type="button" class="btn btn-outline btn-circle green-meadow tesdiqle-btn sened_testiq">
                    <?= $btnTitle ?>
				</button>
			</div>
			<div class="btn-group" role="group">
				<button type="button" class="btn btn-outline btn-circle red-intense sened_imtina">İmtina et</button>
			</div>
		<?php endif; ?>
	</div>
<?php else: ?>
	<div class="" role="group">
		<?php if ($outgoingDocument->duzelishEdeBiler()): ?>
			<?php
				if ($outgoingDocument->getExtraIdOfType() === "icra_muddeti") {
                    $href = "index.php?module=prodoc_xaric_olan_senedler_icra_muddeti&id={$outgoingDocumentInfo['id']}";
                } else {
                    $href = "index.php?module=prodoc_xaric_olan_senedler&id={$outgoingDocumentInfo['id']}";
                }
			?>
			<div class="btn-group">
				<a type="button" class="btn btn-outline btn-circle blue-steel chixan_sened_duzelish" href="<?=$href?>">
					<i class="fa fa-pencil"></i> Düzəliş et
				</a>
			</div>
		<?php endif; ?>

        <?php if ($outgoingDocument->canDelete()): ?>
			<div class="btn-group">
				<button type="button" class="btn btn-outline btn-circle red-intense chixan_sened_sil">
					<i class="fa fa-trash"></i> Sil
				</button>
			</div>
        <?php endif; ?>
        <?php if($outgoingDocument->canAddFile()): ?>
            <div class="btn-group">
                <button type="button" class="btn btn-outline btn-circle green-meadow" onclick='templateYukle("sened_elave_et","Sənəd əlavə et",{"sid": "<?php print $outgoingDocumentInfo['id']; ?>"},0,true,"green");'>
                    <i class="fa fa-commenting"></i> Sənəd əlavə et
                </button>
            </div>
            <script>

            </script>
        <?php endif; ?>
        <?php if ($outgoingDocument->legvEdeBiler()): ?>
			<div class="btn-group">
				<button type="button" class="btn btn-outline btn-circle red-intense chixan_sened_legv_et">Ləğv et</button>
			</div>
        <?php endif; ?>

        <?php if ($outgoingDocument->canSendDocument()): ?>
			<div class="btn-group">
				<button type="button" class="btn btn-outline btn-circle green-meadow chixan_sened_gonder">
					Göndər
				</button>
			</div>
        <?php endif; ?>

        <?php if ($outgoingDocument->canChangeAnswerIsNotRequired()): ?>
			<div class="btn-group">
				<button type="button" class="btn btn-outline btn-circle green-meadow chixan_sened_cavab_gozlenilmir">
					Cavab gozlənilmir
				</button>
			</div>
        <?php endif; ?>

	</div>
<?php endif; ?>
