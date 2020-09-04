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
    <small>Aktın növü:</small><br>
    <span><?php print $poa['act_type'] ?></span>
</p>
<?php if(!$checkPetition): ?>
    <p>
        <small>Aktın tarixi:</small><br>
        <span><?php print $poa['created_at'] ?></span>
    </p>
    <p>
        <small>Şəhər:</small><br>
        <span><?php print htmlspecialchars($poa['sheher']); ?></span>
    </p>
    <p>
        <small>Kənd:</small><br>
        <span><?php print htmlspecialchars($poa['kend']); ?></span>
    </p>

    <p>
        <small>Tapşırıq əmrinin №-si:</small><br>
        <span><?php print htmlspecialchars($tapshiriq_number); ?></span>
    </p>
    <p>
        <small>Növ:</small><br>
        <span><?php print htmlspecialchars($poa['nov']); ?></span>
    </p>

    <p>
        <small>Miqdar:</small><br>
        <span><?php print htmlspecialchars($poa['miqdar']); ?></span>
    </p>

    <p>
        <small>Yerüstü ərazi:</small><br>
        <span><?php print htmlspecialchars($poa['yerustu_erazi']); ?></span>
    </p>

    <p>
        <small>Yeraltı ərazi:</small><br>
        <span><?php print htmlspecialchars($poa['yeralti_erazi']); ?></span>
    </p>

    <p>
        <small>Nəqliyyat nömrəsi:</small><br>
        <span><?php print htmlspecialchars($poa['neqliyyat_nomresi']); ?></span>
    </p>

    <p>
        <small>Məhv etmə ərazisi:</small><br>
        <span><?php print htmlspecialchars($poa['mehv_etme']); ?></span>
    </p>



    <p>
        <small>Təhvil verən şəxs:</small><br>
        <span><?php print htmlspecialchars($poa['tehvil_veren_ad']); ?></span>
    </p>

    <p>
        <small>Təhvil alan orqan:</small><br>
        <span><?php print htmlspecialchars($poa['tehvil_alan_orqan']); ?></span>
    </p>

    <p>
        <small>Təhvil alan şəxs:</small><br>
        <span><?php print htmlspecialchars($poa['tehvil_alan_shexs']); ?></span>
    </p>

    <p>
        <small>İmzalayan şəxslər:</small><br>
        <?php foreach ($imzalayan_shexsler as $imzalayan): ?>
        <strong><?php print htmlspecialchars($imzalayan['user_name']); ?></strong>
        <br>
        <?php endforeach; ?>
    </p>
<?php endif; ?>
<p>
    <small>Qeyd:</small><br>
    <span><?php print htmlspecialchars($poa['qeyd']); ?></span>
</p>
<p>
    <small>Akt tipləri:</small><br>
    <span style="word-wrap: break-word;" ><i class="fa fa-cogs"></i><a href='javascript: templateYukle("act_types","Akt tipləri",{"act_id": <?= $id ?>},80,true,"green");'> Akt tipləri</a></span>
</p>
<?php print $intDoc->getRelatedInternalDocumentsHTMLTree(); ?>
