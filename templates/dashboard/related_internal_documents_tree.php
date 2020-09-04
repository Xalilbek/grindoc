<?php ob_start() ?>
<div class="document-tree" data-position="<?php print addButtonPositionKey($params, 'tree')?>" id="tree">
    <ul>
        <li data-jstree='{"icon":"fa fa-folder fa-lg icon-state-warning"}' class="jstree-open"><?php cap($documentNumber) ?>
            <ul>
                <li data-jstree='{"icon":"fa fa-folder fa-lg icon-state-success"}' >
                    <?= (getProjectName() === BIZIM_MARKET) ? 'Əlaqəli tapşırıq' : dsAlt('2616qeydiyyat_pencereleri_elaqeli','Əlaqəli sənəd') ?>
                    <ul>
                        <?php foreach ($relatedDocuments as $relatedOutgoingDocument):
							$bolme = 'outgoing' === $relatedOutgoingDocument['type'] ? 'kurator' : 'prodoc_sened_qeydiyyatdan_kecib';
						?>
                            <li
                                data-jstree='{"icon":"fa fa-file-text-o"}'
                                data-href="index.php?module=prodoc_new&id=<?= $relatedOutgoingDocument['id'] ?>&bolme=<?=$bolme?>"
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

<link rel="stylesheet" href="asset/global/plugins/jstree/dist/themes/default/style.min.css" />
<script src="asset/global/plugins/jstree/dist/jstree.js"></script>
<script>
	var container = $("#sened-elaveler-body");
	container
		.find('.document-tree')
		.on('activate_node.jstree', function(node, event) {
			if (_.isUndefined(event.node.data.href))
				return;

			location.href = event.node.data.href;
		})
		.jstree()
	;
</script>
<?php $content = ob_get_contents(); ob_end_clean(); return $content; ?>