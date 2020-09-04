<?php
defined('DIRNAME_INDEX') or die("hara?");


$MN = 0;
$pr = (int)$user->checkPrivilegia("gorushler");
$sid = $id;
$userId = (int)$_SESSION['erpuserid'];
$language = $user->getLang();
$dill = new dilStclass($language);

$yoxlagorush = pdof()->query("SELECT * FROM tb_gorushler WHERE id='$sid'")->fetch();
$arayishVar = pdof()->query("select arayish_teleb_etdi from tb_gorushler WHERE id ='$sid'")->fetchColumn();
($arayishVar=="")? $arayishVar=0:$arayishVar=1;

    if(!$yoxlagorush)
    {
        print json_encode(array("status"=>"hazir","template"=>htmlspecialchars("<div style='color:red; padding:20px;'>".dil::soz("11bele_gorush_yoxdur")."</div>",ENT_QUOTES)));
        exit();
    }

    $getUserInfo = pdof()->query("SELECT * FROM tb_users WHERE USERID='$userId'")->fetch();
    $userGroup = (int)$getUserInfo['Groupp'];

    $pri_check = pdof()->query("SELECT module,(SELECT top 1 privs FROM tb_group_privs_new tb2 WHERE tb2.group_id=$userGroup AND tb2.modul_id=tb1.id ORDER by id DESC ) privs FROM tb_modules tb1 WHERE tb1.module='ish_tapshiriqi' OR tb1.module='report_id1_new' OR tb1.module='report_mevacib' OR tb1.module='gorush_report'");

    $pri = array();

    $gorushInf = pdof()->query("SELECT tb_gorushler.*,v_user_adlar.user_ad_qisa AS arayish_teleb_etdi_ad,tb10.id AS arayish_id FROM tb_gorushler LEFT JOIN v_user_adlar ON tb_gorushler.arayish_teleb_etdi=v_user_adlar.USERID LEFT JOIN tb_arayishlar tb10 ON tb10.menbe='gorushler' AND tb10.menbe_id=tb_gorushler.id WHERE tb_gorushler.id='$sid'")->fetch();
    $testiqBtn = false;
    $qebulBtn = false;
    $editImtinaBtn =false;

    if(((int)$gorushInf['user_id']===$userId || (int)$gorushInf['gorushu_teyin_eden_user']===$userId || in_array($userId,explode(",",$gorushInf['rehberler'])) || in_array($userId,explode(",",$gorushInf['ishtirakchilar'])) || in_array($userId,explode(",",$gorushInf['tesdiqleme_geden_userler'])) || pdof()->query("SELECT 1 FROM tb_gorushler_tesdiqleme WHERE gorush_id='$sid' AND (user_id='$userId' OR vekaletname_user='$userId')")->fetch()) || (isset($pri['ish_tapshiriqi']) && $pri['ish_tapshiriqi']==2) || (isset($_POST['forTimecontrol']) && $_POST['forTimecontrol']==true && isset($pri['report_id1_new']) && $pri['report_id1_new']==2) || (isset($_POST['forReportp1']) && $_POST['forReportp1']==true && (isset($pri['report_mevacib']) && $pri['report_mevacib']!=0)) || (isset($pri['gorush_report']) && $pri['gorush_report']!=0) )
    {
        $changeNotifStatus = pdof()->query("UPDATE tb_notifications SET status='1' WHERE bolme='gorushler' AND kid='$sid' AND user_id='$userId'");
        $getCustomerInfo = pdof()->query("SELECT CONCAT(Soyadi, ' ', Adi) FROM tb_Customers WHERE id='" . (int)$gorushInf['customer'] . "'")->fetch();
        $getUserInf1 = pdof()->query("SELECT CONCAT(Soyadi, ' ', Adi) FROM tb_users WHERE USERID='" . (int)$gorushInf['user_id'] . "'")->fetch();
        $getCompInf = pdof()->query("SELECT Adi FROM tb_CustomersCompany WHERE id='" . (int)$gorushInf['company'] . "'")->fetch();
        $getUserInf2 = pdof()->query("SELECT CONCAT(Soyadi, ' ', Adi) FROM tb_users WHERE USERID='" . (int)$gorushInf['gorushu_teyin_eden_user'] . "'")->fetch();

        $sebeb22="sehv";
        $imtinaEdens="sehv";
        $imtina_uid='';
        if($gorushInf['status']==3)
        {
            $imtinaEden = $gorushInf['imtinaEden'];
            $imtinaEdenInf = pdof()->query("SELECT USERID,CONCAT(Soyadi, ' ', Adi) FROM tb_users WHERE USERID='" . $imtinaEden . "'")->fetch();
            $sebeb = $gorushInf['imtinaSebebi'];
            $imtina_uid=$imtinaEdenInf['USERID'];
            $getImtinaDate = pdof()->query("SELECT TOP 1 date FROM tb_gorushler_logs WHERE gorush_id='$sid' AND ne='4' ORDER BY date DESC")->fetch();
            $imtinaEdens=htmlspecialchars($imtinaEdenInf[1]) . " - " . date("d-m-Y H:i", strtotime($getImtinaDate[0]));
            $sebeb22=htmlspecialchars($sebeb);
        }



        $edits = array();
        $getLogsEdit = pdof()->query("SELECT * FROM tb_gorushler_logs WHERE gorush_id='" . $sid . "' AND ne='1'");
        while($logEdit = $getLogsEdit->fetch())
        {
            $getuserInff = pdof()->query("SELECT CONCAT(Soyadi, ' ', Adi),USERID FROM tb_users WHERE USERID='" . (int)$logEdit['user_id'] . "'")->fetch();
            $edits[] = array($getuserInff[0], date("d-m-Y H:i", strtotime($logEdit['date'])), htmlspecialchars($logEdit['qeyd']),$getuserInff[1]);
        }
        $getQebulLog2 = pdof()->query("SELECT TOP 1 tb_gorushler_logs.*,user_ad FROM tb_gorushler_logs LEFT JOIN v_user_adlar ON USERID=user_id WHERE gorush_id='$sid' AND ne='2' ORDER BY date DESC")->fetch();

        $carQrup = pdof()->query("SELECT MIN(qrup) FROM tb_gorushler_tesdiqleme WHERE gorush_id='$sid' AND status='0'")->fetch();
        $carQrup = (int)$carQrup[0];

        $tesdiqleyenler = pdof()->query("SELECT tb1.*,tb2.user_ad,tb3.user_ad AS vekaletname_user_ad FROM tb_gorushler_tesdiqleme tb1 LEFT JOIN v_user_adlar tb2 ON tb2.USERID=tb1.user_id LEFT JOIN v_user_adlar tb3 ON tb3.USERID=tb1.vekaletname_user WHERE tb1.gorush_id='$sid' AND tb1.emeliyyat_tip='tesdiqleme'");
        $tr1 = '';
        $tr2 = '';
        $tesdiqBtn = false;
        $vekaletnameUserYoxla = 0;

        while($trInfo = $tesdiqleyenler->fetch())
        {
            if((int)$trInfo['status']===1)
            {
                $tr1 .= "<div visit_uid=".(int)$trInfo['user_id'].">".htmlspecialchars($trInfo['user_ad'],ENT_QUOTES).((int)$trInfo['vekaletname_user']>0?" <i>(<a href='javascript:templateYukle(\"vekaletname_etrafli\",\"".dil::soz("11elektron_vekaletname__etrafli")."\",{\"sid\":\"".(int)$trInfo['vekaletname']."\"},0,true,\"btn-info\");'>".dil::soz("11evekaletname")."</a>: <span style='color:#68B4F1;'>".htmlspecialchars($trInfo['vekaletname_user_ad'],ENT_QUOTES)."</span>)</i>":"")." - <i class='icon-clock'></i> ".date("d-m-Y H:i:s",strtotime($trInfo['tesdiqleme_tarixi']))."</div>";
            }
            else
            {
                if((int)$trInfo['vekaletname_user']===$userId && $carQrup==(int)$trInfo['qrup'])
                {
                    $tesdiqBtn = true;
                    $vekaletnameUserYoxla = (int)$trInfo['user_id'];
                }
                if((int)$trInfo['user_id']===$userId && $carQrup==(int)$trInfo['qrup'])
                {
                    $tesdiqBtn = true;
                }
                $tr2 .= "<div visit_uid=".(int)$trInfo['user_id']. ">".htmlspecialchars($trInfo['user_ad'],ENT_QUOTES).((int)$trInfo['vekaletname_user']>0?" <i>(<a href='javascript:templateYukle(\"vekaletname_etrafli\",\"".dil::soz("11elektron_vekaletname__etrafli")."\",{\"sid\":\"".(int)$trInfo['vekaletname']."\"},0,true,\"btn-info\");'>".dil::soz("11evekaletname")."</a>: <span style='color:#68B4F1;'>".htmlspecialchars($trInfo['vekaletname_user_ad'],ENT_QUOTES)."</span>)</i>":"")."</div>";
            }
        }

        if($tesdiqBtn && (in_array($userId,explode(",",$gorushInf['tesdiqleme_geden_userler'])) || ($vekaletnameUserYoxla>0 && in_array($vekaletnameUserYoxla,explode(",",$gorushInf['tesdiqleme_geden_userler']))))===false)
        {
            $tesdiqBtn = false;
        }
       $editImtinaBtn= (((int)$gorushInf['status']!==3 && (in_array($userId,explode(",",$gorushInf['tesdiqleme_geden_userler']))))  || $userId==$gorushInf['user_id'] || ($vekaletnameUserYoxla>0 && in_array($vekaletnameUserYoxla,explode(",",$gorushInf['tesdiqleme_geden_userler']))) || $userId==$gorushInf['gorushu_teyin_eden_user'])?1:0;
        if(isset($pri['gorush_report']) && isset($parametrler['report']) && $parametrler['report']=='gorush')
        {
            $tesdiqBtn=0;
            $editImtinaBtn=0;
            $qebulBtn=false;
        }

    }



?>

<div id="buttonlar">
		<div class="btn-group" vezife="export"><a type="button" class="btn btn-default" style="margin: 0;" href="pages/gorushler.php?id=0" target="_blank"><i class="fa fa-download"></i></a><button aria-expanded="false" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><i class="fa fa-angle-down"></i></button><ul class="dropdown-menu" role="menu"><li><a href="pages/gorushler.php?id=0" target="_blank">HTML</a></li><li><a href="pages/gorushler.php?id=0&export=pdf">PDF</a></li></ul></div>

    <a type="button" vezife="edit" class="btn btn-circle btn-info"
       href="?id=<?= $id ?>&module=prodoc_daxili_senedler#ish_tapshirigi">Düzəliş et
    </a>
    <button type="button" vezife="testiqle" class="btn btn-circle green">Təstiqlə</button>
		<button type="button" vezife="qebulet" class="btn btn-circle green">Qəbul et</button>
		<button type="button" vezife="imtina" class="btn btn-circle btn-danger" data-toggle="modal">İmtina et</button>

	</div>

<script>

	if(parseInt('<?= $tesdiqBtn ?>')!=1)
    {
        $("button[vezife='testiqle']").hide();
    }

	if(parseInt('<?= $qebulBtn ?>')!=1)
    {
        $("button[vezife='qebulet']").hide();
    }
    else
    {
        $("button[vezife='qebulet']").css("display","inline");
    }

	if(parseInt('<?= $editImtinaBtn ?>')!=1)
    {
        $("button[vezife='imtina']").hide();
    }






	var sid=parseInt('<?= $sid ?>');


	function yazdir_notify(sid,hansi){
        var lenn = $("#notifySiyahisi li[kid='"+sid+"'][bolme='"+hansi+"']").length,
				say = $("#notifyaSayi").text();
		$("#notifySiyahisi li[kid='"+sid+"'][bolme='"+hansi+"']").remove();
		$("#notifyaSayi").text(parseInt(say)-parseInt(lenn));
	}

	//$("#bosh_modal$MN$ div[vezife='export']").html($("#bosh_modal$MN$ div[vezife='export']").html().replace(/gorushler\.php\?id\=([0-9]+)/g,"gorushler.php?id="+sid));

	$("button[vezife='testiqle']").click(function()
    {
        var gId =sid,
				arayish_teleb_etdi= parseInt('<?= $arayishVar ?>');    //$("div[vezife='arayish_teleb_olunur'] input[type='checkbox']").is(":checked")?1:0;

		if(gId>0)
        {
            $.post("ajax/proid/work_tasks/gorushTestiqle.php", {'gid':gId,'arayish_teleb_etdi':arayish_teleb_etdi}, function(status)
			{

                yazdir_notify("$sid$","gorushler");
                location.reload();
                //if(module=='dashboard')
                //{
                //	$("tr[ne=gorushler]"+"tr[tid='" + $sid$ + "']").fadeOut(150,function(){$(this).remove();table_siralama()}
                //	);
                //	toastr['success']('$11gorushutesdiqlediniz$')
                //	$("#bosh_modal$MN$").modal("hide");
                //}
                //else
                //{
                //	toastr['success']('Görüşü Təsdiqlədiniz');
                //	var btnType = (status==0) ? ["danger", "$11testiqlenmeyib$"] : ["success", "$11testiqlenib$"];
                //	var aa = $("tbody[vezife='gorushlerSiyahi'] tr[gid='" + gId + "']");
                //	aa.children("td").eq(8).html('<button type="button" class="btn btn-xs btn-' + btnType[0] + '">' + btnType[1] + '</button>');
                //	aa.fadeOut(300, function()
                //	{
                //		$(this).children("td").eq(8).html('<button type="button" class="btn btn-xs btn-' + btnType[0] + '">' + btnType[1] + '</button>');
                //		aa.fadeIn(300);
                //	});
                //	$("#bosh_modal$MN$ .close[data-dismiss='modal']").click();
                //}
            });
		}
	});

	$("button[vezife='qebulet']").click(function()
    {
        var baqlaBtn=$(this).next("button").next("button"),gid=sid;

		$.post("ajax/proid/work_tasks/gorushQebulEt.php", {gid:gid}, function(netice)
		{

            yazdir_notify(sid,"gorushler");
            netice = eval(netice);
            if(netice[0]!="sehf")
            {toastr['success']('Əməliyyat Uğurla Başa Çatdı')
					var status = netice[1];

					if(status=="0")
                    {
                        //td.eq(8).html('<button class="btn btn-xs btn-danger" type="button">$11testiqlenmeyib$</button>');
                    }
                    else
                    {
                        location.reload();
                    }
			//else if(status=="1")
		{
            location.reload();
            //td.eq(8).html('<button class="btn btn-xs btn-success" type="button">$11testiqlenib$</button>');
            //
            //var cedvelNovu = aa.parent("tbody").parent("table").attr("id");
            //cedvelNovu = cedvelNovu.substring(0,(cedvelNovu.length-1));
            //var yeniCedvel = cedvelNovu+"2";
            //var setirr = aa.html();
            //aa.fadeOut(500, function()
            //{
            //	$(this).remove();
            //	$("#"+yeniCedvel+" tbody").append("<tr gid='" + gid + "'>" + setirr + "</tr>");
            //	$("#"+yeniCedvel+" tbody tr:last").hide().fadeIn(400, function()
            //	{
            //		if(($("#"+yeniCedvel+" tbody tr").length-$("#"+yeniCedvel+" tbody tr td[colspan]").length)>0)
            //		{
            //			$("#"+yeniCedvel+" tbody tr td[colspan]").parent("tr").remove();
            //		}
            //	});
            //	dblclickTr();
            //});
        }
			baqlaBtn.click();
		}
            else
            {
                //$("#bosh_modal$MN$ div[vezife='error']").text(netice[1]);
            }
        });
	});


	$("button[vezife='imtina']").click(function()
    {
        var mn2=modal_yarat("İmtina et","<form class='form-horizontal form-bordered'><div class='form-body'><div class='form-group'><label class='col-md-4 control-label'>Səbəb:</label><div class='col-md-6'><textarea class='form-control' placeholder='Səbəbi qeyd edin..' maxlength='500' limit></textarea></div></div></div></form>","<button class='btn btn-danger tesdiqle'>İmtina et</button> <button class='btn default' data-dismiss='modal'>Bağla</button>","btn-danger","",true);
        $("#bosh_modal"+mn2+" textarea").textareaLimit();
        $("#bosh_modal"+mn2+" button.tesdiqle").unbind("click").click(function()
        {
            var sebeb = $("#bosh_modal"+mn2+" textarea").val().trim();
            if(sebeb=="")
            {
                $("#bosh_modal"+mn2+" textarea").css("border","1px dashed red");
            }
            else
            {
                modal_loading(1,mn2);
                $("#bosh_modal"+mn2+" button.tesdiqle").css("border","");
                $.post("ajax/proid/work_tasks/gorushImtinaEt.php", {gid:sid, sebeb:sebeb}, function(netice)
				{
                    modal_loading(0,mn2);
                    yazdir_notify(sid,"gorushler");
                    location.reload();
                    //if(module=='dashboard')
                    //{
                    //	$("tr[ne=gorushler]"+"tr[tid='" + $sid$ + "']").fadeOut(150,function(){$(this).remove();table_siralama()});
                    //	toastr['error']('$11gorushdenimtinaedildi$')
                    //}
                    //else
                    //{
                    //	toastr['error']('$11gorushdenimtinaedildi$')
                    //	location.reload();
                    //}
                    //$("#bosh_modal"+mn2).modal("hide");
                    //$("#bosh_modal$MN$").modal("hide");
                });
			}
        });
    });

</script>