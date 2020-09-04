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
    <?php print $intDoc->getDetailedInformationHTML(['elementler' => $elementler]) ?>

    <div data-position="<?php print addButtonPositionKey($elementler, 'kim'); ?>" id="kim">
        <small>Kim:</small><br>
        <span><?php print htmlspecialchars($poa['principal_name']); ?></span>
    </div>
    <div data-position="<?php print addButtonPositionKey($elementler, 'kime'); ?>" id="kime">
        <small>Kimə:</small><br>
        <span ><?php print htmlspecialchars($poa['proxy_name']); ?></span>
    </div>
    <div data-position="<?php print addButtonPositionKey($elementler, 'ne_vaxtdan'); ?>" id="ne_vaxtdan">
        <small><?php print dil::soz('2616etrafli_ne_vaxtdan'); ?>:</small><br>
        <span ><?php print $poa['start_date']; ?></span>
    </div>
    <div data-position="<?php print addButtonPositionKey($elementler, 'ne_vaxtadek'); ?>" id="ne_vaxtadek">
        <small><?php print dil::soz('2616etrafli_ne_vaxta'); ?>:</small><br>
        <span ><?php print \Util\View::altPrint($poa['end_date'], 'Birdəfəlik'); ?></span>
    </div>
    <div data-position="<?php print addButtonPositionKey($elementler, 'senedler'); ?>" id="senedler">
        <small><?php print dil::soz('2616etrafli_senedler'); ?>:</small><br>
        <?php foreach ($allowedDocs as $allowedDoc): ?>
            <?php if ((int)$allowedDoc === 1): ?>
                Növbəti sənədlər
            <?php elseif ((int)$allowedDoc === 2): ?>
                Cari açıq sənədlər
            <?php elseif ((int)$allowedDoc === 3): ?>
                Əvvəlki bağlı sənədlər
            <?php endif; ?>
            <br>
        <?php endforeach; ?>
    </div>
    <div data-position="<?php print addButtonPositionKey($elementler, 'paralel_icra'); ?>" id="paralel_icra">
        <small>:</small><br>
        <span ><?php Template::showCheckbox($poa['parallelism']); ?></span>
    </div>
    <div data-position="<?php print addButtonPositionKey($elementler, 'tabeliyinde_olanlarin_senedleri'); ?>" id="tabeliyinde_olanlarin_senedleri">
        <small><?php print dil::soz('2616etrafli_tabelik_senedler'); ?>:</small><br>
        <span ><?php Template::showCheckbox($poa['allowed_to_work_with_subordinate_users_docs']); ?></span>
    </div>
    <div data-position="<?php print addButtonPositionKey($elementler, 'qeyd'); ?>" id="qeyd">
        <small><?php print dil::soz('2616etrafli_qeyd'); ?>:</small><br>
        <span style="word-wrap: break-word;" ><?php print htmlspecialchars($poa['note']); ?></span>
    </div>

    <?php print $intDoc->getRelatedInternalDocumentsHTMLTree([],$elementler); ?>
</div>

<script>
    var result = <?php echo json_encode($elementler); ?>;
    loadEtrafliFront(".internal-documents-details", result);


    <?php if($senedlerin_etraflisinin_tenzimlenmesi): ?>
        sortEtrafliFront('.internal-documents-details', "ds_power_of_attorney");
    <?php endif; ?>
</script>

