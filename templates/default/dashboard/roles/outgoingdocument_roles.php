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
    <div class="col-md-12" style="<?= $statusDisplay ?>">
        <div class="status_roles">Status</div>
        <div class="background-color-container" style="margin-top: 6px;
        width:153px;
        height: 30px;
        color: white;
        border-radius: 4px !important;" ><div style="font-size: 20px;margin-left: 14px;"><?= $statusTitle ?></div></div>
    </div>
    <div class="col-md-12">
        <div class=" col-md-12 user-info-container"><h1  class="head-username"><?= dsAlt('2616rollar_qeydiyyatci', "Qeydiyyatçı"); ?>:</h1></div>
        <div class="col-md-12 user-header-container">
            <div style="position: relative; outline: 0px; height: 28px; width: 28px;" class="col-md-3">
                <?=  viewPhoto($document_base_roles[$role_users['created_by'][0]['user_id']]['uphoto'],$document_base_roles[$role_users['created_by'][0]['user_id']]['uphotoName'])?>
            </div>
            <div class=" col-md-9 base-username">
                <label>
                    <?= htmlspecialchars($document_base_roles[$role_users['created_by'][0]['user_id']]['Adi'].' '.$document_base_roles[$role_users['created_by'][0]['user_id']]['Soyadi']) ?> /
                    <?= htmlspecialchars($document_base_roles[$role_users['created_by'][0]['user_id']]['struktur_bolmesi']) ?> /
                    <?= htmlspecialchars($document_base_roles[$role_users['created_by'][0]['user_id']]['vezife']) ?>
                </label>
            </div>
        </div>
    </div>
    <?php if (array_key_exists('rey_muelifi',$role_users)&& !is_null($role_users['rey_muelifi'])): ?>
        <div class="col-md-12">
            <div class=" col-md-12 user-info-container"><h1  class="head-username"><?= dsAlt('2616rollar_sedr', "Sədr")?>:</h1></div>
            <?php foreach ($role_users['rey_muelifi'] as $shexs): ?>
                <div class="col-md-12 user-header-container">
                    <div style="position: relative; outline: 0px; height: 28px; width: 28px;" class="col-md-3">

                        <?= viewPhoto($document_base_roles[$shexs['user_id']]['uphoto'], $document_base_roles[$shexs['user_id']]['uphotoName']) ?>

                    </div>
                    <div class=" col-md-9 base-username">
                        <label>

                            <?= htmlspecialchars($document_base_roles[$shexs['user_id']]['Adi'] . ' ' . $document_base_roles[$shexs['user_id']]['Soyadi']) ?>
                            /
                            <?= htmlspecialchars($document_base_roles[$shexs['user_id']]['struktur_bolmesi']) ?> /
                            <?= htmlspecialchars($document_base_roles[$shexs['user_id']]['vezife']) ?>
                        </label>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <?php if (array_key_exists('imzalayan_shexs',$role_users)&& !is_null($role_users['imzalayan_shexs'])): ?>
        <div class="col-md-12">
            <div class=" col-md-12 user-info-container"><h1  class="head-username"><?= dsAlt('2616rollar_imzalayanlar', "İmzalayan şəxslər")?>:</h1></div>
            <?php foreach ($role_users['imzalayan_shexs'] as $shexs): ?>
                     <div class="col-md-12 user-header-container">
                         <div style="position: relative; outline: 0px; height: 28px; width: 28px;" class="col-md-3">

                             <?= viewPhoto($document_base_roles[$shexs['user_id']]['uphoto'], $document_base_roles[$shexs['user_id']]['uphotoName']) ?>

                         </div>
                         <div class=" col-md-9 base-username">
                             <label>

                                 <?= htmlspecialchars($document_base_roles[$shexs['user_id']]['Adi'] . ' ' . $document_base_roles[$shexs['user_id']]['Soyadi']) ?>
                                 /
                                 <?= htmlspecialchars($document_base_roles[$shexs['user_id']]['struktur_bolmesi']) ?> /
                                 <?= htmlspecialchars($document_base_roles[$shexs['user_id']]['vezife']) ?>
                             </label>
                         </div>
                     </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <?php if((count($role_users)>0)===true): ?>
        <?php if (isset($role_users['razilashdiran']) && $role_users['razilashdiran'][0]['user_id'] != NULL && array_key_exists('razilashdiran',$role_users)&& (count($role_users['razilashdiran'])>0)===true): ?>
            <div class="col-md-12">
                <div class=" col-md-12 user-info-container"><h1  class="head-username"><?= dsAlt('2616rollar_viza', "Viza/razılaşdıran şəxs")?>:</h1></div>
                <?php foreach ($role_users['razilashdiran'] as $documentParticipant) : ?>
                    <div class="col-md-12 user-header-container">
                        <div style="position: relative; outline: 0px; height: 28px; width: 28px;" class="col-md-3">
                            <?=  viewPhoto($document_base_roles[$documentParticipant['user_id']]['uphoto'],$document_base_roles[$documentParticipant['user_id']]['uphotoName'])?>

                        </div>
                        <div class=" col-md-9 base-username">
                            <label>
                                <?= htmlspecialchars($document_base_roles[$documentParticipant['user_id']]['Adi'].' '.$document_base_roles[$documentParticipant['user_id']]['Soyadi']) ?> /
                                <?= htmlspecialchars($document_base_roles[$documentParticipant['user_id']]['struktur_bolmesi']) ?> /
                                <?= htmlspecialchars($document_base_roles[$documentParticipant['user_id']]['vezife']) ?>

                            </label>
                        </div>
                        <?php if($documentParticipant['status'] == 1): ?> <i class="fa fa-check" style="color : lightseagreen;" aria-hidden="true"></i> <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    <?php if (count($from_poa_users)>0): ?>
        <div class="col-md-12">
            <div class=" col-md-12 user-info-container"><h1 class="head-username"><?= dsAlt('2616vekaletname_uzre_dos', 'Vəkalətnamə üzrə'); ?>:</h1></div>
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
    <?php if (array_key_exists('umumi_Shobe',$role_users)&& !is_null($role_users['umumi_Shobe'])): ?>
        <div class="col-md-12">
            <div class=" col-md-12 user-info-container"><h1  class="head-username"><?= dsAlt('2616rollar_umumi_shobe', 'Ümumİ şöbə')?>:</h1></div>
            <?php foreach ($role_users['umumi_Shobe'] as $shexs): ?>

                <div class="col-md-12 user-header-container">
                    <div style="position: relative; outline: 0px; height: 28px; width: 28px;" class="col-md-3">


                        <?=  viewPhoto($document_base_roles[$shexs['user_id']]['uphoto'],$document_base_roles[$shexs['user_id']]['uphotoName'])?>

                    </div>
                    <div class=" col-md-9 base-username">
                        <label>

                            <?= htmlspecialchars($document_base_roles[$shexs['user_id']]['Adi'].' '.$document_base_roles[$shexs['user_id']]['Soyadi']) ?> /
                            <?= htmlspecialchars($document_base_roles[$shexs['user_id']]['struktur_bolmesi'] )?> /
                            <?= htmlspecialchars($document_base_roles[$shexs['user_id']]['vezife']) ?>
                        </label>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <div class="col-md-12" style="padding-bottom: 15px;<?= $connectedDocumentStyle ?>">
        <div class="col-md-12 user-info-container elaqeli-senedler"><h1 class="head-username">ƏLAQƏLİ SƏNƏDİN RAZILAŞDIRANLARI:</h1></div>
        <?php
        foreach ($xosConnectedDos as $xosConnectedDosDocumentId) {
            $dosDocument = new Document($xosConnectedDosDocumentId);
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


$sql = "
      SELECT
          (CASE WHEN status= ".OutgoingDocument::STATUS_LEGV_OLUNUB." THEN status ELSE 
            (CASE WHEN is_sended IS NULL OR is_sended=0 THEN 0 ELSE 1 END) END ) as status
       FROM v_chixan_senedler AS document
       WHERE id = ".$outGoingDocumentInfo['id']."
";

$statusOutgoingDocument =  DB::fetch($sql);

$sened = [
    'sened_novu' => 'xos',
    'status'     => $statusOutgoingDocument['status']
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