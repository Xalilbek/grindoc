<?php
require_once DIRNAME_INDEX . 'prodoc/model/OutgoingDocument.php';
require_once DIRNAME_INDEX . 'prodoc/service/Confirmation.php';
require_once DIRNAME_INDEX . 'prodoc/model/TestiqleyecekShexs.php';
require_once DIRNAME_INDEX . 'prodoc/Util/View.php';
require_once DIRNAME_INDEX . 'prodoc/includes/etrafli/etrafli_functions.php';

use Util\View;
use View\Helper\Proxy;

$outgoingDocument = new OutgoingDocument($senedler['id']);
$priv = new Privilegiya();

$senedlerin_etraflisinin_tenzimlenmesi = $priv->getByExtraId('senedlerin_etraflisinin_tenzimlenmesi');

$sid = $senedler['id'];
$sql = "
    	SELECT tb2.id AS id, tb2.document_number, tb3.name AS netice_ad
    	FROM v_prodoc_outgoing_document_relation  AS tb1
    	LEFT JOIN v_daxil_olan_senedler_corrected AS tb2 ON tb1.daxil_olan_sened_id = tb2.id 
    	LEFT JOIN tb_prodoc_neticeler AS tb3 ON tb2.netice = tb3.id
    	WHERE tb1.outgoing_document_id = %s
    ";
$relatedIncomingDocuments = DB::fetchAllIndexed(sprintf($sql, $outgoingDocument->getId()), 'id');

$confirmation = new \Service\Confirmation\Confirmation($outgoingDocument);
$approvingUsers = $confirmation->getApprovingUsers();
$TenantId = $user->getActiveTenantId();

$appealTypeOptional = DB::fetchOneBy('tb_prodoc_muraciet_tip', [ 'id'=>$outgoingDocument->getDocumentType(), 'dos_status'=>3]);


$sql = "
	SELECT relation_document.id,
       (CASE
          WHEN appeal.dos_status IS NULL THEN CONCAT(document_number, N' (Əlaqələndirilmiş sənəd)')
              ELSE document_number END) as document_number
    FROM (SELECT t4.document_number,
                 t4.id
          FROM tb_internal_document_relation AS t1
    
                 LEFT JOIN v_daxil_olan_senedler_corrected AS t4
                           ON t4.id = t1.internal_document_id
    
          WHERE t1.related_document_type = 'outgoing'
            AND t1.related_document_id = {$senedler['id']}
    
          UNION
    
    SELECT t4.document_number,
           t4.id
    FROM v_prodoc_outgoing_document_relation AS tb1
           LEFT JOIN v_daxil_olan_senedler_corrected AS t4
                     ON t4.id = tb1.daxil_olan_sened_id
    WHERE tb1.outgoing_document_id = {$senedler['id']}
    
    UNION
    
    SELECT document_number,
           id
    FROM v_daxil_olan_senedler
    WHERE outgoing_document_id = {$senedler['id']}
    )
    relation_document
    
    LEFT
    JOIN
    v_prodoc_outgoing_document_relation
    muraciet
    ON relation_document.id = muraciet.daxil_olan_sened_id AND muraciet.outgoing_document_id= {$senedler['id']}
      LEFT JOIN tb_prodoc_appeal_outgoing_document appeal ON muraciet.id = appeal_id

	
	";
$checkCertificate = $senedler["arayis_user_id"] > 0 ? true : false;

$relatedInternalDocuments = DB::fetchAll($sql);

$listOfPrincipals = [
    $senedler['created_by'],
    $senedler['kim_gonderir'],
];

foreach ($approvingUsers as $approvingUser) {
    $listOfPrincipals[] = $approvingUser['user_id'];
}

$proxy = new Proxy($outgoingDocument);
$proxy->setListOfPrincipals($listOfPrincipals);


require_once DIRNAME_INDEX . 'prodoc/includes/internal_document.php';

switch ($senedler['muraciet_tip_extra_id']) {
    case 'etibarname' :
        $file_original_name = getIxracFileName('etibarname_chixan_sened', $TenantId);
        break;
    case 'arayish' :
        $file_original_name = getIxracFileName('arayish', $TenantId);
        break;
    case 'etibarname_esas' :
        $sql = "
            SELECT
                tb1.*,
               (SELECT user_ad FROM v_user_adlar s WHERE s.USERID=tb1.selahiyyetli_user_id) as selahiyyetli_name,
               (SELECT TOP 1 vezife FROM v_users v WHERE v.USERID=tb1.selahiyyetli_user_id) as selahiyyetli_vezife,
               (SELECT TOP 1 struktur_bolmesi FROM v_users st WHERE st.USERID=tb1.selahiyyetli_user_id) as selahiyyetli_shobe,
               (SELECT TOP 1 user_ad FROM v_user_adlar s WHERE s.USERID=tb1.icraci_direktor) as icraci_direktor_name,
               (SELECT TOP 1 CONCAT(bi.code_of_state, '', bi.passport_num) FROM tb_users_biometric_ids bi WHERE bi.user_id=tb1.selahiyyetli_user_id) as vesiqesi_melumat
            FROM
                tb_prodoc_chixan_sened_tipi_etibarname AS tb1
            WHERE
             tb1.outgoing_documents_id = {$senedler['id']}
	      ";
        $sened_etibarname_esas = DB::fetch($sql);
        $file_original_name = getIxracFileName('etibarname_esas', $TenantId);
        break;
    default:
        $file_original_name = "";
        break;

}


function renderConfirmingUser($approvingUser)
{
    global $proxy;
    return sprintf(
        " %s %s <br>",
        $proxy->getProxyNameByPrincipal($approvingUser['user_id'], $approvingUser['user_ad_qisa']),
        TestiqleyecekShexs::STATUS_TESTIQLEYIB === (int)$approvingUser['status'] ? '<i class="fa fa-check"></i>' : ''
    );
}

//  document type
    $key_doc = "xos_{$senedler['muraciet_tip_extra_id']}";

    $elementler = getButtonPositionKeys($key_doc);

?>

<style>
    .container-etrafli-xos > div{
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
<div class="page-header">
    <div class="container-etrafli-xos">

        <?php if ($checkCertificate): ?>
            <div data-position="<?php print addButtonPositionKey($elementler, 'sened_formasi')?>" id="sened_formasi">
                <small><?= dsAlt('2616etrafli_sened_formasi', 'Sənəd forması')?>:</small>
                <br>
                <span id="ixracFormasi">
                    <a onclick="javascript: void(0)" class="ixrac_link_yoxlama">
                        <i class="icon-doc"><?= $file_original_name ?></i>
                    </a>
                    <a href="prodoc/pages/certificate.php?id=<?= $sid ?>&tip=2&amp;export=word" class="hidden ixrac_link">
                        <i class="icon-doc"><?= $file_original_name ?></i>
                    </a>
                </span>
            </div>
        <?php endif; ?>
        <?php if ((int)$senedler['status'] === OutgoingDocument::STATUS_LEGV_OLUNUB): ?>
            <div class="alert alert-danger">
                <strong style="color: #ff0000;"><?= dsAlt('2616etrafli_legv', 'Sənəd ləğv olunub'); ?></strong>
            </div>
        <?php endif; ?>
        <?php if (getProjectName() === TS): ?>
            <div data-position="<?php print addButtonPositionKey($elementler, 'tipi')?>" id="tipi">
                <span><?php print dsAlt('2616etrafli_nov', 'Növ') ?>:</span>
                <br>
                <strong><?= dsAlt('2616qeydiyyat_pencereleri_chixan', 'Çıxan sənəd'); ?></strong>
            </div>
        <?php endif; ?>
        <div data-position="<?php print addButtonPositionKey($elementler, 'status')?>" id="status">
                <small><?= dsAlt('2616status_dos', 'Status') ?> </small>
                <br> <?php cap($senedler['is_sended'] == 1 ? "Göndərilib" : "Göndərilməyib") ?>
            </div>
        <?php if (getProjectName() === ANAMA): ?>
            <div data-position="<?php print addButtonPositionKey($elementler, 'eleve_nomre')?>" id="elave_nomre">
                <small><?= dsAlt('2616etrafli_elave_nomre', 'Əlavə Nömrə'); ?></small>
                <br> <?php cap($senedler['eleve_nomre']) ?>
            </div>
        <?php endif; ?>
        <?php if ($senedler['muraciet_tip_extra_id'] === 'arayis'): ?>
            <div data-position="<?php print addButtonPositionKey($elementler, 'arayis_user_id_ad')?>" id="arayis_user_id_ad">
                <small><?php print dsAlt('2616etrafli_asa', 'A.S.A'); ?></small>
                <br> <?php cap($senedler['arayis_user_id_ad']) ?>
            </div>
            <div data-position="<?php print addButtonPositionKey($elementler, 'arayis_tarixi')?>" id="arayis_tarixi">
                <small><?php print dsAlt('2616etrafli_arayish_tarix', 'Arayışın tarixi'); ?>:</small>
                <br> <?php tarixCapEt($senedler['arayis_tarixi']) ?>
            </div>
            <div data-position="<?php print addButtonPositionKey($elementler, 'arayis_ise_qebul_tarixi')?>" id="arayis_ise_qebul_tarixi">
                <small><?php print dsAlt('2616etrafli_ishe_qebul_tarix', 'İşə qəbul tarix'); ?></small>
                <br> <?php tarixCapEt($senedler['arayis_ise_qebul_tarixi']) ?>
            </div>
        <?php endif; ?>
        <?php if ($senedler['muraciet_tip_extra_id'] === 'etibarname'): ?>
            <div data-position="<?php print addButtonPositionKey($elementler, 'etibarname_sexs_ad')?>" id="etibarname_sexs_ad">
                <small><?php print dsAlt('2616etrafli_shexs', 'Şəxs'); ?></small>
                <br> <?php cap($senedler['etibarname_sexs_ad']) ?>
            </div>
            <div data-position="<?php print addButtonPositionKey($elementler, 'etibarname_kartin_nomresi')?>" id="etibarname_kartin_nomresi">
                <small><?php print dsAlt('2616etrafli_kart_nomre', 'Kartın nömrəsi'); ?></small>
                <br> <?php cap($senedler['etibarname_kartin_nomresi']) ?>
            </div>
            <div data-position="<?php print addButtonPositionKey($elementler, 'etibarname_avtomobilin_nomresi')?>" id="etibarname_avtomobilin_nomresi">
                <small><?php print dsAlt('2616etrafli_avto_nomre', "Avtomobilin nömrəsi:"); ?></small>
                <br> <?php cap($senedler['etibarname_avtomobilin_nomresi']) ?>
            </div>
            <div data-position="<?php print addButtonPositionKey($elementler, 'etibarname_yanacagin_miqdari')?>" id="etibarname_yanacagin_miqdari">
                <small><?php print dsAlt('2616etrafli_yanacaq_miqdar', "Yanacağın miqdarı:"); ?></small>
                <br> <?php cap($senedler['etibarname_yanacagin_miqdari']) ?>
            </div>
            <div data-position="<?php print addButtonPositionKey($elementler, 'etibarname_vesiqenin_kodu')?>" id="etibarname_vesiqenin_kodu">
                <small><?php print dsAlt('2616etrafli_sv_fin', "Şəxsiyyət vəsiqəsinin fin kodu:"); ?></small>
                <br> <?php cap($senedler['etibarname_vesiqenin_kodu']) ?>
            </div>
            <div data-position="<?php print addButtonPositionKey($elementler, 'etibarname_vesiqenin_nomresi')?>" id="etibarname_vesiqenin_nomresi">
                <small><?php print dsAlt('2616etrafli_sv_nomre', "Şəxsiyyət vəsiqəsinin nömrəsi:"); ?></small>
                <br> <?php cap($senedler['etibarname_vesiqenin_nomresi']) ?>
            </div>
            <div data-position="<?php print addButtonPositionKey($elementler, 'etibarname_vesiqeni_teqdim_eden_orqan')?>" id="etibarname_vesiqeni_teqdim_eden_orqan">
                <small><?php print dsAlt('2616etrafli_vesiqe_teqdim_eden', "Vəsiqəni təqdim edən orqan:"); ?></small>
                <br> <?php cap($senedler['etibarname_vesiqeni_teqdim_eden_orqan']) ?>
            </div>
            <div data-position="<?php print addButtonPositionKey($elementler, 'etibarliq_muddet')?>" id="etibarliq_muddet">
                <small><?php print dsAlt('2616etrafli_etibarliliq_muddet', "Etibarlılıq müddəti"); ?></small>
                <br> <?php cap($senedler['etibarname_etibarliq_muddet']) ?>
            </div>
            <div data-position="<?php print addButtonPositionKey($elementler, 'icraci_direktor_name')?>" id="icraci_direktor_name">
                <small><?= dsAlt('2616etrafli_icraci_direktor', "İcraçı direktor"); ?>:</small>
                <br> <?php cap($senedler['etibarname_icraci_direktor_ad']) ?>
            </div>
            <div data-position="<?php print addButtonPositionKey($elementler, 'etibarname_bas_muhasib_ad')?>" id="etibarname_bas_muhasib_ad">
                <small><?= dsAlt('2616etrafli_bash_muhasib', "Baş mühasib:") ?></small>
                <br> <?php cap($senedler['etibarname_bas_muhasib_ad']) ?>
            </div>
        <?php endif; ?>
        <?php if ($senedler['muraciet_tip_extra_id'] === 'icra_muddeti'): ?>
            <div data-position="<?php print addButtonPositionKey($elementler, 'icra_muddeti_muraciet_olunan_tarix')?>" id="icra_muddeti_muraciet_olunan_tarix">
                <small><?php print dsAlt('2616etrafli_yeni_icra', "Yeni icra tarixi"); ?></small>
                <br> <?php tarixCapEt($senedler['icra_muddeti_muraciet_olunan_tarix']) ?>
            </div>
            <div data-position="<?php print addButtonPositionKey($elementler, 'icra_muddeti_qeyd')?>" id="icra_muddeti_qeyd">
                <small><?php print dsAlt('2616etrafli_icra_muddet_qeyd', "İcra müddəti qeyd"); ?></small>
                <br> <?php cap($senedler['icra_muddeti_qeyd']) ?>
            </div>
        <?php endif; ?>
        <?php if ($senedler['muraciet_tip_extra_id'] === 'etibarname_esas'): ?>
            <div data-position="<?php print addButtonPositionKey($elementler, 'etibarnamenin_meqsedi')?>" id="etibarnamenin_meqsedi">
                <small><?php print dsAlt('2616etrafli_etibarname_meqsed', "Etibarnamənin məqsədi:"); ?></small>
                <br> <?php cap($sened_etibarname_esas['etibarnamenin_meqsedi']) ?>
            </div>
            <div data-position="<?php print addButtonPositionKey($elementler, 'harada')?>" id="harada">
                <small><?php print dsAlt('2616etrafli_harada', "Harada:"); ?></small>
                <br> <?php cap($sened_etibarname_esas['harada']) ?>
            </div>
            <div data-position="<?php print addButtonPositionKey($elementler, 'selahiyyetli_name')?>" id="selahiyyetli_name">
                <small><?php print dsAlt('2616etrafli_selahiyyetli_shexs', "Səlahiyyətli şəxs"); ?></small>
                <br> <?php cap($sened_etibarname_esas['selahiyyetli_name']) ?>
            </div>
            <div data-position="<?php print addButtonPositionKey($elementler, 'selahiyyetli_shobe')?>" id="selahiyyetli_shobe">
                <small><?php print dsAlt('2616etrafli_departament', "Departament"); ?></small>
                <br> <?php cap($sened_etibarname_esas['selahiyyetli_shobe']) ?>
            </div>
            <div data-position="<?php print addButtonPositionKey($elementler, 'selahiyyetli_vezife')?>" id="selahiyyetli_vezife">
                <small><?php print dsAlt('2616etrafli_vezife', "Vəzifə"); ?></small>
                <br> <?php cap($sened_etibarname_esas['selahiyyetli_vezife']) ?>
            </div>
            <div data-position="<?php print addButtonPositionKey($elementler, 'vesiqesi_melumat')?>" id="vesiqesi_melumat">
                <small><?php print dsAlt('2616etrafli_sv_melumatlar', "Şəxsiyyət vəsiqəsi məlumatları"); ?></small>
                <br> <?php cap($sened_etibarname_esas['vesiqesi_melumat']) ?>
            </div>
            <div data-position="<?php print addButtonPositionKey($elementler, 'icraci_direktor_name')?>" id="icraci_direktor_name">
                <small><?= dsAlt('2616etrafli_icraci_direktor', "İcraçı direktor"); ?>:</small>
                <br> <?php cap($sened_etibarname_esas['icraci_direktor_name']) ?>
            </div>
            <div data-position="<?php print addButtonPositionKey($elementler, 'etibarliq_muddet')?>" id="etibarliq_muddet">
                <small><?php print dsAlt('2616etrafli_etibarliliq_muddet', "Etibarlılıq müddəti"); ?></small>
                <br> <?php cap($sened_etibarname_esas['etibarliq_muddet']) ?>
            </div>
        <?php endif; ?>

        <div data-position="<?php print addButtonPositionKey($elementler, 'document_number_xos')?>" id="document_number_xos">
            <small><?php print dsAlt('2616etrafli_xos_nomre', "Xaric olan sənədin nömrəsi"); ?></small>
            <strong class="document_number_etrafli">
                <br>&nbsp;<?php cap($senedler['document_number']) ?>
            </strong>
        </div>
        <div data-position="<?php print addButtonPositionKey($elementler, 'tarix')?>" id="tarix">
            <small><?= dsAlt('2616umumi_hesabat_tarix', "Tarix"); ?></small>
            <strong>
                <br>&nbsp;<?php cap($senedler['tarix']) ?>
            </strong>
        </div>
        <div data-position="<?php print addButtonPositionKey($elementler, 'teyinat_ad')?>" id="teyinat_ad">
            <small><?php print dsAlt('2616etrafli_teyinat', "Təyinat"); ?></small>
            <br>&nbsp;<?php $senedler['teyinat_ad']== 'Fiziki şəxsə' ? cap('Vətəndaş müraciəti') : cap($senedler['teyinat_ad']); ?>
        </div>
        <div data-position="<?php print addButtonPositionKey($elementler, 'gonderen_teshkilat_ad_xos')?>" id="gonderen_teshkilat_ad_xos">
            <small><?php print dsAlt('2616etrafli_hansi_teshkilat', "Hansı təşkilata göndərilir"); ?></small>
            <br>&nbsp;<?php cap($senedler['gonderen_teshkilat_ad']) ?>
        </div>
        <div data-position="<?php print addButtonPositionKey($elementler, 'gonderen_shexs_ad_xos')?>" id="gonderen_shexs_ad_xos">
            <small><?php print dsAlt('2616etrafli_hansi_shexs', "Hansı şəxsə göndərilir"); ?></small>
            <br>&nbsp;<?php cap($senedler['gonderen_shexs_ad']) ?>
        </div>
        <div data-position="<?php print addButtonPositionKey($elementler, 'muraciet_tip_ad')?>" id="muraciet_tip_ad">
            <small><?php print dsAlt('2616etrafli_muraciet_tip', "Müraciət tipi"); ?></small>
            <br>&nbsp;<?php cap($senedler['muraciet_tip_ad']) ?>
        </div>
        <div data-position="<?php print addButtonPositionKey($elementler, 'muraciet_alt_tip_ad')?>" id="muraciet_alt_tip_ad">
            <small><?php print dsAlt('2616etrafli_muraciet_alt_tip', "Müraciətin alt tipi"); ?></small>
            <br>&nbsp;<?php cap($senedler['muraciet_alt_tip_ad']) ?>
        </div>
        <div data-position="<?php print addButtonPositionKey($elementler, 'icra_muddeti_vereq_sayi')?>" id="icra_muddeti_vereq_sayi">
            <small><?php print dsAlt('2616etrafli_vereq_say', "Vərəqlərin sayı"); ?></small>
            <br> <?php cap($senedler['icra_muddeti_vereq_sayi']) ?>
        </div>

        <div data-position="<?php print addButtonPositionKey($elementler, 'netice_ad')?>" id="netice_ad">
            <small><?php print dsAlt('2616etrafli_dos_netice', "Daxil olan sənədin nəticəsi"); ?></small>
            <br>
            <?php
            View::altPrintArray(
                $relatedIncomingDocuments,
                function ($relatedIncomingDocument) {
                    return sprintf(
                        "%s - %s <br>",
                        $relatedIncomingDocument['document_number'],
                        View::altPrint(htmlspecialchars($relatedIncomingDocument['netice_ad']), '<i>Yoxdur</i>', true)
                    );
                },
                '<i>Sənəd heç bir daxil olan sənədə bağlı deyil</i>',
                '%s'
            );
            ?>
        </div>
        <?php if (getProjectName() != TS): ?>
            <div data-position="<?php print addButtonPositionKey($elementler, 'redakte_eden_name')?>" id="redakte_eden_name">
                <small><?= dsAlt('2616rollar_redakte_eden', "Redaktə edən şəxs"); ?></small>
                <br>
                <?php
                View::altPrintArray(
                    array_filter($approvingUsers, function ($approvingUser) {
                        return TestiqleyecekShexs::TIP_REDAKT_EDEN === $approvingUser['type'];
                    }),
                    "renderConfirmingUser",
                    '<i>Yoxdur</i>'
                );
                ?>
            </div>

        <?php if (getProjectName() !== SN): ?>
            <div  data-position="<?php print addButtonPositionKey($elementler, 'viza_veren_name')?>" id="viza_veren_name">
                <small><?= dsAlt('2616rollar_viza_veren', "Viza verən şəxs"); ?></small>
                <br>
                <?php
                View::altPrintArray(
                    array_filter($approvingUsers, function ($approvingUser) {
                        return TestiqleyecekShexs::TIP_VISA_VEREN === $approvingUser['type'];
                    }),
                    "renderConfirmingUser",
                    '<i>Yoxdur</i>'
                );
                ?>
            </div>
        <?php endif; ?>
            <div  data-position="<?php print addButtonPositionKey($elementler, 'cap_edecek_name')?>" id="cap_edecek_name">
                <small><?= dsAlt('2616rollar_chap_edecek', "Çap edəcək şəxs"); ?></small>
                <br>
                <?php
                View::altPrintArray(
                    array_filter($approvingUsers, function ($approvingUser) {
                        return TestiqleyecekShexs::TIP_CHAP_EDEN === $approvingUser['type'];
                    }),
                    "renderConfirmingUser",
                    '<i>Yoxdur</i>'
                );
                ?>
            </div>
        <?php endif; ?>
        <?php if($appealTypeOptional!==false && count($appealTypeOptional)>0): ?>
            <div data-position="<?php print addButtonPositionKey($elementler, 'document_number_status')?>" id="document_number_status">
                <small>
                    <?php print dsAlt('2616etrafli_status_baglan', "Sənədin statusu bağlansın olaraq"); ?>
                </small>
                <br>
                <strong>
                    <?php foreach ($outgoingDocument->getOptionalAppealTypeChecked() as $related_document) : ?>
                        <?php $related_document['dos_status']==2 ? cap($related_document['document_number'].' - (Seçilib)') : cap($related_document['document_number'].' - (Seçilməyib)'); ?><br>
                    <?php endforeach; ?>
                </strong>
            </div>
        <?php endif; ?>
        <div data-position="<?php print addButtonPositionKey($elementler, 'mektubun_qisa_mezmunu')?>" id="mektubun_qisa_mezmunu">
            <span><?php print dsAlt('2616etrafli_mektub_qm', "Məktubun qısa məzmunu"); ?></span><br> <strong><?php cap($senedler['qisa_mezmun_name']) ?></strong>
        </div>

        <div data-position="<?php print addButtonPositionKey($elementler, 'qisa_mezmun_name')?>" id="qisa_mezmun_name">
            <small> <?php print (getProjectName() === TS) ? dsAlt('2616daxil_olan_qisa_mezmun', "Qısa məzmun") : dsAlt('2616etrafli_qeyd', 'Qeyd') ?> </small>
            <br>&nbsp;<?php cap($senedler['qeyd']) ?>
        </div>

        <div data-position="<?php print addButtonPositionKey($elementler, 'tree')?>" id="tree" class="row" style="font-size: 13px; font-weight: 100;">
            <div class="col-md-9">
                <div class="document-tree">
                    <ul>
                        <li data-jstree='{"icon":"fa fa-folder fa-lg icon-state-warning"}'
                            class="jstree-open"><?php cap($senedler['document_number']) ?>
                            <ul>
                                <li data-jstree='{"icon":"fa fa-folder fa-lg icon-state-success"}'>
                                    <?=  dsAlt('2616qeydiyyat_pencereleri_elaqeli', "Əlaqəli sənəd"); ?>
                                    <ul>
                                        <?php foreach ($relatedInternalDocuments as $relatedOutgoingDocument): ?>
                                            <li
                                                    data-jstree='{"icon":"fa fa-file-text-o"}'
                                                    data-href="index.php?module=prodoc_new&id=<?= $relatedOutgoingDocument['id'] ?>&bolme=prodoc_sened_qeydiyyatdan_kecib"
                                            >
                                                <?= $relatedOutgoingDocument['document_number'] ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-3 doc_relation" data-toggle="tooltip" data-placement="top" title="Sənəd əlaqələndirmə" style=" cursor: pointer; color:#1BBC9B; font-size:16px;">
                <i class="fa fa-file "><span style="color:	#696969;position:absolute;margin-left: 7px;font-family: 'Segoe UI', sans-serif;font-size: 14px">Əlaqələndir</span></i>
            </div>
        </div>

    </div>
    <link rel="stylesheet" href="asset/global/plugins/jstree/dist/themes/default/style.min.css"/>
    <script src="asset/global/plugins/jstree/dist/jstree.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="prodoc/app.js"></script>
    <script>
        var result = <?php echo json_encode($elementler); ?>;

        loadEtrafliFront(".container-etrafli-xos",result);

        <?php if($senedlerin_etraflisinin_tenzimlenmesi): ?>

            sortEtrafliFront('.container-etrafli-xos', "<?php print $key_doc; ?>");

        <?php endif; ?>

        <?php if($checkCertificate): ?>

        $('#ixracFormasi').find('a.ixrac_link_yoxlama').on('click', function (e) {
            $.post('prodoc/pages/certificate.php', {
                'sid':<?= $sid ?>,
                'checkStatus': 1,
                'tip': 2
            }, function (result) {
                result = JSON.parse(result);
                if (result.status == "success") {
                    // $('#ixracFormasi').find('a.ixrac_link').trigger('click');
                    window.location.href = "prodoc/pages/certificate.php?id=<?= $sid ?>&tip=2&amp;export=word";

                }
                else {
                    swals(result.errorMsg, "", "error");
                }
            });

            e.preventDefault();
        })

        <?php endif; ?>

        loadingTreeTemplate($("#sened-elaveler-body"));
        loadingTreeTemplate($("#sened-elaveler-body-elaqeli-sened"));

        function loadingTreeTemplate(container){
            container
                .find('.document-tree')
                .on('activate_node.jstree', function (node, event) {
                    if (_.isUndefined(event.node.data.href))
                        return;

                    location.href = event.node.data.href;
                })
                .jstree()
            ;
        }


        var documet_number = $('.document_number_etrafli').text().trim();
        $("#senedler-tbody .selected").find('.document_number').text(documet_number);
        function showTemplateIsheTik() {
            $('.doc_relation ').unbind('click');

            templateYukle('related_document',' Sənəd əlaqələndirmə ',{'tip': 'daxil_olan_sened','all_operation':1, 'outgoing_document_id': '<?php print $senedler['id'] ?>' },40,true,'green-meadow');

            // $('.ishe_tik .sherh').bind('click', showTemplateIsheTik);
            setTimeout(function() {
                $('.doc_relation').on('click', showTemplateIsheTik);
            }, 500);
        }

        $('.doc_relation').on('click', showTemplateIsheTik);

    </script>
</div>