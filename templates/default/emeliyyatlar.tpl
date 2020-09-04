<style>
	.emeliyatlar a {
		border: none !important;
	}
</style>
<div class="modal-body form" style="padding: 0;">
	<div class="tabbable-line">
		<ul class="nav nav-tabs nav-justified emeliyatlar">
			<li data-emeliyat="cavab_mektubu_gonderilsin"><a href="javascript:void(0);">Cavab məktubu</a></li>
			<li data-emeliyat="ishe_tikilsin"><a href="javascript:void(0);">İşə tik</a></li>
			<li data-emeliyat="tabeli_quruma_gonderilsin"><a href="javascript:void(0);">Tabeli quruma</a></li>
			<li data-emeliyat="adiyati_orqana_gonderilsin"><a href="javascript:void(0);">Aidiyyatı orqana</a></li>
		</ul>
	</div>
	<hr>
	<form>
		<input type="hidden" name="daxil_olan_sened_id" value="$daxil_olan_sened_id$">
		<input type="hidden" name="derkenar_id" value="$derkenar_id$">

		<div id="emeliyyat_body" style="padding: 8px;">
		</div>
	</form>
</div>
<div class="modal-footer" style="border-top: 0; display: none">
	<div class="modal-footer" style="text-align: left">
		<button type="button" class="btn btn-circle green-meadow save">
			<i class="fa fa-check"></i> <span class="save-btn-text">Göndər</span>
		</button>
		<button type="button" data-dismiss="modal" class="btn btn-circle default">
			İmtina et
		</button>
	</div>
</div>
<script type="text/javascript" src="prodoc/settings.js"></script>

<script type="text/javascript">
	$(function() {
		var bm = $("#bosh_modal$MN$");
		Component.Plugin.PluginManager.init(bm);

		var birinciDefeBasir = true;
		bm.find('.emeliyatlar li').unbind('click').on('click', function() {
			var self = $(this);

			digerTabaKechmekIsteydiyinizeEminsinizi(function() {
				bm.find('.emeliyatlar li.active').removeClass('active');
				self.addClass('active');

				var emeliyat = self.data('emeliyat');

				var modalFooter = bm.find('.modal-footer');
				var saveBtnText = '';
				modalFooter.show();

				if ("cavab_mektubu_gonderilsin" === emeliyat) {
					saveBtnText = 'Göndər';
				} else if ("ishe_tikilsin" === emeliyat) {
					saveBtnText = 'İşə tikilsin';
				} else {
					saveBtnText = 'Göndər';
				}

				var url;
				if ("cavab_mektubu_gonderilsin" === emeliyat || "ishe_tikilsin" === emeliyat) {
					url = 'prodoc/modules/ajax/emeliyyatlar/' + emeliyat + '.php?mn=' + "$MN$";
				} else {
					url = 'prodoc/modules/ajax/emeliyyatlar/tabeli_quruma_orqana_gonderilsin.php?mn=' + "$MN$&emeliyat=" + emeliyat;
				}

				modalFooter.find('.save-btn-text').text(saveBtnText);

				$("#emeliyyat_body").load(url, function() {
					Component.Plugin.PluginManager.init($("#emeliyyat_body"));
				});
			}, birinciDefeBasir);
			birinciDefeBasir = false;
		});

		function digerTabaKechmekIsteydiyinizeEminsinizi(callbackOnOk, callRightNow)
		{
			if (callRightNow) {
				callbackOnOk();
			} else {
				var mn2 = modal_yarat("Əminsiniz?","<p style='padding-left: 20px;'>Digər əməliyyat pəncərəsinə keçdiyiniz halda qeyd etdiyiniz məlumatlar silinəcək!</p>","<button class='btn btn-danger testiqle' data-dismiss='modal'> Bəli</button> <button class='btn default cancel' data-dismiss='modal'>Xeyir</button>","btn-danger","");
				$("#bosh_modal"+mn2).attr("style", "z-index: 10051 !important");
				$("#bosh_modal"+mn2+" button.testiqle").unbind("click").click(function(){
					callbackOnOk($("#bosh_modal"+mn2));
				});
			}
		}
	});
</script>