<?php
if(isset($parametrler) && isset($parametrler['sid']) && is_numeric($parametrler['sid']) && $parametrler['sid']>0)
{

    $userId = (int)$_SESSION['erpuserid'];
    $sid = (int)$parametrler['sid'];
    $VocInfo = pdof()->query("SELECT *,(SELECT user_ad FROM v_user_adlar s WHERE s.USERID=tb1.created_user) as user_name,(SELECT user_ad FROM v_user_adlar s WHERE s.USERID=tb1.employe) as employee FROM tb_prodoc_vacation_orders tb1 WHERE tb1.id='$sid' ")->fetch();

    $wystr = "";
    $wyearsarr = @json_decode($VocInfo["work_years"]);
    if(is_array($wyearsarr))
    {
        foreach ($wyearsarr as $value) {
            if(isset($value[0]) && is_numeric($value[0]) && isset($value[1]) && is_numeric($value[1])){
                $workYearsq = pdof()->query("SELECT CONCAT(DATEPART(YEAR, tarix1), ' - ', DATEPART(YEAR, tarix2)) AS years FROM tb_mezuniyyet_gunleri_illik WHERE id='".(int)$value[0]."' ")->fetch();
            }
            $wystr .= sprintf($workYearsq[0]." ( ".$value[1]." %s )<br>", dil::soz("9907gun"));
        }
    }
    $attachment = "";
    foreach(explode(",",$VocInfo['attachment']) AS $sened)
    {
        if(trim($sened)!="")
        {
            $attachment .= '<div style="margin-bottom: 2px;"><a class="btn btn-default btn-xs" style="max-width: 300px; overflow: hidden;" href="uploads/prodoc/vacation_order/'.htmlspecialchars(basename($sened)).'" target="_blank"><i class="icon-doc"></i> '.htmlspecialchars((substr(basename($sened),strpos(basename($sened),"_")+1))).'</a> <a class="btn btn-xs btn-default" href="uploads/prodoc/vacation_order/'.htmlspecialchars(basename($sened)).'" target="_blank" download><i class="fa fa-download"></i></a></div>';
        }
    }

    $approveBtn = ((int)$VocInfo['status']!=3 && (int)$VocInfo['status']!=1 && pdof()->query("SELECT * FROM tb_prodoc_vacation_orders_tesdiqleme WHERE user_id='$userId' AND status='0' AND qrup=(SELECT MIN(qrup) FROM tb_prodoc_vacation_orders_tesdiqleme WHERE status='0')")->fetch()) ? true : false;
    $cancelBtn = ((int)$VocInfo['status']!==3 && (in_array($userId,explode(",",$VocInfo['tesdiqleme_geden_userler'])) || $userId==$VocInfo['employe']))? true : false;
    require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument.php';
    $intDoc = new InternalDocument($VocInfo['document_id']);
    $infoMass = array(

        "sid"=>$sid,
        "order_number"=>htmlspecialchars($VocInfo["order_number"]),
        "detailed_information" => $intDoc->getDetailedInformationHTML(),
        "order_date"=>date("d-m-Y",strtotime($VocInfo["order_date"])),
        "employee_id"=>(int)$VocInfo["employe"],
        "employee"=>htmlspecialchars($VocInfo["employee"]),
        "vacation"=>(int)$VocInfo["vacation_id"],
        "approveBtn"=>(int)$approveBtn,
        "cancelBtn"=>(int)$cancelBtn,
        "editBtn"=>($userId==(int)$VocInfo["created_user"])?true:false,
        "fayl"=>$attachment,
        "work_years_or_reason_title"=>htmlspecialchars($VocInfo['period_type']==1? dil::soz("9907workyears") : dil::soz("9907reason")),
        "work_years_or_reason"=>($VocInfo['period_type']==1?$wystr:htmlspecialchars($VocInfo["reason"])),
        "who_added"=>htmlspecialchars($VocInfo["user_name"]),
        "added_date"=>date("d-m-Y",strtotime($VocInfo["date"])),
        "base"=>htmlspecialchars($VocInfo["base"]),

        "47mezuniyyet_emri__senedin_tarixchesi"=>dil::soz("47mezuniyyet_emri__senedin_tarixchesi"),
        "47mezuniyyet_emri__duzelish_et"=>dil::soz("47mezuniyyet_emri__duzelish_et"),
        "47sebebi_daxil_edin"=>dil::soz("47sebebi_daxil_edin"),
        "47elave_olunma_tarixi"=>dil::soz("47elave_olunma_tarixi"),
        "47order_number"=>dil::soz("47order_number"),
        "47order_date"=>dil::soz("47order_date"),
        "47duzelish_et"=>dil::soz("47duzelish_et"),
        "47mezuniyyet"=>dil::soz("47mezuniyyet"),
        "47elave_eden"=>dil::soz("47elave_eden"),
        "47imtina_et"=>dil::soz("47imtina_et"),
        "47tesdiqle"=>dil::soz("47tesdiqle"),
        "47vacation"=>dil::soz("47vacation"),
        "47employee"=>dil::soz("47employee"),
        "47tarixce"=>dil::soz("47tarixce"),
        "47imtina"=>dil::soz("47imtina"),
        "47qoshma"=>dil::soz("47qoshma"),
        "47bagla"=>dil::soz("47bagla"),
        "47sebeb"=>dil::soz("47sebeb"),
        "47bagla"=>dil::soz("47bagla"),
        "47base"=>dil::soz("47base"),
    );

    print json_encode(array("status"=>"success","html"=>($user->template_yukle('daxili_senedler/vacation_order',$infoMass, 'prodoc'))));

}