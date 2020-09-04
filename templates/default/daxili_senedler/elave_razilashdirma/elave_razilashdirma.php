
<?php print $intDoc->getDetailedInformationHTML() ?>

<p>
    <small>Sənəd tarixi:</small><br>
    <span><?php print htmlspecialchars($poa['senedin_tarixi']); ?></span>
</p>
<p>
	<small>Əməkdaş:</small><br>
	<span><?php print htmlspecialchars($poa['user_name']); ?></span>
</p>
<p>
	<small>Vəzifə:</small><br>
	<span><?php print htmlspecialchars($poa['vezife']); ?></span>
</p>
<p>
	<small>Müqavilə:</small><br>
	<span style="word-wrap: break-word;" ><?php print htmlspecialchars($poa['muqavile']); ?></span>
</p>
<p>
	<small>Əlavə Razılaşdırma №-si:</small><br>
	<span style="word-wrap: break-word;" ><?php print htmlspecialchars($poa['elave_razilashdirnama_nomresi']); ?></span>
</p>
<p>
	<small>Əməkhaqqına əlavə:</small><br>
	<span style="word-wrap: break-word;" >
		<?php print htmlspecialchars($poa['emeqhaqqina_elave']); ?>
        <?php print htmlspecialchars($poa['emeqhaqqina_elave_valyuta_ad']); ?>
	</span>
</p>
<p>
	<small>Ezamiyyəy müddəti:</small><br>
	<span style="word-wrap: break-word;" ><?php print htmlspecialchars($poa['ezamiyyet_muddeti']); ?></span>
</p>
<p>
	<small>Şəxsiyyət vəsiqəsinin pin kodu:</small><br>
	<span style="word-wrap: break-word;" ><?php print htmlspecialchars($poa['sv_pin_kodu']); ?></span>
</p>
<p>
	<small>Şəxsiyyət vəsiqəsinin nömrəsi:</small><br>
	<span style="word-wrap: break-word;" ><?php print htmlspecialchars($poa['sv_nomresi']); ?></span>
</p>
<p>
	<small>Vəsiqəni təqdim edən orqan:</small><br>
	<span style="word-wrap: break-word;" ><?php print htmlspecialchars($poa['sv_teqdim_eden_orqan']); ?></span>
</p>
<p>
	<small>Ünvan:</small><br>
	<span style="word-wrap: break-word;" ><?php print htmlspecialchars($poa['unvan']); ?></span>
</p>
<p>
    <small>Məktubun qısa məzmunu:</small><br>
    <span style="word-wrap: break-word;" ><?php print htmlspecialchars($poa['qisa_mezmun']); ?></span>
</p>
<p>
    <small>Qeyd:</small><br>
    <span style="word-wrap: break-word;" ><?php print htmlspecialchars($poa['qeyd']); ?></span>
</p>

<?php print $intDoc->getRelatedInternalDocumentsHTMLTree(); ?>
<script>

</script>
