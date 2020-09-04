<style type="text/css">
	#tab_13 tbody[vezife='menyular1'] td{
		cursor: pointer;
	}
</style>

<div id="tab_13">
	<div class="row">
		<div class="col-md-10">
			<table class="table table-striped table-bordered table-advance table-hover" id="contact_types">
				<thead>
				<tr>
					<th>№</th>
					<th>Ad</th>
					<th style="width: 200px;">Əlaqəli şəxs</th>
					<th style="width: 200px;">Alt qurum</th>
					<th style="width: 200px;">Tip</th>
					$theadElements$
				</tr>
				</thead>
				<tbody vezife="menyular1">
					$contactTypes$
				</tbody>
			</table>
		</div>
	</div>
</div>

<script type="text/javascript">

	$('select').select2();

	var fildTypesHtml = '<select class="form-control" name="tip"><option></option>$tipler$</select>';
	var fildTypesHtml2 = '<input name="mesul_shexs"class="form-control select"vezife="mesul_shexs" data-id="1" placeholder="Alt qurum">';
	var fildTypesHtml3 = '<input name="elaqeli_shexs"class="form-control select"vezife="elaqeli_shexs" data-id="1" placeholder="Əlaqəli şəxs">';

	$("#tab_13 tbody[vezife='menyular1'] tr:first td:first").click();

	//// CONTACT TYPES ////
	$('#contact_type_add').click(function()
	{
		var tableId = "contact_types";
		if($("#"+tableId+" tbody tr[time='null']").length)
		{
			$('#'+tableId+' tbody tr').remove();
		}
		var say = $('#'+tableId+' tbody tr').length+1;
		$('#'+tableId+' tbody').append("<tr tr_id='0' del='1'><td style='width:20px;text-align:center;'>"+say+"</td><td><input maxlength='75' class='form-control'></td><td>"+fildTypesHtml3+"</td><td>"+fildTypesHtml2+"</td><td>"+fildTypesHtml+"</td><td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs blue'><i class='fa fa-save'></i> </a><a href='javascript:;' class='btn default btn-xs yellow'><i class='fa fa-remove'></i> </a></td></tr>");
		$('[name="mesul_shexs]"').select2('data',[1,2,3]);
		$('input[maxlength]').maxlength();
		$('#'+tableId+' tbody').find('select').select2();
		$('#'+tableId+' tbody tr:last .yellow').click(function()
		{
			$(this).parent('td').parent('tr').remove();
		});
        select();
	});

	$('#contact_types>tbody').on("click",".blue",function(e)
	{
		var tableId = $(this).parents("table").eq(0).attr("id"),
			t = $(this),
			tr = t.parents("tr").eq(0),
			sayi = tr.children('td').eq(0).text(),
			ad = tr.children('td').eq(1).find("input").val(),
			fildType = tr.children('td').eq(4).find("select").val(),
			fildTypeName = tr.children('td').eq(4).find("select option:selected").text(),
			fildType2 = tr.children('td').eq(3).attr('data-qurum-ad'),
			fildTypeName2 = tr.children('td').eq(3).attr('data-qurum'),
            fildType3 = tr.children('td').eq(2).attr('data-elaqeli-shexs'),
            fildTypeName3 = tr.children('td').eq(2).attr('data-elaqeli'),
			tid = tr.attr("tr_id");
console.log(tr.children('td').eq(2));
		// seçilib?
		var sechilib = "";
		if (tr.find("td[sechilib='1']").length) {
			sechilib = "sechilib='1'";

            console.log(sechilib);
		}
        var disabled = "";
		if (tr.attr('del') === '0') disabled = " disabled ";

		if(ad.trim()=="" || fildTypeName == "")
		{
			tr.after('<tr error><td colspan="100%" style="color:red;font-size:11px;">$9956error2$</td></tr>');
			setTimeout(function(){$('#'+tableId+' tbody tr[error]').remove();}, 3000);
		}
		else
		{
            var alt_qurum=$('input[name="mesul_shexs"]').val(), elaqeli_sexs = $('input[name="elaqeli_shexs"]').val();
			$.post("prodoc/ajax/msk/CustomersCompany.php", {'tid':tid,'ad':ad, "fildType":fildType,'alt_qurum':alt_qurum, 'elaqeli_sexs':elaqeli_sexs},function(netice)
			{
				if(netice == "error")
				{
					tr.after('<tr error><td colspan="100%" style="color:red;font-size:11px;">$9956error3$</td></tr>');
					setTimeout(function(){$('#'+tableId+' tbody tr[error]').remove();}, 3000);
				}
				else
				{
				    console.log($('input[name="mesul_shexs"]').val());
				    var valuesOfInstitution=$('input[name="mesul_shexs"]').val().split(",");
				    var arrOfInstitution= $('input[name="mesul_shexs"]').select2('data');
                    var fildTypeName2="";
                    var fildType2="[";
                    var count=0;
                    arrOfInstitution.forEach(function(element) {
                        if(arrOfInstitution.length-1>count){
                            fildTypeName2+= element.text+",";
							fildType2+="{ \"id\" : "+valuesOfInstitution[count]+", \"text\": \""+element.text+"\" },";
						}
						else{
                            fildTypeName2+= element.text;
                            fildType2+="{ \"id\" : "+valuesOfInstitution[count]+", \"text\": \""+element.text+"\" }";
                        }
						count++;

                    });
                    fildType2+="]";

                    var valuesOfInstitution1=$('input[name="elaqeli_shexs"]').val().split(",");
                    var arrOfInstitution1= $('input[name="elaqeli_shexs"]').select2('data');
                    var fildTypeName3="";
                    var fildType3="[";
                    var size=0;
                    arrOfInstitution1.forEach(function(element) {
                        if(arrOfInstitution1.length-1>count){
                            fildTypeName3+= element.text+",";
                            fildType3+="{ \"id\" : "+valuesOfInstitution1[size]+", \"text\": \""+element.text+"\" },";
                        }
                        else{
                            fildTypeName3+= element.text;
                            fildType3+="{ \"id\" : "+valuesOfInstitution1[size]+", \"text\": \""+element.text+"\" }";
                        }
                        count++;

                    });
                    fildType3+="]";

					//console.log(fildType2);
                    // forEach(arrOfInstitution as arr){
                    //
                    //
					// }
				    console.log();

					tr.attr("tr_id",netice).html("<td style='width:20px;text-align:center;'>"+sayi+"</td><td>"+ad+"</td><td data-elaqeli-shexs='"+fildType3+"'>"+fildTypeName3+"</td><td data-qurum-ad='"+fildType2+"'>"+fildTypeName2+"</td><td val="+fildType+" "+sechilib+">"+fildTypeName+"</td><td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs purple'><i class='fa fa-edit'></i> </a><a "+disabled+" href='javascript:;' class='btn default btn-xs red'><i class='fa fa-trash'></i> </a></td>");
                    select();
				}
			});
		}
		e.stopPropagation();
	});

	$('#contact_types>tbody').on("click",".purple",function(e)
	{
		var th = $(this).parent('td').parent('tr'),
			idN = th.attr("tr_id"),
			ad = th.children("td").eq(1).text(),
			fildType = th.children("td").eq(3).attr('val'),
			fildType2 = th.children("td").eq(3).attr('data-qurum-ad'),
			fildTypeName2 = th.children("td").eq(3).text(),
            fildTypeName = th.children("td").eq(4).text(),
            tipval = th.children("td").eq(4).attr('val'),
            fildType1 = th.children("td").eq(2).attr('val'),
            fildType3 = th.children("td").eq(2).attr('data-elaqeli-shexs'),
            fildTypeName3 = th.children("td").eq(2).text(),

			say = $(this).parent('td').parent("tr").children('td').eq(0).text();

		// seçilib?
		var sechilib = "";
		if (th.find("td[sechilib='1']").length) {
			sechilib = "sechilib='1'";
		}

		var disabled = "";
		if (th.attr('del')=='0') disabled = " disabled ";

		th.html("<td style='width:20px;text-align:center;'>"+say+"</td><td><input  maxlength='75' class='form-control' value='"+ad+"'></td><td data-elaqeli-shexs='"+fildType3+"' data-elaqeli='"+fildTypeName3+"'>"+fildTypesHtml3+"</td><td data-qurum-ad='"+fildType2+"' data-qurum='"+fildTypeName2+"' >"+fildTypesHtml2+"</td><td "+sechilib+">"+fildTypesHtml+"</td><td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs blue'><i class='fa fa-save'></i> </a><a href='javascript:;' class='btn default btn-xs yellow'><i class='fa fa-remove'></i> </a></td>");
		$('input[maxlength]').maxlength();
        th.find('select').select2();
		th.find('select').select2('val', fildType);
		th.find('select').select2('val', fildType1);
        th.find('select[name="tip"]').select2("data", {"id": tipval, "text": fildTypeName });


//        th.find('select').select2().select2('val',JSON.stringify(fildType) );
//        th.find('select').select2().select2('val',JSON.stringify(fildType1) );

        th.find('.yellow').click(function()
		{
			th.html("<td style='width:20px;text-align:center;'>"+say+"</td><td>"+ad+"</td><td data-elaqeli-shexs='"+fildType3+"'>"+fildTypeName3+"</td><td  data-qurum-ad='"+fildType2+"' >"+fildTypeName2+"</td><td val="+fildType+" "+sechilib+">"+fildTypeName+"</td><td style='text-align:center;'><a href='javascript:;' class='btn default btn-xs purple'><i class='fa fa-edit'></i> </a><a "+disabled+" href='javascript:;' class='btn default btn-xs red'><i class='fa fa-trash'></i> </a></td>");
		});
		select();
		console.log(fildType2);
        th.find('[name="mesul_shexs"]').select2('data', JSON.parse(fildType2));
        th.find('[name="elaqeli_shexs"]').select2('data', JSON.parse(fildType3));

        e.stopPropagation();
	});

	$('#contact_types>tbody').on("click",".red",function(e)
	{
		tableId = $(this).parents("table").eq(0).attr("id");
		var idsi = $(this).parent('td').parent('tr').attr("tr_id");
		var t = $(this);
		var alt_qurum = $('input[name="mesul_shexs"]').val();
		var elaqeli_shexs = $('input[name="elaqeli_shexs"]').val();

		swals({
				title: "$9956eminsinizhead$",
				text: "$9956eminsinizdesc$",
				type: "warning",
				showCancelButton: true,
				confirmButtonClass: "btn-danger",
				confirmButtonText: "$9956ok$",
				cancelButtonText: "$9956bagla$",
				closeOnConfirm: true
			},
			function(){
				$.post("prodoc/ajax/msk/CustomersCompany.php", {'tid':idsi,'ne':"sil",'alt_qurum':alt_qurum, 'elaqeli_shexs': elaqeli_shexs}).done(function(netice)
				{
					t.parent('td').parent('tr').remove();
					if($('#'+tableId+' tbody').children('tr').children('td').length==0)
					{
						$('#'+tableId+' tbody').html("<tr time='null'><td colspan='100%'>$9956empty$</td></tr>");
					}
					swals("$9956silindi$", "$9956silindi$", "success");
				});
			});
		e.stopPropagation();
	});
  function select() {
      var axtarish = Component.Plugin.Plugin.select2AjaxPlugin.init.bind(Component.Plugin.Plugin.select2AjaxPlugin);
      axtarish($('input[name="mesul_shexs"]'), {
          allowClear:false,
		  multiple:true,
          getAjaxData: function () {
              return {
                  'ne': 'alt_qurum',
				  'a':''

              }
          }
      });

      axtarish($('input[name="elaqeli_shexs"]'), {
          allowClear:false,
          multiple:true,
          getAjaxData: function () {
              return {
                  'ne': 'elaqeli_shexs',
                  'a':''

              }
          }
      });


      // var kurator = $('input[name=""]');
      // var axtarish = Component.Plugin.Plugin.select2AjaxPlugin.init.bind(Component.Plugin.Plugin.select2AjaxPlugin);
      // axtarish(kurator, {
      //     allowClear:false,
      //     getAjaxData: function (t) {
      //         return {
      //             'ne': 'emekdash',
      //             'extra_param': 5,
      //             'extra_emekdash': collectValues()
      //
      //         }
      //     }
      // });
  }
</script>