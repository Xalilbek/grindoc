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
    <div data-position="<?php print addButtonPositionKey($elementler, 'sifarish_tarixi'); ?>" id="sifarish_tarixi">
        <small><?php print dsAlt('2616etrafli_sifarish_tarix', "Sifariş tarixi"); ?>:</small><br>
        <span><?php print htmlspecialchars($poa['sifaris_tarixi']); ?></span>
    </div>
    <div data-position="<?php print addButtonPositionKey($elementler, 'sifarish_shobesi'); ?>" id="sifarish_shobesi">
        <small><?php print dsAlt('2616etrafli_sifarish_shobe', "Sifarişin şöbəsi"); ?>:</small><br>
        <span><?php print htmlspecialchars($poa['sifarisci_shobesi']); ?></span>
    </div>
    <div data-position="<?php print addButtonPositionKey($elementler, 'sifarish_tipi'); ?>" id="sifarish_tipi">
        <small><?php print dsAlt('2616etrafli_sifarish_tip', '"Sifariş tipi"'); ?>:</small><br>
        <span><?php print htmlspecialchars($poa['sifarisci_tipi']); ?></span>
    </div>
    <div data-position="<?php print addButtonPositionKey($elementler, 'senedin_tarixi'); ?>" id="senedin_tarixi">
        <small><?php print dsAlt('2616etrafli_sened_tarix', "Sənədin Tarixi"); ?>:</small><br>
        <span><?php print htmlspecialchars($poa['senedin_tarixi']); ?></span>
    </div>
    <div data-position="<?php print addButtonPositionKey($elementler, 'sifarish'); ?>" id="sifarish">
        &nbsp;<i class="fa fa-file-text-o"></i> <a href='javascript: templateYukle("satin_alma","Mal-Material",{"sender_id": <?php print $poa['sened_id']; ?>, "edit": false },80,true,"green");'>Sifariş</a>
    </div>
    <?php if($sifaris_netice): ?>
        <div data-position="<?php print addButtonPositionKey($elementler, 'sifarishin_neticesi'); ?>" id="sifarishin_neticesi">
            &nbsp;<i class="fa fa-file-text-o"></i> <a href='javascript: templateYukle("netice_satin_alma","Sifariş forması",{"sender_id": <?php print $poa['sened_id']; ?>, "edit": false },80,true,"red");'>
                <?= dsAlt('2616etrafli_sifarish_netice', 'Sifarişin nəticəsi')?>
            </a>
        </div>
    <?php endif; ?>
    <div data-position="<?php print addButtonPositionKey($elementler, 'son_emeliyyat'); ?>" id="son_emeliyyat">
        <small><?php print dsAlt('2616etrafli_son_emeliyyat', "Son əməliyyat"); ?>:</small><br> <strong><?php son_emeliyyat([], $poa['document_id']) ?></strong>
    </div>
    <div data-position="<?php print addButtonPositionKey($elementler, 'qeyd'); ?>" id="qeyd">
        <small><?php print dsAlt('2616etrafli_qeyd', "Qeyd"); ?>:</small><br>
        <span style="word-wrap: break-word;" ><?php print htmlspecialchars($poa['qeyd']); ?></span>
    </div>
    <?php print $intDoc->getRelatedInternalDocumentsHTMLTree([], $elementler); ?>
</div>

<script>
    var result = <?php echo json_encode($elementler); ?>;
    loadEtrafliFront(".internal-documents-details", result);

    <?php if($senedlerin_etraflisinin_tenzimlenmesi): ?>
        sortEtrafliFront('.internal-documents-details', "ds_satin_alma");
    <?php endif; ?>
</script>

