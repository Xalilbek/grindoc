<style>
    .internal-documents-details > div{
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

<div class="internal-documents-details">
    <?php print $intDoc->getDetailedInformationHTML(['hidden_fields' => ['testiq', 'melumat'], 'elementler' => $elementler]) ?>

    <div data-position="<?php print addButtonPositionKey($elementler, 'senedin_tipi'); ?>" id="senedin_tipi">
        <small><?= dsAlt('2616etrafli_sened_tip', "Sənəd tipi"); ?>:</small><br>
        <span><?php print htmlspecialchars($poa['senedin_tipi']); ?></span>
    </div>
    <div data-position="<?php print addButtonPositionKey($elementler, 'sened_novu'); ?>" id="sened_novu">
        <small><?= dsAlt('2616senedin_novu', "Sənədin növü"); ?>:</small><br>
        <span><?php print htmlspecialchars($poa['senedin_novu']); ?></span>
    </div>
    <div data-position="<?php print addButtonPositionKey($elementler, 'senedin_tarixi'); ?>" id="senedin_tarixi">
        <small><?= dsAlt('2616etrafli_sened_tarix', "Sənədin Tarixi"); ?>:</small><br>
        <span><?php tarixCapEt($poa['senedin_tarixi']); ?></span>
    </div>

    <?php if(is_null($poa['sened_nov_xidmeti_mektub'])): ?>
        <?php if(!is_null($poa['icra_edilme_tarixi']) && $poa['sened_tip']==2): ?>
            <div data-position="<?php print addButtonPositionKey($elementler, 'son_icra_tarixi_to'); ?>" id="son_icra_tarixi_to">
                <small><?= dsAlt(TELEB_OLUNAN_TARIX_AD, "Tələb olunan tarix") ?></small><br><strong><?php tarixCapEt($poa['icra_edilme_tarixi'], 'd-m-Y') ?></strong>
            </div>
        <?php endif; ?>
        <?php if(!is_null($poa['son_icra_tarixi']) && $poa['sened_tip']==2): ?>
            <div  data-position="<?php print addButtonPositionKey($elementler, 'son_icra_tarixi'); ?>" id="son_icra_tarixi">
                <small><?= dsAlt('2616son_icra_tarixi', "Son icra tarixi"); ?></small><br><strong><?php tarixCapEt($poa['son_icra_tarixi'], 'd-m-Y') ?></strong>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <div data-position="<?php print addButtonPositionKey($elementler, 'kime'); ?>" id="kime">
            <p><small><?= dsAlt('2616rollar_kime', "Kimə"); ?>/small></p>
            <?php foreach ($formTesdiq as $value): ?>
                <?php if($value['emeliyyat_tip'] == 'tesdiqleme'): ?>
                    <p><strong><?= $proxy->getProxyNameByPrincipal($value['USERID'], $value['user_ad']); ?></strong></p>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>


    <div data-position="<?php print addButtonPositionKey($elementler, 'son_emeliyyat'); ?>" id="son_emeliyyat">
        <small><?= dsAlt('2616etrafli_son_emeliyyat', "Son əməliyyat"); ?></small><br> <strong><?php son_emeliyyat($taskIds, $poa['document_id']) ?></strong>
    </div>
    <div data-position="<?php print addButtonPositionKey($elementler, 'netice_ad'); ?>" id="netice_ad">
        <small><?= dsAlt('2616daxil_olan_senedin_neticesi', "Sənədin nəticəsi"); ?></small><br>
        <span style="word-wrap: break-word;" ><?php print htmlspecialchars($netice); ?></span>
    </div>
    <div data-position="<?php print addButtonPositionKey($elementler, 'document_number_status'); ?>" id="document_number_status">
        <small><?= dsAlt('2616etrafli_status_baglan', "Sənədin statusu bağlansın olaraq"); ?>:</small><br>
        <strong>
            <?php foreach ($intDoc->getOptionalAppealTypeChecked() as $related_document) : ?>
                <?php $related_document['dos_status']==2 ? cap($related_document['document_number'].' - (Seçilib)') : cap($related_document['document_number'].' - (Seçilməyib)'); ?><br>
            <?php endforeach; ?>
        </strong>
    </div>
    <div data-position="<?php print addButtonPositionKey($elementler, 'mektubun_qisa_mezmunu'); ?>" id="mektubun_qisa_mezmunu">
        <small> <?= dsAlt('2616dos_mektub_qm', "Məktubun qısa məzmunu"); ?> :</small><br>
        <span style="word-wrap: break-word;" ><?php print htmlspecialchars($poa['qisa_mezmun']); ?></span>
    </div>
    <div data-position="<?php print addButtonPositionKey($elementler, 'derkenar_metni'); ?>" id="derkenar_metni">
        <small><?= dsAlt('2616derkenar_metni', "Dərkənar mətni"); ?>:</small><br>
        <span style="word-wrap: break-word;" ><?php
            $derkenar_ad = '';
            if ($poa['derkenar_metn_ad_edit'] == ''){
                $derkenar_ad = $poa['derkenar_metn_ad'];
            }else if($poa['derkenar_metn_ad'] != '' && $poa['derkenar_metn_ad_edit'] != ''){
                $derkenar_ad = $poa['derkenar_metn_ad_edit'];
            }
            print htmlspecialchars($derkenar_ad);

            ?>
        </span>
    </div>
    <div data-position="<?php print addButtonPositionKey($elementler, 'qisa_mezmun_name'); ?>" id="qisa_mezmun_name">
        <small><?php print (getProjectName() === TS)?  dsAlt('2616qisa_mezmun', "Qısa məzmun") : dsAlt('2616etrafli_qeyd', "Qeyd") ?>:</small><br>
        <span style="word-wrap: break-word;" ><?php print htmlspecialchars($poa['qeyd']); ?></span>
    </div>
    <?php print $intDoc->getRelatedInternalDocumentsHTMLTree([], $elementler); ?>
</div>

<script>
    var result = <?php echo json_encode($elementler); ?>;
    loadEtrafliFront(".internal-documents-details", result);


    <?php if($senedlerin_etraflisinin_tenzimlenmesi): ?>
        sortEtrafliFront('.internal-documents-details', "<?=$key?>");
    <?php endif; ?>
</script>

