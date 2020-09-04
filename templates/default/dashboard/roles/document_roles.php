<style>
    .user-header-container {
        left: -15px;
        padding-bottom: 6px;
    }
    .user-header-container .fa-check{
        position: absolute;
        right: 0px;
    }
    .document_numbers{
        padding-top: 14px;
        top: -8px;
        font-family: bold;
        color: #337ab7;
        font-size: 15px;
        position: relative;
    }
    .header-document-container {
        background: rgba(221, 228, 233, 0.98);
        width: 95%;
        left: 15px;
        border-bottom: 2px solid #efefef;
    }

    .header-document-container .fa-chevron-up {
        margin-top: 10px;
        margin-left: 100%;
    }

</style>
<div class="row">
    <div class="col-md-11" style="<?= $statusDisplay ?>">
        <div class="status_roles">Status</div>
        <div class="background-color-container" style="margin-top: 6px;
        width: 70px;
        height: 30px;
        color: white;
        border-radius: 4px !important;">
            <div style="font-size: 20px;margin-left: 14px;"><?= $statusTitle ?></div>
        </div>
    </div>
    <div class="col-md-12">
        <div class=" col-md-12 user-info-container"><h1 class="head-username"><?= dsAlt('2616rollar_qeydiyyatci', "Qeydiyyatçı"); ?>:</h1></div>
        <div class="col-md-12 user-header-container">
            <div style="position: relative; outline: 0px; height: 28px; width: 28px;" class="col-md-3">
                <?= viewPhoto($document_base_roles[$documentInfo['created_by']]['uphoto'], $document_base_roles[$documentInfo['created_by']]['uphotoName']) ?>
            </div>
            <div class=" col-md-9 base-username">
                <label>
                    <?= htmlspecialchars($document_base_roles[$documentInfo['created_by']]['Adi'] . ' ' . $document_base_roles[$documentInfo['created_by']]['Soyadi']) ?>
                    /
                    <?= htmlspecialchars($document_base_roles[$documentInfo['created_by']]['struktur_bolmesi']) ?> /
                    <?= htmlspecialchars($document_base_roles[$documentInfo['created_by']]['vezife']) ?>

                </label>

            </div>
        </div>
    </div>
    <?php if (array_key_exists('rey_muellifi', $documentInfo) && !is_null($documentInfo['rey_muellifi']) && $document->isInComing()): ?>
        <div class="col-md-12">
            <div class=" col-md-12 user-info-container"><h1 class="head-username"><?= dsAlt('2616rey_muellifi_dos', 'Rəy müəllifi')?>:</h1></div>
            <div class="col-md-12 user-header-container">
                <div style="position: relative; outline: 0px; height: 28px; width: 28px;" class="col-md-3">
                    <?= viewPhoto($document_base_roles[$documentInfo['rey_muellifi']]['uphoto'], $document_base_roles[$documentInfo['rey_muellifi']]['uphotoName']) ?>

                </div>
                <div class=" col-md-9 base-username">
                    <label>
                        <?= htmlspecialchars($document_base_roles[$documentInfo['rey_muellifi']]['Adi'] . ' ' . $document_base_roles[$documentInfo['rey_muellifi']]['Soyadi']) ?>
                        /
                        <?= htmlspecialchars($document_base_roles[$documentInfo['rey_muellifi']]['struktur_bolmesi']) ?>
                        /
                        <?= htmlspecialchars($document_base_roles[$documentInfo['rey_muellifi']]['vezife']) ?>

                    </label>

                </div>
            </div>
        </div>
    <?php endif; ?>


    <?php if ((count($documentParticipants) > 0) === true): ?>
        <?php if (array_key_exists('derkenar', $documentParticipants) && (count($documentParticipants['derkenar']) > 0) === true ): ?>
            <div class="col-md-12">
                <div class=" col-md-12 user-info-container"><h1 class="head-username"><?= dsAlt('2616rey_muellifi_dos', "Rəy müəllifi") ?>:</h1></div>
                <?php foreach ($documentParticipants['derkenar'] as $documentParticipant) : ?>
                    <div class="col-md-12 user-header-container">
                        <div style="position: relative; outline: 0px; height: 28px; width: 28px;" class="col-md-3">
                            <?= viewPhoto($document_base_roles[$documentParticipant['USERID']]['uphoto'], $document_base_roles[$documentParticipant['USERID']]['uphotoName']) ?>
                        </div>
                        <div class=" col-md-9 base-username">
                            <label>
                                <?= htmlspecialchars($document_base_roles[$documentParticipant['USERID']]['Adi'] . ' ' . $document_base_roles[$documentParticipant['USERID']]['Soyadi']) ?>
                                /
                                <?= htmlspecialchars($document_base_roles[$documentParticipant['USERID']]['struktur_bolmesi']) ?>
                                /
                                <?= htmlspecialchars($document_base_roles[$documentParticipant['USERID']]['vezife']) ?>

                            </label>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (array_key_exists('tesdiqleme', $documentParticipants) && (count($documentParticipants['tesdiqleme']) > 0) === true): ?>
            <div class="col-md-12">
                <div class=" col-md-12 user-info-container"><h1 class="head-username">
                        <?php if ($internal_document_type_key == 'umumi_forma'): ?>
                            <?= dsAlt('2616rollar_kim', "Kim"); ?>
                        <?php else : ?>
                            <?= dsAlt('2616rollar_tesdiqleme', "Təsdiqləmə"); ?>
                        <?php endif; ?>


                        :</h1></div>
                <?php foreach ($documentParticipants['tesdiqleme'] as $documentParticipant) : ?>
                    <div class="col-md-12 user-header-container">
                        <div style="position: relative; outline: 0px; height: 28px; width: 28px;" class="col-md-3">
                            <?= viewPhoto($document_base_roles[$documentParticipant['USERID']]['uphoto'], $document_base_roles[$documentParticipant['USERID']]['uphotoName']) ?>
                        </div>
                        <div class=" col-md-9 base-username">
                            <label>
                                <?= htmlspecialchars($document_base_roles[$documentParticipant['USERID']]['Adi'] . ' ' . $document_base_roles[$documentParticipant['USERID']]['Soyadi']) ?>
                                /
                                <?= htmlspecialchars($document_base_roles[$documentParticipant['USERID']]['struktur_bolmesi']) ?>
                                /
                                <?= htmlspecialchars($document_base_roles[$documentParticipant['USERID']]['vezife']) ?>

                            </label>
                        </div>
                        <?php if($documentParticipant['status'] == 1): ?> <i class="fa fa-check" style="color : lightseagreen;" aria-hidden="true"></i> <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (array_key_exists('melumatlandirma', $documentParticipants) && (count($documentParticipants['melumatlandirma']) > 0) === true): ?>
            <div class="col-md-12">
                <div class=" col-md-12 user-info-container"><h1 class="head-username"><?= dsAlt('2616rollar_melumatlandirma', "Məlumatlandırma") ?>:</h1></div>
                <?php foreach ($documentParticipants['melumatlandirma'] as $documentParticipant) : ?>
                    <div class="col-md-12 user-header-container">
                        <div style="position: relative; outline: 0px; height: 28px; width: 28px;" class="col-md-3">
                            <?= viewPhoto($document_base_roles[$documentParticipant['USERID']]['uphoto'], $document_base_roles[$documentParticipant['USERID']]['uphotoName']) ?>
                        </div>
                        <div class=" col-md-9 base-username">
                            <label>
                                <?= htmlspecialchars($document_base_roles[$documentParticipant['USERID']]['Adi'] . ' ' . $document_base_roles[$documentParticipant['USERID']]['Soyadi']) ?>
                                /
                                <?= htmlspecialchars($document_base_roles[$documentParticipant['USERID']]['struktur_bolmesi']) ?>
                                /
                                <?= htmlspecialchars($document_base_roles[$documentParticipant['USERID']]['vezife']) ?>

                            </label>
                        </div>
                        <?php if($documentParticipant['status'] == 1): ?> <i class="fa fa-check" style="color : lightseagreen;" aria-hidden="true"></i> <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (array_key_exists('qiymetlendirme', $documentParticipants) && (count($documentParticipants['qiymetlendirme']) > 0) === true): ?>
            <div class="col-md-12">
                <div class=" col-md-12 user-info-container"><h1 class="head-username"><?= dsAlt('2616rollar_qiymetlendirme', "Qiymətləndirmə"); ?>:</h1></div>
                <?php foreach ($documentParticipants['qiymetlendirme'] as $documentParticipant) : ?>
                    <div class="col-md-12 user-header-container">
                        <div style="position: relative; outline: 0px; height: 28px; width: 28px;" class="col-md-3">
                            <?= viewPhoto($document_base_roles[$documentParticipant['USERID']]['uphoto'], $document_base_roles[$documentParticipant['USERID']]['uphotoName']) ?>
                        </div>
                        <div class=" col-md-9 base-username">
                            <label>
                                <?= htmlspecialchars($document_base_roles[$documentParticipant['USERID']]['Adi'] . ' ' . $document_base_roles[$documentParticipant['USERID']]['Soyadi']) ?>
                                /
                                <?= htmlspecialchars($document_base_roles[$documentParticipant['USERID']]['struktur_bolmesi']) ?>
                                /
                                <?= htmlspecialchars($document_base_roles[$documentParticipant['USERID']]['vezife']) ?>

                            </label>
                        </div>
                        <?php if($documentParticipant['status'] == 1): ?> <i class="fa fa-check" style="color : lightseagreen;" aria-hidden="true"></i> <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (array_key_exists('neticeni_qeyd_eden_sexs', $documentParticipants) && (count($documentParticipants['neticeni_qeyd_eden_sexs']) > 0) === true): ?>
            <div class="col-md-12">
                <div class=" col-md-12 user-info-container"><h1 class="head-username"><?= dsAlt('2616rollar_netice_qeyd_eden', "Nəticəni qeyd edən şəxs"); ?>:</h1>
                </div>
                <?php foreach ($documentParticipants['neticeni_qeyd_eden_sexs'] as $documentParticipant) : ?>
                    <div class="col-md-12 user-header-container">
                        <div style="position: relative; outline: 0px; height: 28px; width: 28px;" class="col-md-3">
                            <?= viewPhoto($document_base_roles[$documentParticipant['USERID']]['uphoto'], $document_base_roles[$documentParticipant['USERID']]['uphotoName']) ?>
                        </div>
                        <div class=" col-md-9 base-username">
                            <label>
                                <?= htmlspecialchars($document_base_roles[$documentParticipant['USERID']]['Adi'] . ' ' . $document_base_roles[$documentParticipant['USERID']]['Soyadi']) ?>
                                /
                                <?= htmlspecialchars($document_base_roles[$documentParticipant['USERID']]['struktur_bolmesi']) ?>
                                /
                                <?= htmlspecialchars($document_base_roles[$documentParticipant['USERID']]['vezife']) ?>

                            </label>
                        </div>
                        <?php if($documentParticipant['status'] == 1): ?> <i class="fa fa-check" style="color : lightseagreen;" aria-hidden="true"></i> <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (array_key_exists('yeni_icra_eden_sexs', $documentParticipants) && (count($documentParticipants['yeni_icra_eden_sexs']) > 0) === true): ?>
            <div class="col-md-12">
                <div class=" col-md-12 user-info-container"><h1 class="head-username"><?= dsAlt('2616rollar_yeni_icrachi', "Yeni icraçı şəxs"); ?>:</h1></div>
                <?php foreach ($documentParticipants['yeni_icra_eden_sexs'] as $documentParticipant) : ?>
                    <div class="col-md-12 user-header-container">
                        <div style="position: relative; outline: 0px; height: 28px; width: 28px;" class="col-md-3">
                            <?= viewPhoto($document_base_roles[$documentParticipant['USERID']]['uphoto'], $document_base_roles[$documentParticipant['USERID']]['uphotoName']) ?>
                        </div>
                        <div class=" col-md-9 base-username">
                            <label>
                                <?= htmlspecialchars($document_base_roles[$documentParticipant['USERID']]['Adi'] . ' ' . $document_base_roles[$documentParticipant['USERID']]['Soyadi']) ?>
                                /
                                <?= htmlspecialchars($document_base_roles[$documentParticipant['USERID']]['struktur_bolmesi']) ?>
                                /
                                <?= htmlspecialchars($document_base_roles[$documentParticipant['USERID']]['vezife']) ?>

                            </label>
                        </div>
                        <?php if($documentParticipant['status'] == 1): ?> <i class="fa fa-check" style="color : lightseagreen;" aria-hidden="true"></i> <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (array_key_exists('yeni_hemIcrachi', $documentParticipants) && (count($documentParticipants['yeni_hemIcrachi']) > 0) === true): ?>
            <div class="col-md-12">
                <div class=" col-md-12 user-info-container"><h1 class="head-username"><?= dsAlt('2616rollar_yeni_hemicraci', "Yeni həm icraçı şəxs"); ?>:</h1></div>
                <?php foreach ($documentParticipants['yeni_hemIcrachi'] as $documentParticipant) : ?>
                    <div class="col-md-12 user-header-container">
                        <div style="position: relative; outline: 0px; height: 28px; width: 28px;" class="col-md-3">
                            <?= viewPhoto($document_base_roles[$documentParticipant['USERID']]['uphoto'], $document_base_roles[$documentParticipant['USERID']]['uphotoName']) ?>
                        </div>
                        <div class=" col-md-9 base-username">
                            <label>
                                <?= htmlspecialchars($document_base_roles[$documentParticipant['USERID']]['Adi'] . ' ' . $document_base_roles[$documentParticipant['USERID']]['Soyadi']) ?>
                                /
                                <?= htmlspecialchars($document_base_roles[$documentParticipant['USERID']]['struktur_bolmesi']) ?>
                                /
                                <?= htmlspecialchars($document_base_roles[$documentParticipant['USERID']]['vezife']) ?>

                            </label>
                        </div>
                        <?php if($documentParticipant['status'] == 1): ?> <i class="fa fa-check" style="color : lightseagreen;" aria-hidden="true"></i> <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (array_key_exists('mesul_shexs', $documentParticipants) && (count($documentParticipants['mesul_shexs']) > 0) === true): ?>
            <div class="col-md-12">
                <div class=" col-md-12 user-info-container"><h1 class="head-username"><?= dsAlt('2616rollar_icrachi', "İcraçı"); ?>:</h1></div>
                <?php foreach ($documentParticipants['mesul_shexs'] as $documentParticipant) : ?>
                    <div class="col-md-12 user-header-container">
                        <div style="position: relative; outline: 0px; height: 28px; width: 28px;" class="col-md-3">
                            <?= viewPhoto($document_base_roles[$documentParticipant['USERID']]['uphoto'], $document_base_roles[$documentParticipant['USERID']]['uphotoName']) ?>

                        </div>
                        <div class=" col-md-9 base-username">
                            <label>
                                <?= htmlspecialchars($document_base_roles[$documentParticipant['USERID']]['Adi'] . ' ' . $document_base_roles[$documentParticipant['USERID']]['Soyadi']) ?>
                                /
                                <?= htmlspecialchars($document_base_roles[$documentParticipant['USERID']]['struktur_bolmesi']) ?>
                                /
                                <?= htmlspecialchars($document_base_roles[$documentParticipant['USERID']]['vezife']) ?>

                            </label>
                        </div>
                        <?php if($documentParticipant['status'] == 1): ?> <i class="fa fa-check" style="color : lightseagreen;" aria-hidden="true"></i> <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (array_key_exists('melumat', $documentParticipants) && (count($documentParticipants['melumat']) > 0) === true): ?>
            <div class="col-md-12">
                <div class=" col-md-12 user-info-container"><h1 class="head-username"><?= dsAlt('2616rollar_melumatlandirilan', "Məlumatlandırılan şəxs"); ?>:</h1>
                </div>
                <?php foreach ($documentParticipants['melumat'] as $documentParticipant) : ?>
                    <div class="col-md-12 user-header-container">
                        <div style="position: relative; outline: 0px; height: 28px; width: 28px;" class="col-md-3">
                            <?= viewPhoto($document_base_roles[$documentParticipant['USERID']]['uphoto'], $document_base_roles[$documentParticipant['USERID']]['uphotoName']) ?>

                        </div>
                        <div class=" col-md-9 base-username">
                            <label>
                                <?= htmlspecialchars($document_base_roles[$documentParticipant['USERID']]['Adi'] . ' ' . $document_base_roles[$documentParticipant['USERID']]['Soyadi']) ?>
                                /
                                <?= htmlspecialchars($document_base_roles[$documentParticipant['USERID']]['struktur_bolmesi']) ?>
                                /
                                <?= htmlspecialchars($document_base_roles[$documentParticipant['USERID']]['vezife']) ?>

                            </label>
                        </div>
                        <?php if($documentParticipant['status'] == 1): ?> <i class="fa fa-check" style="color : lightseagreen;" aria-hidden="true"></i> <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (array_key_exists('sub_task_executor', $documentParticipants) && (count($documentParticipants['sub_task_executor']) > 0) === true): ?>
            <div class="col-md-12">
                <div class=" col-md-12 user-info-container"><h1 class="head-username"> <?= dsAlt('2616rollar_alt_derkenar_icra', "Alt dərkənar icraçısı"); ?>:</h1></div>
                <?php foreach ($documentParticipants['sub_task_executor'] as $documentParticipant) : ?>
                    <div class="col-md-12 user-header-container">
                        <div style="position: relative; outline: 0px; height: 28px; width: 28px;" class="col-md-3">
                            <?= viewPhoto($document_base_roles[$documentParticipant['USERID']]['uphoto'], $document_base_roles[$documentParticipant['USERID']]['uphotoName']) ?>

                        </div>
                        <div class=" col-md-9 base-username">
                            <label>
                                <?= htmlspecialchars($document_base_roles[$documentParticipant['USERID']]['Adi'] . ' ' . $document_base_roles[$documentParticipant['USERID']]['Soyadi']) ?>
                                /
                                <?= htmlspecialchars($document_base_roles[$documentParticipant['USERID']]['struktur_bolmesi']) ?>
                                /
                                <?= htmlspecialchars($document_base_roles[$documentParticipant['USERID']]['vezife']) ?>

                            </label>
                        </div>
                        <?php if($documentParticipant['status'] == 1): ?> <i class="fa fa-check" style="color : lightseagreen;" aria-hidden="true"></i> <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if (array_key_exists('ishtrakchi', $documentParticipants) && (count($documentParticipants['ishtrakchi']) > 0) === true): ?>
            <div class="col-md-12">
                <div class=" col-md-12 user-info-container"><h1 class="head-username"><?= dsAlt('2616rollar_hemicraci', "Həmicraçı"); ?>:</h1></div>
                <?php foreach ($documentParticipants['ishtrakchi'] as $documentParticipant) : ?>
                    <div class="col-md-12 user-header-container">
                        <div style="position: relative; outline: 0px; height: 28px; width: 28px;" class="col-md-3">
                            <?= viewPhoto($document_base_roles[$documentParticipant['USERID']]['uphoto'], $document_base_roles[$documentParticipant['USERID']]['uphotoName']) ?>

                        </div>
                        <div class=" col-md-9 base-username">
                            <label>
                                <?= htmlspecialchars($document_base_roles[$documentParticipant['USERID']]['Adi'] . ' ' . $document_base_roles[$documentParticipant['USERID']]['Soyadi']) ?>
                                /
                                <?= htmlspecialchars($document_base_roles[$documentParticipant['USERID']]['struktur_bolmesi']) ?>
                                /
                                <?= htmlspecialchars($document_base_roles[$documentParticipant['USERID']]['vezife']) ?>

                            </label>
                        </div>
                        <?php if($documentParticipant['status'] == 1): ?> <i class="fa fa-check" style="color : lightseagreen;" aria-hidden="true"></i> <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if (array_key_exists('ishtirakchi', $documentParticipants) && (count($documentParticipants['ishtirakchi']) > 0) === true): ?>
            <div class="col-md-12">
                <div class=" col-md-12 user-info-container"><h1 class="head-username"><?= dsAlt('2616rollar_hemicraci', "Həmicraçı"); ?>:</h1></div>
                <?php foreach ($documentParticipants['ishtirakchi'] as $documentParticipant) : ?>
                    <div class="col-md-12 user-header-container">
                        <div style="position: relative; outline: 0px; height: 28px; width: 28px;" class="col-md-3">
                            <?= viewPhoto($document_base_roles[$documentParticipant['USERID']]['uphoto'], $document_base_roles[$documentParticipant['USERID']]['uphotoName']) ?>

                        </div>
                        <div class=" col-md-9 base-username">
                            <label>
                                <?= htmlspecialchars($document_base_roles[$documentParticipant['USERID']]['Adi'] . ' ' . $document_base_roles[$documentParticipant['USERID']]['Soyadi']) ?>
                                /
                                <?= htmlspecialchars($document_base_roles[$documentParticipant['USERID']]['struktur_bolmesi']) ?>
                                /
                                <?= htmlspecialchars($document_base_roles[$documentParticipant['USERID']]['vezife']) ?>

                            </label>
                        </div>
                        <?php if($documentParticipant['status'] == 1): ?> <i class="fa fa-check" style="color : lightseagreen;" aria-hidden="true"></i> <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if (array_key_exists('kurator', $documentParticipants) && (count($documentParticipants['kurator']) > 0) === true): ?>
            <div class="col-md-12">
                <div class=" col-md-12 user-info-container"><h1 class="head-username"><?= dsAlt('2616rollar_icraya_nezaret_eden', "İcraya nəzarət edən şəxs"); ?>:</h1>
                </div>
                <?php foreach ($documentParticipants['kurator'] as $documentParticipant) : ?>
                    <div class="col-md-12 user-header-container">
                        <div style="position: relative; outline: 0px; height: 28px; width: 28px;" class="col-md-3">
                            <?= viewPhoto($document_base_roles[$documentParticipant['USERID']]['uphoto'], $document_base_roles[$documentParticipant['USERID']]['uphotoName']) ?>

                        </div>
                        <div class=" col-md-9 base-username">
                            <label>
                                <?= htmlspecialchars($document_base_roles[$documentParticipant['USERID']]['Adi'] . ' ' . $document_base_roles[$documentParticipant['USERID']]['Soyadi']) ?>
                                /
                                <?= htmlspecialchars($document_base_roles[$documentParticipant['USERID']]['struktur_bolmesi']) ?>
                                /
                                <?= htmlspecialchars($document_base_roles[$documentParticipant['USERID']]['vezife']) ?>

                            </label>
                        </div>
                        <?php if($documentParticipant['status'] == 1): ?> <i class="fa fa-check" style="color : lightseagreen;" aria-hidden="true"></i> <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (array_key_exists('viza', $documentParticipants) && (count($documentParticipants['viza']) > 0) === true): ?>
            <div class="col-md-12">
                <div class=" col-md-12 user-info-container"><h1 class="head-username">Viza/Razılaşdıran:</h1></div>
                <?php foreach ($documentParticipants['viza'] as $documentParticipant) : ?>
                    <div class="col-md-12 user-header-container">
                        <div style="position: relative; outline: 0px; height: 28px; width: 28px;" class="col-md-3">
                            <?= viewPhoto($document_base_roles[$documentParticipant['USERID']]['uphoto'], $document_base_roles[$documentParticipant['USERID']]['uphotoName']) ?>
                        </div>
                        <div class=" col-md-9 base-username">
                            <label>
                                <?= htmlspecialchars($document_base_roles[$documentParticipant['USERID']]['Adi'] . ' ' . $document_base_roles[$documentParticipant['USERID']]['Soyadi']) ?>
                                /
                                <?= htmlspecialchars($document_base_roles[$documentParticipant['USERID']]['struktur_bolmesi']) ?>
                                /
                                <?= htmlspecialchars($document_base_roles[$documentParticipant['USERID']]['vezife']) ?>

                            </label>
                        </div>
                        <?php if($documentParticipant['status'] == 1): ?> <i class="fa fa-check" style="color : lightseagreen;" aria-hidden="true"></i> <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    <?php if (array_key_exists('yoxlayan_shexs', $documentInfo) && !is_null($documentInfo['yoxlayan_shexs']) && $documentInfo['yoxlayan_shexs'] != 0): ?>
        <div class="col-md-12">
            <div class=" col-md-12 user-info-container"><h1 class="head-username"><?= dsAlt('2616rollar_yoxlayan', "Yoxlayan şəxs"); ?>:</h1></div>
            <div class="col-md-12 user-header-container">
                <div style="position: relative; outline: 0px; height: 28px; width: 28px;" class="col-md-3">
                    <?= viewPhoto($document_base_roles[$documentInfo['yoxlayan_shexs']]['uphoto'], $document_base_roles[$documentInfo['yoxlayan_shexs']]['uphotoName']) ?>

                </div>
                <div class=" col-md-9 base-username">
                    <label>
                        <?= htmlspecialchars($document_base_roles[$documentInfo['yoxlayan_shexs']]['Adi'] . ' ' . $document_base_roles[$documentInfo['yoxlayan_shexs']]['Soyadi']) ?>
                        /
                        <?= htmlspecialchars($document_base_roles[$documentInfo['yoxlayan_shexs']]['struktur_bolmesi']) ?>
                        /
                        <?= htmlspecialchars($document_base_roles[$documentInfo['yoxlayan_shexs']]['vezife']) ?>

                    </label>
                </div>
                <?php if($documentInfo['state'] != 1): ?> <i class="fa fa-check" style="color : lightseagreen;" aria-hidden="true"></i> <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
    <?php if (count($from_poa_users)>0): ?>
        <div class="col-md-12">
            <div class=" col-md-12 user-info-container"><h1 class="head-username">Vəkalətnamə üzrə:</h1></div>
            <?php
            foreach ($from_poa_users as $toUser => $fromUserArray) {
                ?>
                <div class="col-md-12 user-header-container">
                <div style="top: 5px;position: relative; outline: 0px; height: 28px; width: 28px;" class="col-md-3">
                    <?= viewPhoto($document_base_roles[$toUser]['uphoto'], $document_base_roles[$toUser]['uphotoName']) ?>
                </div>
                <div class="col-md-19 base-username">
                <label>
                    <?= htmlspecialchars($document_base_roles[$toUser]['Adi'] . ' ' . $document_base_roles[$toUser]['Soyadi']) ?>
                    /
                    <?= htmlspecialchars($document_base_roles[$toUser]['struktur_bolmesi']) ?>
                    /
                    <?= htmlspecialchars($document_base_roles[$toUser]['vezife']) ?>
                </label>
                <?php
                foreach ($fromUserArray as $fromUser) {
                    ?>
                    (<?= htmlspecialchars($document_base_roles[$fromUser]['Adi'] . ' ' . $document_base_roles[$fromUser]['Soyadi']) ?>
                    /
                    <?= htmlspecialchars($document_base_roles[$fromUser]['struktur_bolmesi']) ?>
                    /
                    <?= htmlspecialchars($document_base_roles[$fromUser]['vezife']) ?>)
                    </div>
                    </div>
                    <?php

                }
            }
            ?>
        </div>
    <?php endif; ?>
    <div class="col-md-12" style="padding-bottom: 15px;<?= $connectedDocumentStyle ?>">
        <div class="col-md-12 user-info-container elaqeli-senedler"><h1 class="head-username">ƏLAQƏLİ SƏNƏDİN RAZILAŞDIRANLARI:</h1></div>
        <?php
        foreach ($dosConnectedDos as $dosConnectedDosDocumentId) {
            $dosDocument = new Document($dosConnectedDosDocumentId['related_document_id']);
            $documentInfo = $dosDocument->getData();

            if (!is_null($documentInfo)) {
                ?>
                <div derkenar_id = "<?= $derkenarId['derkenar_id'] ?>" id="<?= $documentInfo['id'] ?>" class="header-document-container col-md-12">
                    <div class="form-froup">
                        <div class="document_numbers col-md-10"><?= $documentInfo['document_number'] ?></div>
                        <div class="chevron-contanier col-md-2"><i class="fa fa-chevron-up" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="center-document-container" style="display: none;">
                        <div class="connected-document-mini-roles-tab"></div>
                    </div>
                </div>
                <?php
            }
        }
        ?>
    </div>
</div>

<?php
$sened = [
    'sened_novu'=>'dos',
    'status'=>$document->getStatus()
];

$statusColor = getDocumentColorRelatedStatus($sened);

?>
<script>
    $( document ).ready(function() {
        $('.header-document-container').on('click', '.fa-chevron-up', function () {
            var parent = $(this).parents('.header-document-container');

            if (parent.find('.center-document-container').hasClass('active')) {

                parent.find('.center-document-container').hide();
                parent.find('.center-document-container').removeClass('active');
                $(this).css('transform', '');

            } else {
                parent.find('.center-document-container').addClass('active');
                parent.find('.center-document-container').show();
                $(this).css('transform', 'rotate(180deg)');
                var id = parent.attr('id');
                var derkenar_id = parent.attr('derkenar_id');

                if ($('#'+id).find('.connected-document-mini-roles-tab').is(':empty')) {
                    $.post("prodoc/ajax/connectedDocumentAcceptedUsers.php", {'id': id,'derkenarId' : derkenar_id}, function (netice) {
                        $('#'+id).find('.connected-document-mini-roles-tab').html(netice)
                    });
                }
            }
        })
    });

    $('.background-color-container').css('background',"<?php print $statusColor ?>")

</script>
