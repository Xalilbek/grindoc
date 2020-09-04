<?php
$TenantId = $user->getActiveTenantId();
$sened_novu_id = DB::fetchColumn("SELECT TOP 1 id FROM tb_sened_novleri WHERE forma_ad='task_command' AND TenantId=0;");
$file_original_name = DB::fetchColumn(
    "SELECT file_original_name FROM tb_prodoc_sened_novleri_periods WHERE file_name!='' AND sened_novu_id='$sened_novu_id' AND TenantId='$TenantId' AND deleted=0 
");

$path = 'task_command';
include DIRNAME_INDEX . "prodoc/includes/IxracFormasi.php";
?>

<?php //print $intDoc->getDetailedInformationHTML() ?>


<p>
    <small>Status:</small><br>
    <span><?php print htmlspecialchars($status); ?></span>
</p>

<p>
    <small>Sənəd nömrəsi:</small><br>
    <span><?php print htmlspecialchars($poa['document_number']); ?></span>
</p>
<p>
    <small>Sənəd tarixi:</small><br>
    <span><?php print htmlspecialchars($poa['document_date']); ?></span>
</p>

<p>
    <small>Kimə:</small><br>
    <?php $prevMesulShexs = null; $num = 0; ?>
    <?php foreach ($kime as $task): $num++; ?>
        <?php if ($task['name'] != $prevMesulShexs || is_null($prevMesulShexs)): ?>
            <?=  $proxy->getProxyNameByPrincipal($task['kime'], $task['name']); ?>
            <br>
        <?php endif; ?>
        <span style="color:red; word-wrap: break-word;">  <?php print $task['derkenar_metn_ad']; ?></span><br> <?php (is_null($task['son_icra_tarixi'])||$task['son_icra_tarixi']=='')? print "Müraciət olduqda" : print $task['son_icra_tarixi']; ?>
        <br>
        <?php $prevMesulShexs = $task['name']; ?>
    <?php endforeach; ?>

</p>
<p>
    <small>Məlumat:</small><br>
    <?php foreach ($melumat as $kim): ?>
    <?php $status=$kim['status']==1?"<i class=\"fa fa-check\" aria-hidden=\"true\"></i>":" "; ?>
    <span><?= $proxy->getProxyNameByPrincipal($kim['user_id'], $kim['name']).$status; ?></span><br>
    <?php endforeach; ?>
    <span><?php print $melumat_shexsler ?></span>
</p>
<p>
    <small>Kimdən:</small><br>
    <span><?= $proxy->getProxyNameByPrincipal($poa['rey_muellifi'], $poa['rey_muellifi_ad']); ?></span>
</p>
<p>
    <small>Mövzu:</small><br>
    <span style="word-wrap: break-word;"><?php print htmlspecialchars($poa['movzu']); ?></span>
</p>
<p>
    <small>Giriş:</small><br>
    <span style="word-wrap: break-word;" ><?php print htmlspecialchars($poa['girish']); ?></span>
</p>

<p>
    <small>Məqsəd:</small><br>
    <span style="word-wrap: break-word;" ><?php print htmlspecialchars($poa['meqsed']); ?></span>
</p>
<p>
    <small>Xüsusi qeydlər:</small><br>
    <span style="word-wrap: break-word;" ><?php print htmlspecialchars($poa['xususi_qeydler']); ?></span>
</p>

<?php print $intDoc->getRelatedInternalDocumentsHTMLTree(); ?>

