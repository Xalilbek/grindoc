<div class="mt-element-list" id="derkenarlar">
    <div class="mt-list-container list-news" style="border-left: none; border-right: none">
        <ul>
            <?php foreach ($derkenarlar as $derkenar): ?>
                <?php

                $derkenarObject = new Task($derkenar['id'], ['data' => $derkenar]);

                $titleClass = '';
                $emeliyyat    = '';

                if($derkenar['status'] == Task::STATUS_IMTINA_OLUNUB)
                {
                    $titleClass = 'imtina';
                }

                if ($derkenar['tip'] == 'sened_hazirla' || $derkenar['related_document_id'] > 0) {
                    $emeliyyat = 'Sənəd hazirla';
                } elseif ($derkenar['tip'] == "" && (int)$derkenar['parentTaskID'] > 0) {
                    $emeliyyat = 'Alt dərkənar';
                } elseif ($derkenar['tip'] == 'ishe_tik') {
                    $emeliyyat = 'İşə alınıb';
                } else {
                    $emeliyyat = 'Yoxdu';
                }

                $titleStyle = '';
                if ($titleClass === 'imtina') {
                    $titleStyle = 'class="font-red"';
                }
                ?>

            <li class="mt-list-item"
                data-related-key="<?php print $derkenar['tip']; ?>"
                data-related-id="<?php print $derkenar['muraciet_id']; ?>">
                <div class="list-datetime bold uppercase font-green"> <?php print tarixeCevir($derkenar['elave_olunma_tarixi']) ?> </div>

                    <div class="list-icon-container">
                        <a href="javascript:;" class="show-detailed-history">
                            <i class="fa fa-angle-left"></i>
                        </a>
                    </div>

                <div class="list-item-content" style="word-wrap: break-word;">
                    <h3 class="uppercase bold" style="display: flex;align-items: center">
                        <a href="javascript:;" <?= $titleStyle; ?>><?= dsAlt('2616qeydiyyat_pencereleri_derkenar', "Dərkənar"); ?></a>
                        <?php if ($derkenarObject->canEdit()): ?>
                            <a href="index.php?module=prodoc_derkenar&derkenar_id=<?php print $derkenar['id'] ?>&sid=<?php print $derkenar['daxil_olan_sened_id'] ?>"><i class="fa fa-pencil" style="cursor: pointer"></i></a>
                        <?php endif; ?>
                        <?php if (getProjectName() === ANAMA): ?>
                        <a href="pages/prodoc/Derkenar_Forma.php?derkenar_id=<?php print $derkenar['id'] ?>" target="_blank"><i class="fa fa-file"></i></a>
                        <?php endif; ?>
                    </h3>
                    <br>

                    <span class="text-muted">
						<i class="fa fa-user"></i> <?= dsAlt('2616rey_muellifi_dos', "Rəy müəllifi"); ?>: <?php print $daxil_olan_sened['rey_muellifi_ad']; ?>
					</span>
                    <br>

                    <?php if ($daxil_olan_sened['sened_tip'] == Document::SENED_TIP_ICRA_UCHUN ): ?>
                        <span class="text-muted">
                            <i class="fa fa-user"></i> <?= dsAlt('2616icrachi_dos', "İcraçı") ?>: <?php print $derkenar['mesul_shexs_ad']; ?>
                        </span>
                        <br>

                        <span class="text-muted">
                            <i class="fa fa-user"></i> <?= dsAlt('2616rollar_hemicraci', "Həmicraçı"); ?>: <?php print $derkenar['hemIcraci']; ?>
                        </span>
                        <br>

                        <span class="text-muted">
                            <i class="fa fa-user"></i> <?= dsAlt('2616rollar_icraya_nezaret_eden', "İcraya nəzarət edən şəxs"); ?>: <?php print $derkenar['icraya_nezaret_eden_shexs']; ?>
                        </span>
                        <br>

                        <?php if (!is_null($daxil_olan_sened['icra_edilme_tarixi'])): ?>
                            <span class="text-muted">
                                <i class="fa fa-calendar"></i> <?= dsAlt(TELEB_OLUNAN_TARIX_AD, "Tələb olunan tarix") ?>: <?php print tarixeCevir($daxil_olan_sened['icra_edilme_tarixi'], 'd M Y') ?>
                            </span>
                            <br>
                        <?php endif; ?>
                    <?php elseif ($daxil_olan_sened['sened_tip'] == Document::SENED_TIP_MELUMAT_UCHUN): ?>
                        <span class="text-muted">
                            <i class="fa fa-user"></i><?= dsAlt('2616rollar_melumatlandirilan', "Məlumatlandırılan şəxs") ?>: <?php print $derkenar['melumatlandiran_shexs']; ?>
                        </span>
                        <br>
                    <?php endif; ?>



                    <span class="text-muted">
						<i class="fa fa-list"></i><?= dsAlt('2616etrafli_derkenar_metn', "Dərkənar mətni") ?>: <?php print $derkenar['diger_derkenar_metn'] ? : $derkenar['derkenar_metn_ad'] ?>
					</span>
                    <br>

                    <span class="text-muted">
						<i class="fa fa-pencil"></i><?= dsAlt('2616filter_emeliyyat', "Əməliyyat") ?>: <?php print $emeliyyat ?>
					</span>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<script>

    var documentDetailedInformation = $("#derkenarlar");

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
            data.muracietId  = closestLi.data('related-id');
            data.tip         = closestLi.data('related-key');

            var childUl = documentDetailedInformation.find(s.sprintf('div[data-parent-li=%s]', data.muracietId));
            if ('opened' === currentState) {
                if (childUl.length) {
                    childUl.slideDown();
                    return;
                }
                sehifeLoading(1);
                $.post('prodoc/ajax/dashboard/derkenarlar/derkenarlar.php', data, function(res) {
                    var html = res.html;

                    $(html).insertAfter(closestLi);
                    sehifeLoading(0);
                }, 'json');
            } else {
                childUl.slideUp();
            }
        });
</script>



