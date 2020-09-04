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
    <small>Hesabatın qeydiyyat №-si:</small><br>
    <span><?php print $doc_numb ?></span>
</p>
<p>
    <small>Hesabatın ID:</small><br>
    <span><?php print htmlspecialchars($poa['hesabat_id']); ?></span>
</p>

<p>
    <small>Tapşırıq əmrinin №-si:</small><br>
    <span><?php print htmlspecialchars($tapshiriq_number); ?></span>
</p>
<p>
	<small>Təsdiq olundu:</small><br>
	<span><?php print htmlspecialchars($RM['tesdiq_olundu']); ?></span>
</p>

<p>
	<small>Təsdiq olunma tarixi:</small><br>
	<span><?php print htmlspecialchars($RM['tesdiq_tarixi']); ?></span>
</p>

<p>
    <small>Hesabatı verən:</small><br>
    <span style="word-wrap: break-word;"><?php print htmlspecialchars($created_by_info['full_name']); ?></span>
</p>
<p>
    <small>Təşkilat :</small><br>
    <span style="word-wrap: break-word;" ><?php print htmlspecialchars($created_by_info['struktur_bolmesi']); ?></span>
</p>

<p>
    <small>Vəzifə :</small><br>
    <span style="word-wrap: break-word;" ><?php print htmlspecialchars($created_by_info['vezife']); ?></span>
</p>
<p>
    <small>Hesabatın tarixi:</small><br>
    <span style="word-wrap: break-word;" ><?php print htmlspecialchars($poa['hesabatin_tarixi']); ?></span>
</p>
<p>
    <small>Şəhər/Kənd:</small><br>
    <span style="word-wrap: break-word;" ><?php print htmlspecialchars($poa['sheher_ad']); ?></span>
</p>
<p>
    <small>Rayon:</small><br>
    <span style="word-wrap: break-word;" ><?php print htmlspecialchars($poa['rayon_ad']); ?></span>
</p>
<p>
    <small>Nişangah/Başlanğıc nöqtənin koordinatları:</small><br>
    <span style="word-wrap: break-word;" ><?php print htmlspecialchars($poa['bashlangic_noqtenin_koordinatlari']); ?></span>
</p>
<p>
    <small>Coğrafi uzunluq/Şərq:</small><br>
    <span style="word-wrap: break-word;" ><?php print htmlspecialchars($poa['sherq']); ?></span>
</p>
<p>
    <small>Coğrafi enlik/Şimal:</small><br>
    <span style="word-wrap: break-word;" ><?php print htmlspecialchars($poa['shimal']); ?></span>
</p>
<p>
    <small>Koordinat götürülüb:</small><br>
    <span style="word-wrap: break-word;" >DGPS
        <?php if( htmlspecialchars($poa['dkps'])==1) : ?>
        <i class="fa fa-check"></i>
        <?php elseif(htmlspecialchars($poa['dkps'])==0) : ?>
        <i class="fa fa-close"></i>
        <?php else : ?>

        <?php endif; ?>
    </span>
    <br>
    <span style="word-wrap: break-word;" >GPS
        <?php if( htmlspecialchars($poa['kps'])==1) : ?>
            <i class="fa fa-check"></i>
        <?php elseif(htmlspecialchars($poa['kps'])==0) : ?>
            <i class="fa fa-close"></i>
        <?php else : ?>

        <?php endif; ?>
    </span>
</p>
<p>
    <small>Nişangah/Başlanğıc nöqtənin və/və ya PHS təsviri:</small><br>
    <span style="word-wrap: break-word;" ><?php print htmlspecialchars($poa['phs_tesviri']); ?></span>
</p>
<p>
    <small>Vəziyyəti:</small><br>
    <span style="word-wrap: break-word;" >Zərərsizləşdirilmiş
        <?php if( htmlspecialchars($poa['veziyyet_zererleshdirilmish'])==1) : ?>
            <i class="fa fa-check"></i>
        <?php elseif(htmlspecialchars($poa['veziyyet_zererleshdirilmish'])==0) : ?>
            <i class="fa fa-close"></i>
        <?php else : ?>

        <?php endif; ?>
    </span>
    <br>
    <span style="word-wrap: break-word;" >İşarənləmiş
        <?php if( htmlspecialchars($poa['veziyyet_isharelenmish'])==1) : ?>
            <i class="fa fa-check"></i>
        <?php elseif(htmlspecialchars($poa['veziyyet_isharelenmish'])==0) : ?>
            <i class="fa fa-close"></i>
        <?php else : ?>

        <?php endif; ?>
    </span>
    <br>
    <span style="word-wrap: break-word;" >Aparılmış
        <?php if( htmlspecialchars($poa['veziyyet_aparilmish'])==1) : ?>
            <i class="fa fa-check"></i>
        <?php elseif(htmlspecialchars($poa['veziyyet_aparilmish'])==0) : ?>
            <i class="fa fa-close"></i>
        <?php else : ?>

        <?php endif; ?>
    </span>
</p>
<p>
    <small>Aparılmış qurğunun xüsusiyyətləri:</small><br>
    <span style="word-wrap: break-word;" ><?php print htmlspecialchars($poa['aparilmish_qurgunun_xususiyyetleri']); ?></span>
</p>
<p>
    <small>Çirklənmiş ərazinin təsviri (çirklənmənin səbəbi və s.):</small><br>
    <span style="word-wrap: break-word;" ><?php print htmlspecialchars($poa['chirklenmish_erazinin_tesviri']); ?></span>
</p>
<p>
    <small>Qurğular:</small><br>
    <span style="word-wrap: break-word;" ><i class="fa fa-cogs"></i><a href='javascript: templateYukle("devices","Qurğular",{"report_id": <?= $id ?>},80,true,"green");'> Qurğu növləri</a></span>
</p>
<?php print $intDoc->getRelatedInternalDocumentsHTMLTree(); ?>
