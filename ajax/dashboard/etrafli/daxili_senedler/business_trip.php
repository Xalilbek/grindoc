<?php
if(isset($parametrler) && isset($parametrler['sid']) && is_numeric($parametrler['sid']) && $parametrler['sid']>0)
{
    $userId = (int)$_SESSION['erpuserid'];
    $sid = (int)$parametrler['sid'];

    $BusinessTripInfoInfo = pdof()->query("SELECT *,(SELECT user_ad FROM v_user_adlar WHERE USERID=created_user) as user_name FROM tb_prodoc_business_trip tb1 WHERE tb1.id='$sid' ")->fetch();
    if($BusinessTripInfoInfo==false)
    {
        print json_encode(array("status"=>"sehv"));
        exit();
    }

    $attachment = "";
    foreach(explode(",",$BusinessTripInfoInfo['attachment']) AS $sened)
    {
        if(trim($sened)!="")
        {
            $attachment .= '<div style="margin-bottom: 2px;"><a class="btn btn-default btn-xs" style="max-width: 300px; overflow: hidden;" href="uploads/prodoc/business_trip/'.htmlspecialchars(basename($sened)).'" target="_blank"><i class="icon-doc"></i> '.htmlspecialchars((substr(basename($sened),strpos(basename($sened),"_")+1))).'</a> <a class="btn btn-xs btn-default" href="uploads/prodoc/business_trip/'.htmlspecialchars(basename($sened)).'" target="_blank" download><i class="fa fa-download"></i></a></div>';
        }
    }

    $approveBtn = ((int)$BusinessTripInfoInfo['status']!=3 && (int)$BusinessTripInfoInfo['status']!=1 && (pdof()->query("SELECT * FROM tb_prodoc_business_trip_tesdiqleme WHERE user_id='$userId' AND status='0' AND trip_id='".(int)$sid."' AND qrup=(SELECT MIN(qrup) FROM tb_prodoc_business_trip_tesdiqleme WHERE status='0' AND trip_id='".(int)$sid."' )")->fetch())!=false) ? true : false;
    $cancelBtn = ((int)$BusinessTripInfoInfo['status']!=3 && (in_array($userId,explode(",",$BusinessTripInfoInfo['tesdiqleme_geden_userler'])) || (pdof()->query("SELECT * FROM tb_proid_business_trip_users WHERE user_id='$userId' AND eid='".(int)$BusinessTripInfoInfo["business_trip_id"]."' AND order_id='$sid'")->fetch()) ))? true : false;

    $trioinfo = pdof()->query("SELECT TOP 20 CONCAT(N'Ezamiyyət №',tb1.id) as name,tb1.*,tb3.ad AS olke_ad,tb4.struktur_bolmesi as bolme_ad,tb5.Adi AS shirket_ad,tb2.ad AS sheher_ad FROM tb_ezamiyyetler tb1 LEFT JOIN tb_Struktur tb4 ON tb4.struktur_id=tb1.bolme LEFT JOIN tb_istehsalchilar tb5 ON tb5.id=tb1.shirket LEFT JOIN tb_general_cities tb2 ON tb2.id=tb1.sheher LEFT JOIN tb_general_countries tb3 ON tb3.id=tb1.olke WHERE tb1.id='".(int)$BusinessTripInfoInfo["business_trip_id"]."' ")->fetch();

    $ezaInfo = pdof()->query("SELECT * FROM tb_ezamiyyetler tb1 WHERE tb1.id='".(int)$BusinessTripInfoInfo["business_trip_id"]."' ")->fetch();
    require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument.php';
    $intDoc = new InternalDocument($BusinessTripInfoInfo['document_id']);
    $infoMass = array(
        "detailed_information" => $intDoc->getDetailedInformationHTML(),
        "sid"=>$sid,
        "from"=>(int)$ezaInfo["from"],
        "order_number"=>htmlspecialchars($BusinessTripInfoInfo["order_number"]),
        "business_trip_date"=>"(".sprintf(date("d-m-Y",strtotime($trioinfo['start_date'])).")-(".date("d-m-Y",strtotime($trioinfo['end_date'])).") [".(floor((strtotime($trioinfo['end_date'])-strtotime($trioinfo['start_date']))/24/60/60)+1)." %s]", dil::soz("9910gun")),
        "business_trip_des"=>htmlspecialchars(((int)$trioinfo['shirket_daxili']!=0?$trioinfo['bolme_ad']:($trioinfo['olke_ad'].", ".$trioinfo['sheher_ad'].(trim($trioinfo['shirket_ad'])==""?"":", ".$trioinfo['shirket_ad'])))),
        "order_date"=>date("d-m-Y",strtotime($BusinessTripInfoInfo["order_date"])),
        "business_trip_id"=>(int)$BusinessTripInfoInfo["business_trip_id"],
        "approveBtn"=>(int)$approveBtn,
        "cancelBtn"=>(int)$cancelBtn,
        "who_added"=>htmlspecialchars($BusinessTripInfoInfo["user_name"]),
        "added_date"=>date("d-m-Y",strtotime($BusinessTripInfoInfo["date"])),
        "editBtn"=>($userId==(int)$BusinessTripInfoInfo["created_user"])?true:false,
        "fayl"=>$attachment==""?"-":$attachment,
        "base"=>htmlspecialchars(trim($BusinessTripInfoInfo["base"])==""?"-":$BusinessTripInfoInfo["base"]),

        "47mezuniyyet_emri__senedin_tarixchesi"=>dil::soz("47mezuniyyet_emri__senedin_tarixchesi"),
        "47ezamiyyet_emri__duzelish_et"=>dil::soz("47ezamiyyet_emri__duzelish_et"),
        "47sebebi_daxil_edin"=>dil::soz("47sebebi_daxil_edin"),
        "47elave_olunma_tarixi"=>dil::soz("47elave_olunma_tarixi"),
        "47ezamiyyet_haqqinda"=>dil::soz("47ezamiyyet_haqqinda"),
        "47business_trip"=>dil::soz("47business_trip"),
        "47order_number"=>dil::soz("47order_number"),
        "47destination"=>dil::soz("47destination"),
        "47order_date"=>dil::soz("47order_date"),
        "47duzelish_et"=>dil::soz("47duzelish_et"),
        "47elave_eden"=>dil::soz("47elave_eden"),
        "47ezamiyyet"=>dil::soz("47ezamiyyet"),
        "47imtina_et"=>dil::soz("47imtina_et"),
        "47tesdiqle"=>dil::soz("47tesdiqle"),
        "47tarixce"=>dil::soz("47tarixce"),
        "47imtina"=>dil::soz("47imtina"),
        "47qoshma"=>dil::soz("47qoshma"),
        "47bagla"=>dil::soz("47bagla"),
        "47sebeb"=>dil::soz("47sebeb"),
        "47date"=>dil::soz("47date"),
        "47base"=>dil::soz("47base"),
    );

    print json_encode(array("status"=>"success","html"=>($user->template_yukle('daxili_senedler/business_trip',$infoMass, 'prodoc'))));

}