<style>
    .accordion-block{
        padding-bottom:15px;
    }
    textarea{
        resize: vertical;
    }
    .col-md-6,textarea{
        margin:5px 0;
    }
    .plus{
        width: 34px;
        display: block;
        border-radius: 50%;
        text-align: center;
        background: #1C8F5F;
        color:white;
        padding: 10px 0;
    }
    .minus{
        width: 34px;
        display: block;
        border-radius: 50%;
        text-align: center;
        background: red;
        color:white;
        padding: 10px 0;
    }

    #tapsirig_table thead th{
        vertical-align: middle;
        text-align: center;
    }

    .col-md-3.text-right{
        display: none;
    }
    .yeni_sened_container {
        display: none;
    }

    .dropdown-toggle{
        border: 0;
    }

</style>

<link rel="stylesheet" type="text/css" href="assets/plugins/select2/select2_metro.css"/>
<link rel="stylesheet" type="text/css" href="assets/css/balloon.css"/>
<script type="text/javascript" src="assets/plugins/select2/select2.min.js"></script>
<script src="prodoc/asset/js/underscore_mixin.js"></script>
<script src="prodoc/asset/widget/multiple.js"></script>
<script type="text/javascript" src="prodoc/asset/widget/hideShow.js"></script>
<script src="assets/scripts/tippy.all.min.js"></script>
<script type="text/javascript" src="assets/plugins/bootstrap-datepicker/js/locales/bootstrap-datepicker.az.js"></script>

<div id="bosh_modal$MN$">

    <div class="modal-body form" style="padding: 0;" id="tapsirig_emri">
        <form action="">
            <div class="whiteboard">
                <div class="col-md-12">
                    <input type="hidden" name="id" value="$id$">
                    <div class="blockname">
                        <h3 class="text-success text-left">
                            <strong>Sənədin nömrəsi</strong>
                        </h3>
                        <span class="accordion-toggler">
                            <i class="fa fa-minus"></i>
                        </span>
                    </div>
                </div>
                <div class="accordion-block">
                    <div class="row">
                        <div class="col-md-6 hidden">
                            <div class="form-group">
                                <label for="" class="col-md-12">
                                    Sənəd növü:
                                </label>
                                <div class="col-md-12">
                                    <select data-plugin="select2" class="form-control" name="sened_tip">
                                        <option value="2">İcra üçün</option>
                                        <option value="1">Məlumat üçün</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                $doc_num_input_html$
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="" class="col-md-12">
                                    Sənədin tarixi:
                                </label>
                                <div class="col-md-12">
                                    <input name="document_date" value="$current_date$" type="text" class="sened_tarix form-control" placeholder="Sənədin tarixi">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="whiteboard">
                <div class="col-md-12">
                    <div class="blockname">
                        <h3 class="text-success text-left">
                            <strong>İcra</strong>
                        </h3>
                        <span class="accordion-toggler">
                            <i class="fa fa-minus"></i>
                        </span>
                    </div>
                </div>
                <div class="accordion-block">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="" class="col-md-12">
                                    Kimə:
                                </label>
                                <div class="col-md-12">
                                    <input
                                            class="kime form-control"
                                            data-plugin="select2-ajax"
                                            data-plugin-params='{"multiple": true, "queryString": {"ne": "emekdash"}}'
                                            placeholder="Kimə" name="kime"
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="" class="col-md-12">
                                    Məlumat:
                                </label>
                                <div class="col-md-12">
                                    <input
                                            name="melumat"
                                            class="form-control"
                                            data-plugin="select2-ajax"
                                            data-plugin-params='{"multiple": true, "queryString": {"ne": "emekdash"}}'
                                            placeholder="Məlumat" name="melumat"
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="" class="col-md-12">
                                    Kimdən:
                                </label>
                                <div class="col-md-12">
                                    <input
                                            name="rey_muellifi"
                                            class="form-control"
                                            data-plugin="select2-ajax"
                                            data-plugin-params='{"queryString": {"ne": "rey_muellifleri", "tip": "daxili"}}'
                                            placeholder="Kimdən"
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="whiteboard">
                <div class="col-md-12">
                    <div class="blockname">
                        <h3 class="text-success text-left">
                            <strong>Mövzu</strong>
                        </h3>
                        <span class="accordion-toggler">
                            <i class="fa fa-minus"></i>
                        </span>
                    </div>
                </div>
                <div class="accordion-block">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="" class="col-md-12">
                                    Mövzu:
                                </label>
                                <div class="col-md-12"><input name="movzu" type="text" class="form-control"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="" class="col-md-12">
                                    Giriş:
                                </label>
                                <div class="col-md-12">
                                    <textarea  rows="2" class="form-control" name="girish"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="" class="col-md-12">
                                    Məqsəd:
                                </label>
                                <div class="col-md-12">
                                    <textarea rows="2" class="form-control" name="meqsed"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script type="template/underscore" id="tapsirig_row">
                <tr data-function="item">
                    <td>
                        1
                    </td>
                    <td>
                        <input type="text" class="form-control" placeholder="Qeyd" name="derkenar_metn[]">
                    </td>
                    <td>
                        <input
                                name="kime[]"
                                class="kime form-control"
                                placeholder="Kimə"
                        >
                    </td>
                    <td data-balloon="Müraciət olunduqda" data-balloon-pos="left">
                        <input type="text" class=" form-control muddet" style="display: inline-block;width: 80%;" name="icra_edilme_tarixi[]">
                        <input placeholder="Müddət" type="checkbox" class=" dateDisabler" data-plugin="uniform" name="icra_edilme_tarixi_disabled[]">
                    </td>
                    <td>
                        <i  class="fa fa-minus minus" data-function="action-remove"></i>
                    </td>
                </tr>
            </script>

            <div class="whiteboard">
                <div class="col-md-12">
                    <div class="blockname">
                        <h3 class="text-success text-left">
                            <strong>Tapşırıqlar</strong>
                        </h3>
                        <span class="accordion-toggler">
                                    <i class="fa fa-minus"></i>
                                </span>
                    </div>
                </div>
                <div class="accordion-block">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="" class="col-md-12">
                                    Mövzu:
                                </label>
                                <div class="col-md-12">
                                    <table id="tapsirig_table" class="table table-bordered table-hover table-striped" style="table-layout: fixed">
                                        <thead>
                                            <tr>
                                                <th width="20"><strong>№</strong></th>
                                                <th width="120"><strong>Tapşırıq</strong></th>
                                                <th width="120"><strong>Kimə</strong></th>
                                                <th width="100"><strong>Müddət</strong></th>
                                                <th width="30">
                                                    <i data-function="action-add" class="fa fa-plus plus"></i>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody data-function="container">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="col-md-12">
                                    Xüsusi qeydlər <input type="checkbox" data-plugin="uniform" name="show_outgoing_document_container">
                                </label>
                                <div class="col-md-12 outgoing-document-container" style="display: none" >
                                    <textarea class="form-control" rows="2" name="xususi_qeydler"></textarea>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <p class="text-center" style="padding: 5px 25px">
                                Tapşırıqla bağlı hər-hansı problem yarandıqda Bakı, Anama-nın BQ-sindəki ANAMA-nın ƏM-i ilə əlaqə saxlanılmalıdır. Tapşırığın
                                alınması imza ilə təsdiqlənməlidir və ilkin nüsxə ANAMA-nın ƏŞ-sinə qaytarılmalıdır.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="modal-footer" style="border-top: 0;">
        <div style="float: left; color: red;" vezife="error"></div>
        <div>
            <button type="button" data-v="testiqle" class="btn green save btn-circle">İcraya göndər</button>
        </div>
    </div>
</div>
<script src="prodoc/modules/daxili_senedler/appendGeneralData.js"></script>


<script>

   $(function () {

       var tapsirig_emri=$("#tapsirig_emri");
       var tapsirig_table=$("#tapsirig_table")

       tapsirig_emri.find('[name=show_outgoing_document_container]').hideShow({
           showWhenChecked: tapsirig_emri.find(".outgoing-document-container"),
           hideWhenChecked: tapsirig_emri.find(".hide-when-binding-to-outgoing-document"),
           animation: 'slide'
       });

       function initJsPlugs() {
           $("input:checkbox").uniform();
           var docDate = $('[name=document_date]');

           $("#bosh_modal$MN$ .muddet").datepicker({
               autoclose: true,
               format: "dd-mm-yyyy",
               startDate: docDate.datepicker('getDate'),
               language: 'az'
           });
       }

       $("#bosh_modal$MN$ .sened_tarix").datepicker({
		   autoclose: true,
		   format: "dd-mm-yyyy",
           language: 'az',
       });

       $("#bosh_modal$MN$ .muddet, #bosh_modal$MN$ .sened_tarix").on('keydown', function() {
           event.preventDefault();
       });

       initJsPlugs()

       tapsirig_table.multiple({
           itemTemplateId: 'tapsirig_row',
           prepend: false,
           initialItem:false,
           beforeAppend:function(item,e,extra){

               var data=$("#bosh_modal$MN$ .kime").select2('data')
               var len=data.length;

               if(len==0){
                   return false;
               }

               var lasttr=tapsirig_table.find("tbody>tr:last").not(".input_row");
               var inputtd=lasttr.find("td").eq(2).find("input");
               var data=inputtd.select2('data')
               if(_.isNull(data) || !data.text){
                   return false;
               }
           },
           afterAppend:function(item){

               initJsPlugs()
               Component.Plugin.PluginManager.init(item);
               var axtarish = Component.Plugin.Plugin.select2AjaxPlugin.init.bind(Component.Plugin.Plugin.select2AjaxPlugin);

               tapsirig_table.on("click",".dateDisabler",function () {
                   if (!$(this).prop('checked')) {
                       $(this).parents("tr").find(".muddet").removeAttr("readonly");
                   } else {
                       $(this).parents("tr").find(".muddet").attr("readonly",true)
                   }
               })
               tapsirig_table.find("tr:visible").each(function (i,obj) {
                   $(obj).find("td").eq(0).text(i);
               })

               var data=$("#bosh_modal$MN$ .kime").select2('data')

               tapsirig_table.find("input.kime").each(function (i,obj) {
                   $(obj).select2({
                       data:data
                   })
               })

           },
           afterDelete:function(item){
               tapsirig_table.find("tr:visible").each(function (i,obj) {
                   $(obj).find("td").eq(0).text(i);
               })
           }
       })

       $("#bosh_modal$MN$ .kime").on("change",function(){
           var data=$(this).select2('data');
           var len=data.length,
               tapsirig_item = [],
               tapsirig_count = tapsirig_table.find("tbody>tr");

           $(tapsirig_count).each(function (i) {
               var userID = $(this).find("td").eq(2).find("input").select2("data")['id']
               if(!_.isUndefined(userID))
               {
                 tapsirig_item[userID] = {
                    "title":  $(this).find("td").eq(1).find("input").val(),
                    "muddet": $(this).find("td").eq(3).find("input").val(),
                    "check":  $(this).find("td").eq(3).find("input").is(':checked') ? 1 : 0,
                 };
               }
           });

           tapsirig_table.find('tr.input_row').remove();
           for (var i=0;i<len;i++) {
               $("#bosh_modal$MN$ .plus").click();
               var lastItem = tapsirig_table.find("[data-function=item]:last");
               lastItem.addClass("input_row");

               tapsirig_item.forEach(function (item,index) {
                   if(index == data[i].id)
                   {
                       lastItem.find("td").eq(1).find("input").val(tapsirig_item[index].title);
                       lastItem.find("td").eq(3).find("input").val(tapsirig_item[index].muddet);
                       if(tapsirig_item[index].check) lastItem.find("td").eq(3).find("input.dateDisabler").trigger('click')
                   }
               });
               lastItem.find("td").eq(2).find("input").select2("data", data[i]);
               lastItem.find("td").eq(2).find("input").select2('readonly', true);
               tapsirig_table.find("tbody").append(lastItem);
           }

           tapsirig_table.find('tr.input_row').find(".minus").hide();
       });

	   var modal = $("#bosh_modal$MN$");

       Component.Plugin.PluginManager.init(modal);

	   modal.find('.save').on('click', function() {

           var fd = new FormData();
           getFiles(fd);

           Component.Form.send({
			   url: 'prodoc/ajax/task_command/add_edit_controller.php',
			   sendUncheckedCheckbox: true,
               existingFormData: fd,
			   form: modal,
			   success: function (res) {
				   proccessResponse(res);
			   }
		   });

	   });

	   var formData = $formData$;
	   if (!_.isNull(formData)) {

	   	    Component.Form.setData(modal, formData);
          console.log( formData);
             modal.find('[name="sened_tip"]').val(formData.sened_tipi).change();
           if (typeof formData.tasks !== 'undefined') {
               if (formData.tasks.length>0){

                   formData.tasks.forEach(function (element,i) {
                       $('#tapsirig_table tbody tr').eq(i).find('[name="derkenar_metn[]"]').val(element.derkenar_metn_ad);
                       if(!_.isNull(element.son_icra_tarixi) && element.son_icra_tarixi !="" ){
                           $('#tapsirig_table tbody tr').eq(i).find('[name="icra_edilme_tarixi[]"]').val(element.son_icra_tarixi);
                       }else{
                           $('#tapsirig_table tbody tr').eq(i).find('[name="icra_edilme_tarixi_disabled[]"]').trigger('click');
                       }
                   })
               }

               if(formData.xususi_qeydler!=""){
                   $('[name="show_outgoing_document_container"]').trigger('click');
               }
           }

	   }

       var executor = JSON.parse('$executor$');

       if(executor.length == 1)
       {
           $('input[name="rey_muellifi"]').select2('data', executor[0]);
           $('input[name="rey_muellifi"]').select2('readonly', true);
       }

   })


</script>