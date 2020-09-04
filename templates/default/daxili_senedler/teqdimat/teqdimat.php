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
    <div data-position="<?php print addButtonPositionKey($elementler, 'melumatin_qisa_mezmunu'); ?>" id="melumatin_qisa_mezmunu">
        <small><?php print dil::soz('2616etrafli_melumat_qisa_mezmun'); ?>:</small><br>
        <span ><?php print htmlspecialchars($poa['qisa_mezmun']); ?></span>
    </div>
    <div data-position="<?php print addButtonPositionKey($elementler, 'melumatin_metni'); ?>" id="melumatin_metni">
        <small><?php print dil::soz('2616etrafli_melumat_metn'); ?>:</small><br>
        <span ><?php print htmlspecialchars($poa['melumat_metni']); ?></span>
    </div>

    <?php print $intDoc->getRelatedInternalDocumentsHTMLTree([], $elementler); ?>
</div>

<script>
    var result = <?php echo json_encode($elementler); ?>;
    loadEtrafliFront(".internal-documents-details", result);

    <?php if($senedlerin_etraflisinin_tenzimlenmesi): ?>
        sortEtrafliFront('.internal-documents-details', "ds_teqdimat");
    <?php endif; ?>
</script>

