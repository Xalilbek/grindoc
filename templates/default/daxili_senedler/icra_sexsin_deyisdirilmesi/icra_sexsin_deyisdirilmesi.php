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
    <div data-position="<?php print addButtonPositionKey($elementler, 'icra_eden_shexsin_deyishdirildiyi_sened'); ?>" id="icra_eden_shexsin_deyishdirildiyi_sened">
        <small><?php print dsAlt('2616etrafli_icra_shexs_deyish_sened', "İcra edən şəxsinin dəyişdirildiyi sənəd"); ?>:</small><br>
        <span><?php print htmlspecialchars($deyisdirilmesi_sened); ?></span>
    </div>
    <div data-position="<?php print addButtonPositionKey($elementler, 'senedin_tarixi'); ?>" id="senedin_tarixi">
        <small><?php print dsAlt('2616etrafli_sened_tarix', "Sənədin Tarixi"); ?>:</small><br>
        <span><?php print htmlspecialchars($poa['senedin_tarixi']); ?></span>
    </div>



    <div data-position="<?php print addButtonPositionKey($elementler, 'mektubun_qisa_mezmunu'); ?>" id="mektubun_qisa_mezmunu">
        <small><?php print dsAlt('2616etrafli_mektub_qm', "Məktubun qısa məzmunu"); ?>:</small><br>
        <span style="word-wrap: break-word;" ><?php print htmlspecialchars($poa['qisa_mezmun']); ?></span>
    </div>
    <div data-position="<?php print addButtonPositionKey($elementler, 'netice_ad'); ?>" id="netice_ad">
        <small><?php print dsAlt('2616etrafli_netice', "Nəticə"); ?>:</small><br>
        <span style="word-wrap: break-word;" ><?php print htmlspecialchars($poa['neticesi']); ?></span>
    </div>
    <div data-position="<?php print addButtonPositionKey($elementler, 'qisa_mezmun_name'); ?>" id="qisa_mezmun_name">
        <small><?php print (getProjectName() === TS)?  dsAlt('2616daxil_olan_qisa_mezmun', "Qısa məzmun"): dsAlt('2616etrafli_qeyd', "Qeyd") ?>:</small><br>
        <span style="word-wrap: break-word;" ><?php print htmlspecialchars($poa['qeyd']); ?></span>
    </div>
    <?php print $intDoc->getRelatedInternalDocumentsHTMLTree([], $elementler); ?>
</div>

<script>
    var result = <?php echo json_encode($elementler); ?>;
    loadEtrafliFront(".internal-documents-details", result);

    <?php if($senedlerin_etraflisinin_tenzimlenmesi): ?>
        sortEtrafliFront('.internal-documents-details', "ds_icra_sexsin_deyisdirilmesi");
    <?php endif; ?>
</script>

