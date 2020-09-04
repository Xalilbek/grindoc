<?php

require_once DIRNAME_INDEX . 'prodoc/model/OutgoingDocument.php';

$chixanSened = new OutgoingDocument($id);
$chixanSenedInfo = $chixanSened->getInfo();

$testiqleyenler = Testiqleme::modulunTestiqleyecekShexsleriniQaytar('OutgoingDocument', $id);

if (
	(int)$chixanSenedInfo['status'] === OutgoingDocument::STATUS_TESTIQLEMEDE &&
	array_key_exists((string)$sessionUserId, $testiqleyenler)
):

	$caritestiqleyen = $testiqleyenler[$sessionUserId];
	if ($caritestiqleyen['tip'] === TestiqleyecekShexs::TIP_CHAP_EDEN) {
		$btnTitle = 'Çap olundu';
    } else {
        $btnTitle = 'Razılaşdır';
    }

?>
	<div class="btn-group btn-group-justified" role="group" data-id="<?= $caritestiqleyen['id'] ?>">
		<div class="btn-group" role="group">
			<button type="button" class="btn btn-success sened_testiq">
				<?= $btnTitle ?>
			</button>
		</div>
		<div class="btn-group" role="group">
			<button type="button" class="btn btn-danger sened_imtina">İmtina et</button>
		</div>
	</div>
<?php elseif (
	(int)$chixanSenedInfo['status'] === OutgoingDocument::STATUS_IMTINA_OLUNUB &&
    $chixanSened->duzelishVeLegvEdeBiler()
): ?>
	<div class="btn-group btn-group-justified" role="group">
		<div class="btn-group" role="group">
			<a type="button" class="btn btn-info chixan_sened_duzelish" href="index.php?module=prodoc_xaric_olan_sened&id=<?=$chixanSenedInfo['id']?>">
               Düzəliş et
			</a>
		</div>
		<div class="btn-group" role="group">
			<button type="button" class="btn btn-danger chixan_sened_legv_et">Ləğv et</button>
		</div>
	</div>
<?php endif; ?>
