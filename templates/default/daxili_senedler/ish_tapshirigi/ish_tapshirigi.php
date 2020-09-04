<?php
//$TenantId = $user->getActiveTenantId();
//$sened_novu_id = DB::fetchColumn("SELECT TOP 1 id FROM tb_sened_novleri WHERE forma_ad='hesabat_yarat' AND TenantId=0;");
//$file_original_name = DB::fetchColumn(
//    "SELECT file_original_name FROM tb_prodoc_sened_novleri_periods WHERE file_name!='' AND sened_novu_id='$sened_novu_id' AND TenantId='$TenantId' AND deleted=0
//");
//
//$path = 'hesabat_yarat';
//include DIRNAME_INDEX . "prodoc/includes/IxracFormasi.php";
//?>

<?php print $intDoc->getDetailedInformationHTML(['hidden_fields' => ['testiq', 'melumat']]) ?>

<style>
    .modal-content  {
        -webkit-border-radius: 15px !important;
        -moz-border-radius: 15px !important;
        border-radius: 15px !important;

    }
    .modal-header{
        -webkit-border-radius: 13px 13px 0 0 !important;
        -moz-border-radius: 13px 13px 0 0 !important;
        border-radius: 13px 13px 0 0 !important;

    }
    .modal-header, .modal-header:hover{
        background: #1C8F5F !important;
        border-color: #1C8F5F !important;
    }
</style>

<p>
    <small>İş tapşırığının №-si:</small><br>
    <span><?php print $doc_numb ?></span>
</p>
<p>
    <small>Görüşəcək Şəxs:</small><br>
    <span><?php print htmlspecialchars($poa['kim']); ?></span>
</p>

<p>
    <small>Əlaqəli şəxs:</small><br>
    <span><?php print htmlspecialchars($poa['elaqeli_shexs']); ?></span>
</p>
<p>
    <small>Şirkət:</small><br>
    <span><?php print htmlspecialchars($poa['company']); ?></span>
</p>
<p>
    <small>Təyinat:</small><br>
    <span><?php print htmlspecialchars($poa['teyinat']); ?></span>
</p>
<p>
    <small>Təyinat tipi:</small><br>
    <span><?php print htmlspecialchars($poa['teyinat_tipi']); ?></span>
</p>

<p>
    <small>Məlumat:</small><br>
    <span><?php print htmlspecialchars($poa['melumat']); ?></span>
</p>


<?php print $intDoc->getRelatedInternalDocumentsHTMLTree(); ?>
