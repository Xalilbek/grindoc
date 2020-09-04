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
    <div data-position="<?php print addButtonPositionKey($elementler, 'sened_legv_olunub'); ?>" id="sened_legv_olunub">
        <?php if((int)$poa['state'] === Document::STATE_IN_TRASH): ?>
            <div class="alert alert-danger" style="padding: 13px; color: #ff0000;">
                <strong><?= dsAlt('2616etrafli_legv', "Sənəd ləğv olunub")?></strong>
            </div>
        <?php endif; ?>
    </div>
    <?php print $intDoc->getDetailedInformationHTML(['elementler' => $elementler]) ?>
    <div data-position="<?php print addButtonPositionKey($elementler, 'icra_muddeti_deyishdirilmesi'); ?>" id="icra_muddeti_deyishdirilmesi">
        <small><?php print dsAlt('2616etrafli_icra_muddet_deyish_sened', "İcra müddətinin dəyişdirildiyi sənəd"); ?>:</small><br>
        <span><?php print htmlspecialchars($senede_bagli_nomre['document_number']); ?></span>
    </div>
    <div data-position="<?php print addButtonPositionKey($elementler, 'senedin_tarixi'); ?>" id="senedin_tarixi">
        <small><?php print dsAlt('2616etrafli_sened_tarix', "Sənədin Tarixi"); ?></small><br>
        <span><?php tarixCapEt($poa['senedin_tarixi']); ?></span>
    </div>
    <div data-position="<?php print addButtonPositionKey($elementler, 'cari_icra_tarixi'); ?>" id="cari_icra_tarixi">
        <small><?php print dsAlt('2616etrafli_cari_icra', "Cari icra tarixi"); ?></small><br>
        <span><?php tarixCapEt($senede_bagli_nomre['icra_edilme_tarixi']); ?></span>
    </div>
    <div data-position="<?php print addButtonPositionKey($elementler, 'icra_muddeti_muraciet_olunan_tarix'); ?>" id="icra_muddeti_muraciet_olunan_tarix">
        <small><?php print dsAlt('2616etrafli_yeni_icra', "Yeni icra tarixi"); ?>:</small><br>
        <span><?php tarixCapEt($poa['icra_muddeti_muraciet_olunan_tarix']); ?></span>
    </div>
    <div data-position="<?php print addButtonPositionKey($elementler, 'mektubun_qisa_mezmunu'); ?>" id="mektubun_qisa_mezmunu">
        <small><?php print dsAlt('2616etrafli_mektub_qm', "Məktubun qısa məzmunu"); ?>:</small><br>
        <span style="word-wrap: break-word;" ><?php print htmlspecialchars($poa['qisa_mezmun']); ?></span>
    </div>
    <div data-position="<?php print addButtonPositionKey($elementler, 'qisa_mezmun_name'); ?>" id="qisa_mezmun_name">
        <small><?php print (getProjectName() === TS)?  "Qısa məzmun": dil::soz('2616etrafli_qeyd')?>:</small><br>
        <span style="word-wrap: break-word;" ><?php print htmlspecialchars($poa['qeyd']); ?></span>
    </div>

    <?php print $intDoc->getRelatedInternalDocumentsHTMLTree([], $elementler); ?>
</div>
<script>
    var result = <?php echo json_encode($elementler); ?>;
    loadEtrafliFront(".internal-documents-details", result);

    <?php if($senedlerin_etraflisinin_tenzimlenmesi): ?>
        sortEtrafliFront('.internal-documents-details', "ds_icra_muddeti_deyisdirilmesi");
    <?php endif; ?>
</script>

