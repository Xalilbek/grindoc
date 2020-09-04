<?php
require_once DIRNAME_INDEX . 'prodoc/Util/View.php';
require_once DIRNAME_INDEX . 'prodoc/Util/ArrayUtils.php';
require_once DIRNAME_INDEX . 'prodoc/includes/internal_document.php';

?>

<?php if ($fromParent): ?>
<div data-parent-li="<?php printf('%s-%s', $relatedKey, $relatedId); ?>" style="padding-left: 25px ;background-color:  #E8E8E8;">
<?php else:  ?>
<div>
<?php endif;
//var_dump($historyRecords);exit(); ?>
	<div class="mt-element-list">
		<div class="mt-list-container list-news" style="border-left: none; border-right: none">
			<ul>
                <?php foreach ($historyRecords as $historyRecord): ?>
                    <?php
						$relatedKeyAndOperation =  $historyRecord['related_key'] . '_' . $historyRecord['operation'];
						$operation = $historyRecord['operation'];

						if (isset($relatedKeyAndOperationTextMap[$relatedKeyAndOperation])) {
							if ($relatedKeyAndOperation === "appeal_registration" && !empty($historyRecord['internal_document_type'])) {
                                $operationText = $historyRecord['internal_document_type'] . " qeydiyyatdan keçirilib.";
                            } else {
                                $operationText = $relatedKeyAndOperationTextMap[$relatedKeyAndOperation];
                            }
						} else if (isset($operationTextMap[$operation])) {
							$operationText = $operationTextMap[$operation];
						} else {
							continue;
						}

						$isOutgoingDocRegistrationOrEdit = $isTaskRegistrationOrEdit = false;
						if ('outgoing_document_registration' === $relatedKeyAndOperation || 'outgoing_document_edit' === $relatedKeyAndOperation) {
							$isOutgoingDocRegistrationOrEdit = true;

							$teyinat_ad            = findColumnValue('teyinat_ad', $historyRecord);
							$gonderen_teshkilat_ad = findColumnValue('gonderen_teshkilat_ad', $historyRecord);

							$operationText = sprintf($operationText, $teyinat_ad);
						} else if ('task_registration' === $relatedKeyAndOperation || 'task_edit' === $relatedKeyAndOperation) {
							$isTaskRegistrationOrEdit = true;

							$mesul_shexs_ad = findColumnValue('mesul_shexs_ad', $historyRecord);
                            $melumatlandiran_shexs = findColumnValueTaskUsers('melumat', $historyRecord['related_record_id']);
						}
                    ?>

					<li class="mt-list-item"
                        data-id="<?php print $historyRecord['id']; ?>"
						data-related-key="<?php print $historyRecord['related_key']; ?>"
						data-related-id="<?php print $historyRecord['related_record_id']; ?>">

						<?php if (('daxil_olan_sened' === $tip || 'task' === $tip) && ('task_registration' === $relatedKeyAndOperation || 'outgoing_document_registration' === $relatedKeyAndOperation)): ?>
							<div class="list-icon-container">
								<a href="javascript:;" class="show-detailed-history">
									<i class="fa fa-angle-left"></i>
								</a>
							</div>
                        <?php endif; ?>

						<div class="list-datetime bold uppercase font-green"> <?php tarixCapEt($historyRecord['created_at']) ?> </div>
						<div class="list-item-content">
							<h3 class="">
								<a href="javascript:;"><?php cap(mb_strtoupper_az($operationText)) ?></a>
							</h3>

                            <?php if ($isOutgoingDocRegistrationOrEdit): ?>
								<span class="text-muted">
									<i class="fa fa-list"></i> Hara göndərilib: <?php print $gonderen_teshkilat_ad; ?>
								</span>
								<br>
                            <?php endif; ?>

							<span class="text-muted">
								<i class="fa fa-user"></i> Əməliyyatçı: <?php cap($historyRecord['user_name']) ?>
							</span>


                            <?php if ($isTaskRegistrationOrEdit && $senedTip == Document::SENED_TIP_ICRA_UCHUN): ?>
								<br>
								<span class="text-muted">
									<i class="fa fa-user"></i> İcraçı:   <?php print $mesul_shexs_ad; ?>
								</span>
                            <?php elseif ($isTaskRegistrationOrEdit && $senedTip == Document::SENED_TIP_MELUMAT_UCHUN): ?>
                                <br>
                                <span class="text-muted">
									<i class="fa fa-user"></i> Məlumatlandırılan şəxslər: <?php print $melumatlandiran_shexs; ?>
								</span>
                            <?php endif; ?>

                            <?php if ('task_registration' === $relatedKeyAndOperation): ?>
								<br>
								<span class="text-muted">
									<i class="fa fa-list"></i> Nömrə: <?php print $historyRecord['related_record_id']; ?>
								</span>
                            <?php endif; ?>
							<br>
							<span class="text-muted">
								         <?php if ('task' === $historyRecord['related_key'] && $historyRecord['operation'] != 'approve_tanish_ol'): ?>
                                             <i class="fa fa-pencil"></i> Dərkənar mətni: - <?php \Util\View::altPrint($historyRecord['note'], '<i>Yoxdur</i>'); ?>
                                         <?php else: ?>
                                             <i class="fa fa-pencil"></i> Qeyd: - <?php \Util\View::altPrint($historyRecord['note'], '<i>Yoxdur</i>'); ?>
                                         <?php endif; ?>
							</span>
                            <?php if(!is_null($historyRecord['document_number'])&&$historyRecord['document_number']!=""): ?>
                                <br>
                                <span class="text-muted">
                                    <i class="fa fa-user"></i> Vəkalətnamə üzrə
<!--                                    : --><?php //cap($historyRecord['document_number']) ?>
                                </span>
                            <?php endif; ?>
                            <br>

                            <?php if (isset($historyRecordFiles[$historyRecord['id']])): ?>
                                <span class="text-muted">
									<?php
                                    $historyRecordFilesHTML = "";
                                    $title = "Fayl";

                                    $historyRecordFilesHTML = implode(',', array_map(function($file) {
                                        return sprintf(
                                            "<i class='fa %s'></i><a rel='noopener' target='_blank' href='".PRODOC_FILES_WEB_PATH."%s'>%s</a>",
                                            \View\Helper\File::getFAIconCSSClass($file['file_actual_name']),
                                            $file['file_actual_name'],
                                            $file['file_original_name']
                                        );
                                    }, $historyRecordFiles[$historyRecord['id']]));

                                    if (count($historyRecordFiles[$historyRecord['id']]) > 1) {
                                        $title = "Fayllar";
                                    }
                                    ?>

                                    <i class="fa fa-file"></i> <?php print $title; ?>: <?php \Util\View::altPrint($historyRecordFilesHTML, '<i>Yoxdur</i>'); ?>
								</span>
                            <?php endif; ?>
						</div>
					</li>
                <?php endforeach; ?>

			</ul>
		</div>
	</div>
</div>

<script>
	var documentDetailedInformation = $("#sened-elaveler");

	documentDetailedInformation
		.off('click', 'a.show-detailed-history')
		.on('click', 'a.show-detailed-history', function () {
			var closestLi = $(this).closest('li');

			var isClosed = closestLi.find('i.fa.fa-angle-left').length > 0;

			var currentState;
			if (isClosed) {
				closestLi.find('i.fa.fa-angle-left').removeClass('fa-angle-left').addClass('fa-angle-down');
				currentState = 'opened';
			} else {
				closestLi.find('i.fa.fa-angle-down').removeClass('fa-angle-down').addClass('fa-angle-left');
				currentState = 'closed';
			}

			var data = {};
			data.relatedKey = closestLi.data('related-key');
			data.relatedId  = closestLi.data('related-id');

			var childUl = documentDetailedInformation.find(s.sprintf('div[data-parent-li=%s-%s]', data.relatedKey, data.relatedId));
			if ('opened' === currentState) {
				if (childUl.length) {
					childUl.slideDown();
					return;
				}

				sehifeLoading(1);
				data.from_parent = 1;
				$.post('prodoc/ajax/dashboard/tarixce/umumi.php', data, function(res) {
					var html = res.html;

					$(html).insertAfter(closestLi);
					sehifeLoading(0);
				}, 'json');
			} else {
				childUl.slideUp();
			}
	});

</script>