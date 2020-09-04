<?php


$sened = $iDoc->getInfo();

function getApproveBtnTitle($emeliyyat_tip)
{
    $approveBtnTitleMap = [
        'mezuniyet_emri'  => 'Məzuniyyət əmri hazırla',
        'ezamiyet_emri'   => 'Ezamiyyət əmri hazırla',
        'melumatlandirma' => 'Tanış ol',
        'tesdiq_sifaris'  => 'Təhvil al',
        'viza'  => 'Razılaşdır',
    ];

    if (isset($emeliyyat_tip) && array_key_exists($emeliyyat_tip, $approveBtnTitleMap)) {
        $approveBtnTitle = $approveBtnTitleMap[$emeliyyat_tip];
    } else {
        $approveBtnTitle = 'Təsdiqlə';
    }

    return $approveBtnTitle;
}
?>


<?php if ($iDoc->canEdit()): ?>
<a type="button" vezife="edit" class="btn btn-outline btn-circle blue-steel"
   href="?id=<?php print $internalDocumentId; ?>&daxil_olan_sened_id=<?php print $id; ?>&hide_header=1&dsType=<?php print ltrim($tip,'#') ?>&module=prodoc_daxili_senedler<?php print $tip; ?>">Düzəliş et
</a>
<?php endif; ?>
<!--$incomingDocument = new Document($id);-->
<!--$sened = $incomingDocument->getInfo();-->

<?php if((int)$sened['state'] === Document::STATE_CANCELED) : ?>

    <?php if($iDoc->canDelete()): ?>
        <div class="btn-group">
            <button type="button" class="btn btn-outline btn-circle red-intense sened_sil">Sil</button>
        </div>
    <?php elseif ($iDoc->legvEdeBiler()): ?>
        <div class="btn-group">
            <button type="button" class="btn btn-outline btn-circle red-intense sened_legv_et">Ləğv et</button>
        </div>
    <?php endif; ?>

<?php endif; ?>


<?php if (!is_null($currentOperation)): ?>
	<?php $operation = $currentOperation['emeliyyat_tip'];?>

	<?php if ($operation === 'melumatlandirma'): ?>
		<button type="button" vezife="testiqle" data-tip="melumatlandirma" class="btn btn-outline btn-circle green-meadow">
			Tanış ol
		</button>
	<?php elseif ($operation === 'qiymetlendirme'): ?>
		<button type="button" class="btn btn-outline btn-circle red-intense qiymetlendirme"
				onclick='templateYukle("satin_alma","Mal-Material",{"sender_id": "<?php print $internalDocumentId; ?>", "edit":true, "testiq_id": "<?php print $currentOperation['id']; ?>"},80,true,"green");'>
			<i class="fa fa-commenting"></i> Qiymətləndirmə
		</button>
    <?php elseif ($operation === 'neticeni_qeyd_eden_sexs'): ?>
        <button type="button" class="btn btn-outline btn-circle red-intense neticeni_qeyd_eden_sexs"
                onclick='templateYukle("netice_satin_alma","Sifariş forması",{"sender_id": "<?php print $internalDocumentId; ?>", "edit":true, "testiq_id": "<?php print $currentOperation['id']; ?>"},80,true,"red");'>
            <i class="fa fa-commenting"></i> Nəticə qeyd et
        </button>
	<?php elseif ($operation === 'umumi_shobe_netice'): ?>
		<button type="button" class="btn btn-outline btn-circle red-intense umumi_shobe_netice"
				onclick='templateYukle("umumi_shobe_netice","Nəticə",{"sid": "<?php print $currentOperation['id']; ?>", "from_daxili": 1},0,true,"red");'>
			<i class="fa fa-commenting"></i> Nəticə qeyd et
		</button>
	<?php else: ?>
		<button type="button" vezife="testiqle" data-tip="<?= $operation ?>" class="btn btn-outline btn-circle green-meadow">
            <?php print getApproveBtnTitle($operation); ?>
		</button>
		<button type="button" vezife="imtina" class="btn btn-outline btn-circle red-intense">
			İmtina
		</button>
	<?php endif; ?>
<?php endif; ?>

<script type="text/javascript">
	$(".form-group label").css("padding-top", "13px");

	var testiq_id = "<?php print (!is_null($currentOperation)) ? $currentOperation['id'] : -1; ?>";

	<?php if (getProjectName()===TS): ?>
		$("button[vezife='testiqle']").click(function()
		{
			var tip = $(this).attr('data-tip');

			if (tip === "mezuniyet_emri") {
				location.href = "index.php?module=prodoc_daxili_senedler&vacation_id=<?php print $internalDocumentId; ?>#mezuniyet_emri";
			} else if (tip === "ezamiyet_emri") {
				location.href = "index.php?module=prodoc_daxili_senedler&hide_header=1&dsType=ezamiyet_emri&business_trip_id=<?php print $internalDocumentId; ?>#ezamiyet_emri";
			} else {
				sherhYazilsin(function(sebeb) {
					$.post("ajax/prodoc/formlar/form_approve.php",
						{
							'testiq_id': testiq_id,
							'note': sebeb
						},
						function(status)
						{
							refreshActiveDocument()
						}
					);
				}, $(this).text());
			}
		});
	<?php else: ?>
		$("button[vezife='testiqle']").click(function()
		{
			var tip = $(this).attr('data-tip');

			if (tip === "mezuniyet_emri") {
				location.href = "index.php?module=prodoc_daxili_senedler&vacation_id=<?php print $internalDocumentId; ?>#mezuniyet_emri";
			} else if (tip === "ezamiyet_emri") {
				location.href = "index.php?module=prodoc_daxili_senedler&hide_header=1&dsType=ezamiyet_emri&business_trip_id=<?php print $internalDocumentId; ?>#ezamiyet_emri";
			} else {
				$.post("ajax/prodoc/formlar/form_approve.php",
					{
						'testiq_id':testiq_id,
						'type':'<?php print $tip; ?>'
					},
					function(status)
					{
						refreshActiveDocument()
					}
				);
			}
		});
	<?php endif; ?>



	$("button[vezife='imtina']").click(function()
	{
        var senedAtach = '<div class="add-file-btn"><i class="fa fa-paperclip font-green-meadow" style="margin-left: 64px;"></i><button type="button" class="btn btn-link font-dark" style="padding: 6px;"><span style="font-weight: 500;">Sənəd əlavə et</span></button></div><div class="list-of-files" style="margin-left: 64px;"></div>';
		var mn2=modal_yarat("İmtina","<form class='form-horizontal form-bordered'><div class='form-body  file-upload'><div class='form-group'><label class='col-md-4 control-label'>Səbəb</label><div class='col-md-6'><textarea class='form-control' placeholder='Səbəb' maxlength='500' limit></textarea></div></div>"+senedAtach+"</div></form>","<button class='btn red btn-circle tesdiqle'>İmtina et</button> <button class='btn default btn-circle' data-dismiss='modal'>Bağla</button>","btn-danger","",true);
		$("#bosh_modal"+mn2+" textarea").textareaLimit();

        $("#bosh_modal"+mn2).find('.file-upload').fileUpload({
            name: 'sened'
        });

		$("#bosh_modal"+mn2+" button.tesdiqle").unbind("click").click(function()
		{
            var fd = Component.Form.collectData({form: "#bosh_modal"+mn2});

			var sebeb = $("#bosh_modal"+mn2+" textarea").val().trim();

            fd.append('testiq_id', testiq_id);
            fd.append('sebeb', sebeb);
			if(sebeb=="")
			{
				$("#bosh_modal"+mn2+" textarea").css("border","1px dashed red");
			}
			else
			{
				$("#bosh_modal"+mn2+" button.tesdiqle").css("border","");

                Component.Form.send({
                    form: fd,
                    url: 'ajax/prodoc/formlar/form_cancel.php',
                    success: function () {
                        refreshActiveDocument();
                        $("#bosh_modal"+mn2).modal('hide');
                    }
                });
			}
		});
	});

</script>