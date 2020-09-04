
<?php

	use Model\LastExecution\LastExecution;
	use View\Helper\Proxy;

	require_once DIRNAME_INDEX . 'prodoc/model/Task/Task.php';
	require_once DIRNAME_INDEX . 'prodoc/service/Confirmation.php';
	require_once DIRNAME_INDEX . 'prodoc/model/OutgoingDocument.php';
	require_once DIRNAME_INDEX . 'prodoc/model/Document.php';
	require_once DIRNAME_INDEX . 'prodoc/Util/Template.php';
	require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument.php';
    require_once DIRNAME_INDEX . 'prodoc/includes/etrafli/etrafli_functions.php';

	require_once 'son_emeliyyat.php';
    $user = new User();
    $priv = new Privilegiya();
    $senedlerin_etraflisinin_tenzimlenmesi = $priv->getByExtraId('senedlerin_etraflisinin_tenzimlenmesi');

	$sql = "
		SELECT
			tb1.*, (
				SELECT
					Adi
				FROM
					tb_CustomersCompany
				WHERE
					id = tb1.gonderen_teshkilat
			) AS gonderen_teshkilat_ad,
			(
				SELECT
					CONCAT (Adi, ' ', Soyadi, ' ', AtaAdi)
				FROM
					tb_Customers
				WHERE
					id = tb1.gonderen_shexs
			) AS gonderen_shexs_ad,
			(
				SELECT
					CONCAT (Adi, ' ', Soyadi, ' ', AtaAdi)
				FROM
					tb_users
				WHERE
					USERID = tb1.created_by
			) AS created_by_name,
			(
				SELECT
					CONCAT (Adi, ' ', Soyadi, ' ', AtaAdi)
				FROM
					tb_users
				WHERE
					USERID = tb1.rey_muellifi
			) AS rey_muellifi_ad,
			tb2.ad AS mektubun_tipi_ad,
			tb3.ad AS mektubun_alt_tipi_ad,
			t.ad as tibb_muessiseleri_ad,
            t.dovlet as tibb_muessiseleri_tip,
            n.name as nazalogiya_ad,
            m.ad as mektubun_tipi_third_ad,
            mt.ad as mektubun_mezmunu_ad,
			tb7.ad AS movzu_ad,
			join_fiziki.muraciet_eden_tip_id,
			(
				CASE
				WHEN tb1.outgoing_document_id > 0 THEN
					tb1.outgoing_document_person_id
				ELSE
					join_fiziki.muraciet_eden
				END
			) muraciet_eden,
			tb1.sened_tip,
			(
				CASE
				WHEN tb1.sened_tip = 1 THEN
					N'Məlumat üçün'
				ELSE
					N'İcra üçün'
				END
			) senedin_tipi,
			CONCAT(tb4.Adi, ' ', tb4.Soyadi) AS muraciet_eden_ad,
			join_fiziki.unvan,
			join_fiziki.telefon,
			join_fiziki.region,
			tb5.name AS region_ad,
			join_fiziki.hardan_daxil_olub,
			join_fiziki.shexsiyyet_vesiqesi_seria AS seria,
			join_fiziki.shexsiyyet_vesiqesi_pin_kod AS pin_kod,
			join_fiziki.tekrar_eyni_sened_id,
			join_fiziki.tekrar_eyni,
			tb6.name AS hardan_daxil_olub_ad,
			tb8.user_ad AS yoxlayan_shexs_ad,
			tb9.name AS netice_ad,
		    (
				CASE
				WHEN tb1.tip = 1 THEN
					N'Hüquqi'
				WHEN tb1.tip = 2 THEN
					N'Vətəndaş müraciəti'
				ELSE
					N'Qeyd olunmayıb'
				END
			) as tipi,
		    ( 
                CASE 
                WHEN derkenar_metn.name != '' AND derkenar.diger_derkenar_metn = ''
                THEN 
                    derkenar_metn.name 
                ELSE 
                    derkenar.diger_derkenar_metn 
                END 
		    ) AS derkenar_metn_ad
			FROM
				v_daxil_olan_senedler_corrected AS tb1
			LEFT JOIN tb_derkenar_metnler as derkenar_metn	 ON  derkenar_metn.id=tb1.derkenar_metn_id
            LEFT JOIN tb_derkenar AS derkenar ON derkenar.daxil_olan_sened_id= tb1.id
			LEFT JOIN tb_daxil_olan_senedler_fiziki join_fiziki ON join_fiziki.daxil_olan_sened_id = tb1.id
			LEFT JOIN tb_mektubun_tipleri tb2 ON tb2.id = tb1.mektubun_tipi
			LEFT JOIN tb_mektubun_tipleri tb3 ON tb3.id = tb1.mektubun_alt_tipi
			LEFT JOIN tb_mektubun_tipleri tb7 ON tb7.id = join_fiziki.movzu
			LEFT JOIN tb_Customers AS tb4 ON tb4.id = (CASE WHEN tb1.outgoing_document_id > 0 THEN tb1.outgoing_document_person_id ELSE join_fiziki.muraciet_eden END)
			LEFT JOIN tb_prodoc_regionlar AS tb5 ON tb5.id = join_fiziki.region
			LEFT JOIN tb_prodoc_daxil_olma_menbeleri AS tb6 ON tb6.id = join_fiziki.hardan_daxil_olub
			LEFT JOIN v_user_adlar tb8 ON tb8.USERID = tb1.yoxlayan_shexs
			LEFT JOIN tb_prodoc_neticeler AS tb9 ON tb9.id = tb1.netice
			LEFT JOIN tb_prodoc_tibb_muessiseleri t ON t.id = tb1.tibb_muessisesi
            LEFT JOIN tb_prodoc_nazalogiya n ON n.id = tb1.nazalogiya
            LEFT JOIN tb_mektubun_tipleri m ON m.id = tb1.mektubun_tipi_third
            LEFT JOIN tb_mektubun_tipleri mt ON mt.id = tb1.mektubun_mezmunu
			WHERE tb1.id = '$sened_id' ORDER BY elave_olunma_tarixi DESC  
	";

	$senedler = DB::fetch($sql);

    $sql = '
                SELECT 
                    ophone
                FROM tb_daxil_olan_senedler_fiziki_ophone
                WHERE sened_id = %s';
    $ophones = DB::fetchAll(sprintf($sql, $sened_id));

	$incomingDocument = new Document($sened_id);

	$relatedTasks = Task::getAllTasks($incomingDocument);
	$confirmation = new Service\Confirmation\Confirmation($incomingDocument);
	$approvingUsers = $confirmation->getApprovingUsersOfGroup(null, 'tanish_ol');

	$fizikiSeneddi = Document::TIP_FIZIKI === (int)$senedler['tip'];



	$taskIds = [];
    $relatedMainTasks = [];
    $relatedSubTasks  = [];
	$daxili_nezaret = false;
	foreach ($relatedTasks as $relatedTask) {
		$taskIds[] = $relatedTask['id'];
        $daxili_nezaret = ($relatedTask['daxili_nezaret'] == 1 || $daxili_nezaret ? true : false);

        if($relatedTask['parentTaskId'] > 0)
        {
            $relatedMainTasks[] = $relatedTask;
        }
        else
        {
            $relatedSubTasks[] = $relatedTask;
        }
    }

	$documentHasNoTasks = count($relatedTasks) === 0;




    $curators = $tasksWithAppeal = [];
    if (count($taskIds)) {
        $sql = "SELECT
					v_user_adlar.user_ad,
					v_user_adlar.USERID
				FROM
					tb_derkenar_elave_shexsler
				LEFT JOIN v_user_adlar ON v_user_adlar.USERID = tb_derkenar_elave_shexsler.user_id
				WHERE
					derkenar_id IN (%s) AND tip = 'kurator'
				GROUP BY v_user_adlar.USERID, v_user_adlar.user_ad";
    	$curators = DB::fetchAll(sprintf($sql, implode(',', $taskIds)));

        $sql = "
			SELECT derkenar_id
			FROM v_prodoc_muraciet
			WHERE derkenar_id IN (%s)
		";
        $tasksWithAppeal = DB::fetchColumnArray(sprintf($sql, implode(',', $taskIds)));
    }

	$relatedDocuments = $incomingDocument->getRelatedDocuments();

	$relatedOutgoingDocuments = array_column(array_filter($relatedDocuments, function($relatedDocument) {
		return $relatedDocument['type'] === 'outgoing' && (int)$relatedDocument['id'] > 0;
	}), 'id');

	if ($fizikiSeneddi && (int)$senedler['tekrar_eyni_sened_id'] > 0) {
        $sql = "
			SELECT 
				DOS.document_number,
				DOS.id,
				'incoming' AS type,
				'' AS answer_is_not_required
			FROM v_daxil_olan_senedler_corrected AS DOS
			WHERE
			DOS.id = {$senedler['tekrar_eyni_sened_id']}
		";
        $relatedDocuments[] = DB::fetch($sql);
    }


    $answersToRelatedOutgoingDocuments = [];
    if (count($relatedOutgoingDocuments)) {
    	$sql = "
    		SELECT id, document_number, outgoing_document_id
    		FROM v_daxil_olan_senedler_corrected
    		WHERE outgoing_document_id IN (%s) AND document_number_id IS NOT NULL
    	";
        $answersToRelatedOutgoingDocuments = DB::fetchAllIndexed(sprintf($sql, implode(',',$relatedOutgoingDocuments)), 'outgoing_document_id');
    }

	include_once DIRNAME_INDEX . 'prodoc/model/LastExecutionDate/LastExecution.php';
	$nezaret_muddeti = DB::fetchOneColumnBy('tb_options', 'value', [
		'option_name' => 'nezaret_muddeti'
	]);
	$lastExecution = new LastExecution($user, $nezaret_muddeti === 'istehsalat_teqvimi');

	$led = new DateTime(date('d-m-Y H:i', strtotime($senedler['icra_edilme_tarixi'])));

	$res = $lastExecution->getRemainingDaysByLastExecutionDate($led, true);
	if (is_array($res)) {
		$result['remainingDaysNum']  = $res['remainingDaysNum'];
		$result['nonWorkingDaysNum'] = $res['nonWorkingDaysNum'];
	} else {
		$result['remainingDaysNum'] = $res;
		$result['nonWorkingDaysNum'] = 0;
	}

	$repeatOrSameDocs = [];
	if ($fizikiSeneddi) {
        $sql = "
			SELECT f.fiziki_document_number, f.tekrar_eyni, f.daxil_olan_sened_id AS id
			FROM v_daxil_olan_senedler_fiziki f
			WHERE
			f.tekrar_eyni_sened_id = $sened_id
		";
        $repeatOrSameDocs = DB::fetchAll($sql);
    }

    $listOfPrincipals = [
        $senedler['created_by'],
        $senedler['rey_muellifi'],
        $senedler['yoxlayan_shexs']
	];

	foreach ($relatedTasks as $relatedTask) {
		$listOfPrincipals[] = $relatedTask['mesul_shexs'];
    }

	foreach ($approvingUsers as $approvingUser) {
		$listOfPrincipals[] = $approvingUser['user_id'];
	}

	foreach ($curators as $curator) {
		$listOfPrincipals[] = $curator['USERID'];
	}

	$proxy = new Proxy($incomingDocument);
	$proxy->setListOfPrincipals($listOfPrincipals);

    $mektubun_tipi_first  = \Service\Option\Option::getOrCreateValue('mektubun_tipi_first_table', dsAlt('2616etrafli_mektub_tip', 'Məktubun tipi'));
    $mektubun_tipi_second = \Service\Option\Option::getOrCreateValue('mektubun_tipi_second_table', dsAlt('2616etrafli_alt', 'Alt tipi'));
    $mektubun_tipi_third  = \Service\Option\Option::getOrCreateValue('mektubun_tipi_third_table', dsAlt('2616qeydiyyat_pencereleri_alt_movzu', 'Alt mövzu'));
    $mektubun_tipi_last   = \Service\Option\Option::getOrCreateValue('mektubun_tipi_last_table', dsAlt('2616qeydiyyat_penceleri_mektub_mezmun', 'Məktubun məzmunu'));



//    document type
    $tipId = $senedler['sened_tip'] === "1" ?  "melumat" : "icra";
    $senedTipId = $fizikiSeneddi ? "vm" : "hs";
    $foundKey = "dos_{$senedTipId}_{$tipId}";

    $elementler = getButtonPositionKeys($foundKey);
?>

<style>
    .container-etrafli > div{
        margin: 20px 0;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 14px;
        font-weight: bold;
        cursor: move; /* fallback if grab cursor is unsupported */
        cursor: grabbing;
        cursor: -moz-grabbing;
        cursor: -webkit-grabbing;
    }
</style>

<div>
    <div class='page-header'>
        <div class="container-etrafli">

            <div data-position="<?php print addButtonPositionKey($elementler, 'tipi'); ?>" id="tipi">
                <p>
                    <span><?= dsAlt('2616etrafli_nov', 'Növ')?>:</span>
                    <br>
                    <strong><?php cap($senedler['tipi']) ?></strong>
                </p>

                <?php if((int)$senedler['state'] === Document::STATE_IN_TRASH): ?>
                    <div class="alert alert-danger">
                        <strong style="color: #ff0000;"><?= dsAlt('2616etrafli_legv', 'Sənəd ləğv olunub'); ?></strong>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($fizikiSeneddi): ?>
                    <div data-position="<?php print addButtonPositionKey($elementler, 'muraciet_eden_ad'); ?>" id="muraciet_eden_ad">
                        <span><?= dsAlt('2616etrafli_muraciet_eden', 'Müraciət edən şəxs')?></span>

                        <br>
                        <strong>
                            <?php cap($senedler['muraciet_eden_ad']) ?>
                        </strong>
                        <br>
                        &nbsp;<i class="fa fa-file-text-o"></i> <a href='javascript: templateYukle("sender_documents","Əvvəlki sənədlər",{"sender_id": <?php print $senedler['muraciet_eden']; ?>},80,true,"green");'>
                            <?= dsAlt('2616etrafli_evvelki_senedler', 'Əvvəlki sənədlər')?>
                        </a>
                    </div>
                    <div data-position="<?php print addButtonPositionKey($elementler, 'region_ad'); ?>" id="region_ad">
                        <span><?= dsAlt('2616etrafli_region', 'Region')?></span><br>
                        <strong><?php cap($senedler['region_ad']) ?></strong>
                    </div>
                    <div data-position="<?php print addButtonPositionKey($elementler, 'phone'); ?>" id="phone">
                        <span><?= dsAlt('2616etrafli_telefon', 'Telefonu')?></span><br>
                        <?php foreach ($ophones as $ophone): ?>
                            <strong><?= $ophone['ophone'] ?></strong><br>
                        <?php endforeach; ?>
                    </div>
                    <div data-position="<?php print addButtonPositionKey($elementler, 'shexsiyyet_vesiqesi'); ?>" id="shexsiyyet_vesiqesi">
                        <span><?= dsAlt('2616etrafli_sv', 'Şəxsiyyət vəsiqəsi') ?></span><br>
                                <?= dsAlt('2616etrafli_seria_nomre', 'seria №')?>: <strong><?php cap($senedler['seria']) ?></strong> <br> <?= dsAlt('2616etrafli_fin_kod', 'fin kodu')?>: <strong><?php cap($senedler['pin_kod']) ?></strong>
                    </div>
                    <div data-position="<?php print addButtonPositionKey($elementler, 'unvan'); ?>" id="unvan">
                        <span><?= dsAlt('2616etrafli_unvan', 'Ünvan')?></span><br>&nbsp;<strong><?php cap($senedler['unvan']) ?></strong>
                    </div>
                    <div data-position="<?php print addButtonPositionKey($elementler, 'hardan_daxil_olub_ad'); ?>" id="hardan_daxil_olub_ad">
                        <span><?= dsAlt('2616haradan_daxil_olub', 'Haradan daxil olub') ?></span><br>&nbsp;<strong><?php cap($senedler['hardan_daxil_olub_ad']) ?></strong>
                    </div>
                    <?php if(!is_null($senedler['icra_edilme_tarixi'])): ?>
                        <?php if ($result['remainingDaysNum'] > 0): ?>
                            <div data-position="<?php print addButtonPositionKey($elementler, 'remainingDaysNum'); ?>" id="remainingDaysNum">
                                <span><?= dsAlt('2616etrafli_icra_gun', 'İcrasına qalan gün')?></span><br>&nbsp;
                                <strong><?php print $result['remainingDaysNum']; ?></strong>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-danger">
                                <strong style="color: #ff0000;"><?= dsAlt('2616etrafli_son_icra_bitib', 'Sənədin son icra tarixi bitib'); ?></strong>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
            <?php endif; ?>

            <div data-position="<?php print addButtonPositionKey($elementler, 'document_number'); ?>" id="document_number">
                <span><?= dsAlt('2616etrafli_do_nomre', "Sənədin daxil olma №-si")?></span>&nbsp;<br>
                <strong class="document_number_etrafli">
                    <?php !empty($senedler['document_number']) ?
                            cap($senedler['document_number']) :
                            cap('-'); ?>
                    <?php if ($fizikiSeneddi): ?>
                        <?php if ((int)$senedler['tekrar_eyni'] === 1): ?>
                            - <i><?= dsAlt('2616tekrar', 'Təkrar')?></i>
                        <?php elseif ((int)$senedler['tekrar_eyni'] === 2): ?>
                            - <i class="font-red-flamingo"><?= dsAlt('2616eyni', 'Eyni')?></i>
                        <?php endif; ?>

                    <?php endif; ?>
                </strong>
            </div>

            <div data-position="<?php print addButtonPositionKey($elementler, 'status'); ?>" id="status">
                <span><?= dsAlt('2616status_dos', 'Status')?></span><br>&nbsp;<strong><?php cap(Document::getStatusTitle($senedler['status'])) ?></strong>
            </div>
            <div data-position="<?php print addButtonPositionKey($elementler, 'senedin_tipi'); ?>" id="senedin_tipi">
                <span><?= dsAlt('2616etrafli_sened_tip', 'Sənəd tipi')?></span><br><strong><?php cap($senedler['senedin_tipi']) ?></strong>
            </div>

            <div data-position="<?php print addButtonPositionKey($elementler, 'gonderen_teshkilat_ad'); ?>" id="gonderen_teshkilat_ad">
                <span><?= dsAlt('2616gonderen_teshk','Göndərən təşkilat') ?></span><br><strong><?php cap($senedler['gonderen_teshkilat_ad']) ?></strong>
            </div>

            <?php if (!$fizikiSeneddi): ?>
                <div data-position="<?php print addButtonPositionKey($elementler, 'gonderen_aidiyyati_tabeli_ad'); ?>" id="gonderen_aidiyyati_tabeli_ad">
                    <span><?= dsAlt('2616etrafli_gonderen_qurum', 'Göndərən qurum')?></span><br> <strong><?= $senedler['gonderen_aidiyyati_tabeli_ad']?></strong>
                </div>
            <?php endif; ?>
            <div data-position="<?php print  addButtonPositionKey($elementler, 'gonderen_shexs_ad'); ?>" id="gonderen_shexs_ad">
                <span><?= dsAlt('2616etrafli_gonderen_shexs', 'Göndərən şəxs')?></span><br>&nbsp;<strong><?php cap($senedler['gonderen_shexs_ad']) ?></strong>
            </div>
            <div data-position="<?php print addButtonPositionKey($elementler, 'gonderen_teshkilatin_nomresi'); ?>" id="gonderen_teshkilatin_nomresi">
                <span><?= dsAlt('2616etrafli_gonderen_teshkilat_no', 'Göndərən təşkilatın nömrəsi')?></span><br><strong><?php cap($senedler['gonderen_teshkilatin_nomresi']) ?></strong>
            </div>
            <div data-position="<?php print addButtonPositionKey($elementler, 'senedin_tarixi');?>" id="senedin_tarixi">
                <span><?= dsAlt('2616etrafli_sened_tarix', 'Sənəd tarixi')?></span><br><strong><?php tarixCapEt($senedler['senedin_tarixi'], 'd M Y') ?></strong>
            </div>
            <div data-position="<?php print addButtonPositionKey($elementler, 'senedin_daxil_olma_tarixi'); ?>" id="senedin_daxil_olma_tarixi">
                <span><?= dsAlt('2616etrafli_do_tarix', 'Sənədin daxil olma tarixi')?></span><br><strong><?php tarixCapEt($senedler['senedin_daxil_olma_tarixi']) ?></strong>
            </div>

            <div data-position="<?php print addButtonPositionKey($elementler, 'son_emeliyyat'); ?>" id="son_emeliyyat">
                <span><?= dsAlt('2616etrafli_son_emeliyyat', 'Son əməliyyat')?></span><br> <strong><?php son_emeliyyat($taskIds, $sened_id) ?></strong>
            </div>
            <div data-position="<?php print addButtonPositionKey($elementler, 'mektubun_tipi_ad'); ?>" id="mektubun_tipi_ad">
                <span><?= $mektubun_tipi_first ?></span><br> <strong><?php cap($senedler['mektubun_tipi_ad']) ?></strong>
            </div>


          <?php
            if($senedler['tipi'] != 'Hüquqi') {
                ?>
                <div data-position="<?php print  addButtonPositionKey($elementler, 'movzu_ad'); ?>" id="movzu_ad">
                    <span><?= dsAlt('2616etrafli_movzu', 'Mövzu')?></span><br> <strong><?php cap($senedler['movzu_ad']) ?></strong>
                </div>
                <?php
            }
          ?>

            <div data-position="<?php print addButtonPositionKey($elementler, 'mektubun_alt_tipi_ad'); ?>" id="mektubun_alt_tipi_ad">
                <span><?= $mektubun_tipi_second ?></span><br> <strong><?php cap($senedler['mektubun_alt_tipi_ad']) ?></strong>
            </div>

            <?php if (!empty($senedler['mektubun_tipi_third_ad'])): ?>
                <p><span><?= $mektubun_tipi_third ?></span><br> <strong><?php cap($senedler['mektubun_tipi_third_ad']) ?></strong></p>
            <?php endif; ?>

            <?php if (!empty($senedler['tibb_muessiseleri_ad'])): ?>
                <p><span>Tibb müəssisəsi</span><br> <strong><?php cap($senedler['tibb_muessiseleri_ad']) ?></strong></p>
            <?php endif; ?>

            <?php if (!empty($senedler['nazalogiya_ad'])): ?>
                <p><span>Nazalogia</span><br> <strong><?php cap($senedler['nazalogiya_ad']) ?></strong></p>
            <?php endif; ?>

            <?php if (!empty($senedler['mektubun_mezmunu_ad'])): ?>
                <p><span><?= $mektubun_tipi_last ?></span><br> <strong><?php cap($senedler['mektubun_mezmunu_ad']) ?></strong></p>
            <?php endif; ?>


            <?php if(!is_null($senedler['icra_edilme_tarixi']) && $senedler['sened_tip']==2): ?>
                <div data-position="<?php print addButtonPositionKey($elementler, 'son_icra_tarixi_to'); ?>" id="son_icra_tarixi_to">
                    <span><?= dsAlt(TELEB_OLUNAN_TARIX_AD, 'Tələb olunan tarix') ?></span><br><strong><?php tarixCapEt($senedler['icra_edilme_tarixi'], 'd-m-Y') ?></strong>
                </div>
            <?php endif; ?>

            <?php if(!is_null($senedler['son_icra_tarixi'])): ?>
                <div data-position="<?php print addButtonPositionKey($elementler, 'son_icra_tarixi'); ?>" id="son_icra_tarixi">
                    <span><?= dsAlt('2616etrafli_son_icra_tarix', 'Son icra tarixi')?></span><br><strong><?php tarixCapEt($senedler['son_icra_tarixi'], 'd-m-Y') ?></strong>
                </div>
            <?php endif; ?>

            <div data-position="<?php print addButtonPositionKey($elementler, 'sened_daxili_nezaretdedir'); ?>" id="sened_daxili_nezaretdedir">
                <span><?= dsAlt('2616etrafli_daxili_nezaret', 'Sənəd daxili nəzarətdədir')?></span><br><?php print($daxili_nezaret ? '<i class="fa fa-check"></i>' : "") ?>
            </div>
            <div data-position="<?php print addButtonPositionKey($elementler, 'netice_ad'); ?>" id="netice_ad">
                <span><?= dsAlt('2616etrafli_netice', 'Nəticə')?></span><br> <strong><?php cap($senedler['netice_ad']) ?></strong>
            </div>
            <div data-position="<?php print addButtonPositionKey($elementler, 'daxil_olma_yolu_ad'); ?>" id="daxil_olma_yolu_ad">
                <span><?= dsAlt('2616dos_doy', 'Daxil olma yolları')?></span><br> <strong><?php cap($senedler['daxil_olma_yolu_ad']) ?></strong>
            </div>
            <div data-position="<?php print addButtonPositionKey($elementler, 'vereq_sayi'); ?>" id="vereq_sayi">
                <span><?= dsAlt('2616qeydiyyat_pencereleri_vereqler_sayi', 'Sənədin vərəqlərinin sayı')?></span><br> <strong><?php cap($senedler['vereq_sayi']) ?></strong>
            </div>
            <div data-position="<?php print addButtonPositionKey($elementler, 'qoshma_sayi'); ?>" id="qoshma_sayi">
                <span><?= dsAlt('2616etrafli_qoshma_vereq_say', 'Qoşma vərəqlərinin sayı')?></span><br> <strong><?php print (int)$incomingDocument->data['qoshma_sayi'] ?></strong>
            </div>
            <div data-position="<?php print addButtonPositionKey($elementler, 'mektubun_qisa_mezmunu'); ?>" id="mektubun_qisa_mezmunu">
                <span><?= dsAlt('2616etrafli_mektub_qm', 'Məktubun qısa məzmunu')?></span><br> <strong><?php cap($senedler['qisa_mezmun_name']) ?></strong>
            </div>
            <div data-position="<?php print addButtonPositionKey($elementler, 'qisa_mezmun_name'); ?>" id="qisa_mezmun_name">
                <span><?= print (getProjectName() === TS)?  dsAlt('2616daxil_olan_qisa_mezmun', 'Qısa məzmun'): dsAlt('2616etrafli_qeyd', 'Qeyd')?></span><br> <span style="font-weight: bold;word-wrap: break-word;"><?php cap($senedler['mektubun_qisa_mezmunu']) ?></span>
            </div>
            <div data-position="<?php print addButtonPositionKey($elementler, 'derkenar_metn_ad'); ?>" id="derkenar_metn_ad">
                <p><span><?= dsAlt('2616etrafli_derkenar_metn', 'Dərkənar mətni')?></span><br> <strong><?php print $senedler['derkenar_metn_ad'] ?></strong></p>
            </div>
            <div class="document-tree" data-position="<?php print addButtonPositionKey($elementler, 'tree'); ?>" id="tree">
                <ul>
                    <li data-jstree='{"icon":"fa fa-folder fa-lg icon-state-warning"}' class="jstree-open"><?php cap($senedler['document_number']) ?>
                        <ul>
                            <li data-jstree='{"icon":"fa fa-folder fa-lg icon-state-warning"}'>
                                <?= dsAlt('2616etrafli_cavab_senedi', 'Cavab sənədi') ?>
                                <ul>
                                    <?php foreach ($answersToRelatedOutgoingDocuments as $answersToRelatedOutgoingDocument): ?>
                                        <li
                                            data-jstree='{"icon":"fa fa-file-text-o"}'
                                            data-href="index.php?module=prodoc_new&id=<?= $answersToRelatedOutgoingDocument['id'] ?>&bolme=prodoc_sened_qeydiyyatdan_kecib">
                                            <?= $answersToRelatedOutgoingDocument['document_number'] ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                            <li data-jstree='{"icon":"fa fa-folder fa-lg icon-state-success"}' >
                                <?= dsAlt('2616qeydiyyat_pencereleri_elaqeli', 'Əlaqəli sənəd') ?>
                                <ul>
                                    <?php foreach ($relatedDocuments as $relatedOutgoingDocument): ?>
                                        <li
                                            data-jstree='{"icon":"fa fa-file-text-o"}'
                                            data-href="<?= \View\Helper\DocumentLinkGenerator::generateHref($relatedOutgoingDocument['id'], $relatedOutgoingDocument['type']); ?>"
                                        >
                                            <?= $relatedOutgoingDocument['document_number'] ?>
                                            <?= $relatedOutgoingDocument['related_type'] ?>

                                            <?php /* wrong variable name :( */ if ($relatedOutgoingDocument['type'] === 'outgoing'): ?>
                                                -
                                                <?php if (array_key_exists($relatedOutgoingDocument['id'], $answersToRelatedOutgoingDocuments)): ?>
                                                    Cavab gəlib
                                                <?php elseif ((int)$relatedOutgoingDocument['answer_is_not_required']): ?>
                                                    Cavab gozlənilmir
                                                <?php else: ?>
                                                    Cavab gəlməyib
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>

                            <?php if ((int)$incomingDocument->data['tip'] === Document::TIP_FIZIKI): ?>
                            <li data-jstree='{"icon":"fa fa-folder fa-lg icon-state-success"}' >
                                <?= dsAlt('2616etrafli_tekrar', 'Təkrar sənəd'); ?>
                                <ul>
                                    <?php foreach (array_filter($repeatOrSameDocs, function($repeatOrSameDoc) { return (int)$repeatOrSameDoc['tekrar_eyni'] === 1; }) as $repeatOrSameDoc): ?>
                                        <li
                                            data-jstree='{"icon":"fa fa-file-text-o"}'
                                            data-href="index.php?module=prodoc_new&id=<?= $repeatOrSameDoc['id'] ?>&bolme=prodoc_sened_qeydiyyatdan_kecib"
                                        >
                                            <?= $repeatOrSameDoc['fiziki_document_number'] ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                            <li data-jstree='{"icon":"fa fa-folder fa-lg icon-state-success"}' >
                                <?= dsAlt('2616etrafli_eyni', 'Eyni sənəd'); ?>
                                <ul>
                                    <?php foreach (array_filter($repeatOrSameDocs, function($repeatOrSameDoc) { return (int)$repeatOrSameDoc['tekrar_eyni'] === 2; }) as $repeatOrSameDoc): ?>
                                        <li
                                            data-jstree='{"icon":"fa fa-file-text-o"}'
                                            data-href="index.php?module=prodoc_new&id=<?= $repeatOrSameDoc['id'] ?>&bolme=prodoc_sened_qeydiyyatdan_kecib"
                                        >
                                            <?= $repeatOrSameDoc['fiziki_document_number'] ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                            <?php endif; ?>

                        </ul>
                    </li>
                </ul>
            </div>

        </div>
        <link rel="stylesheet" href="asset/global/plugins/jstree/dist/themes/default/style.min.css" />
        <script src="asset/global/plugins/jstree/dist/jstree.js"></script>
        <script src="prodoc/app.js"></script>
		<script>
            var result = <?php echo json_encode($elementler); ?>;

            loadEtrafliFront(".container-etrafli",result);

            <?php if($senedlerin_etraflisinin_tenzimlenmesi): ?>
                sortEtrafliFront('.container-etrafli', "<?php print $foundKey; ?>");

            <?php endif; ?>
            
            var getmodule = '<?php print isset($_POST['from_task_page']);  ?>';
            loadingTreeTemplate($("#sened-elaveler-body-elaqeli-sened"),getmodule);
            loadingTreeTemplate($("#sened-elaveler-body"),getmodule);

            function loadingTreeTemplate(container,getmodule){
                container
                    .find('.document-tree')
                    .on('activate_node.jstree', function (node, event) {
                        if (_.isUndefined(event.node.data.href))
                            return;

                        if(getmodule==1)
                            return;

                        location.href = event.node.data.href;
                    })
                    .jstree()
                ;
            }

			var modules = '<?php print isset($_POST['module']) ? $_POST['module'] : '';?>';

			if(modules != 'prodoc_nezaret_sehifesi')
            {
                var documet_number = $('.document_number_etrafli').text().trim();
                var dashboard_doc_number =$("#senedler-tbody .selected").find('.document_number');
                if(dashboard_doc_number.text()==""||dashboard_doc_number.text()=="-"||dashboard_doc_number.text().length<2){
                    dashboard_doc_number.text(documet_number);
                }
            }

		</script>
    </div>
</div>