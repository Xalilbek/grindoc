<?php


session_start();
include_once '../../class/class.functions.php';
$user = new User();

$userId = (int)$_SESSION['erpuserid'];

if(!$user->get_session())
{
    print "daxil_olmayib";
    exit;
}
$activeTenant=$user->getActiveTenantId();
if(isset($_POST['istiqamet_tipi'])&& is_string($_POST['istiqamet_tipi']))
{


    $istiqamet_tipi= $_POST['istiqamet_tipi'];
    $dependenceFromId= isset($_POST['dependenceFromId'])?$_POST['dependenceFromId']:0;


    $filter = array(
        "daxil_olan_senedler"=>array(
            "sened_novu"=>          array("id"=>" tip as [value] ", "text"=> " (CASE tip WHEN 1 THEN N'Hüquqi' WHEN 2 THEN N'Vətəndaş' Else  N'Qeyd olunmayıb' END) as [text] ", "from"=> "v_daxil_olan_senedler", "where"=>" WHERE tip<>3 ") ,
            "sened_tip"=>           array("id"=>" sened_tip as [value] ", "text"=> " (CASE sened_tip WHEN 1 THEN N'Məlumat üçün' WHEN 2 THEN N'İcra üçün' Else  N'Qeyd olunmayıb' END) as [text] ", "from"=> "v_daxil_olan_senedler", "where"=>" WHERE tip<>3 ") ,
            "haradan_daxil_olub"=>  array("id"=>" id as [value] ", "text"=> " name as [text] ", "from"=> "tb_prodoc_daxil_olma_menbeleri", "where"=>" WHERE deleted=0 ") ,
            "gonderen_teshkilat"=>  array("id"=>" id as [value] ", "text"=> " Adi as [text], (Select  id  from tb_prodoc_daxil_olma_menbeleri  WHERE [key] = 'qurum') as [has]  ",  "from"=> "tb_CustomersCompany", "where"=>" WHERE silinib=0 AND TenantId=".$activeTenant) ,
//            "status"=>              array("id"=>" distinct status as [value] ", "text"=> " (CASE status WHEN 1 THEN N'Açıq' WHEN 2 THEN N'Bağlı' Else  N'Ləğv olunan' END) as [text] ", "from"=> "v_daxil_olan_senedler", "where"=>" WHERE tip<>3 AND status is not null") ,
            "bolme"=>               array("id"=>" struktur_id as [value] ", "text"=> " struktur_bolmesi as [text] ", "from"=> "tb_Struktur", "where"=>" WHERE TenantId=".$activeTenant." AND silinib=0 ") ,
            "mektub_tipi"=>         array("id"=>" id as [value] ", "text"=> " ad as [text] ", "from"=> "tb_mektubun_tipleri", "where"=>" WHERE (select_type<>'movzu' OR select_type IS NULL)  AND silinib = 0  AND (parent_id = 0 OR parent_id IS NULL) AND TenantId=".$activeTenant) ,
            "rey_muellifi"=>        array("id"=>" user_id as [value] ", "text"=> " (Select Concat(Adi,' ',Soyadi) from tb_users where USERID=user_id) as [text], ( SELECT struktur_id from v_users where USERID=user_id) as [has] ", "from"=> "tb_prodoc_icrachi_shexsler", "where"=>" WHERE (sened_tip=1 OR sened_tip=2) AND icrachi_tip='rey_muellifi'") ,
            "icrachi"=>             array("id"=>" DISTINCT mesul_shexs as [value] ", "text"=> " (Select Concat(Adi,' ',Soyadi) from tb_users where USERID=mesul_shexs) as [text], ( SELECT struktur_id from v_users where USERID=mesul_shexs) as [has] ", "from"=> "v_derkenar", "where"=>" ") ,
        ),
        "daxili_senedler"=> array(
            "sened_novu"=>      array("id"=>" id as [value] ", "text"=> " name  as  [text] ", "from"=> "tb_prodoc_inner_document_type", "where" => " where  parent_id is null AND silinib = 0") ,
            "sened_tip"=>       array("id"=>" sened_tip as [value] ", "text"=> " (CASE sened_tip WHEN 1 THEN N'Məlumat üçün' WHEN 2 THEN N'İcra üçün' Else  N'Qeyd olunmayıb' END) as [text] ", "from"=> "v_daxil_olan_senedler", "where"=>" WHERE tip<>3 ") ,
            "alt_novu"=>        array("id"=>" DISTINCT doc_type.id as [value] ", "text"=> " doc_type.name  as  [text], doc_type.parent_id as [has] ", "from"=> "tb_prodoc_inner_document_type doc_type  LEFT JOIN tb_prodoc_alt_privilegiyalar alt_priv ON doc_type.extra_id = alt_priv.string_id LEFT JOIN tb_prodoc_role_privilegiyalar rollar ON rollar.alt_privilegiya_id= alt_priv.id LEFT JOIN TenantUserAuthentication authentication ON rollar.role_id=authentication.ProdocGroupId ", "where" => " where  doc_type.parent_id > 0 AND   doc_type.silinib = 0  AND authentication.TenantUserId=$userId AND rollar.privilegiya = 1") ,
            "rey_muellifi"=>    array("id"=>" user_id as [value] ", "text"=> " (Select Concat(Adi,' ',Soyadi) from tb_users where USERID=user_id) as [text] ,( SELECT struktur_id from v_users where USERID=user_id) as [has] ", "from"=> "tb_prodoc_icrachi_shexsler", "where"=>" WHERE  sened_tip=3 AND icrachi_tip='rey_muellifi' ") ,
            "icrachi"=>         array("id"=>" DISTINCT mesul_shexs as [value] ", "text"=> " (Select Concat(Adi,' ',Soyadi) from tb_users where USERID=mesul_shexs) as [text], ( SELECT struktur_id from v_users where USERID=mesul_shexs) as [has] ", "from"=> "v_derkenar", "where"=>" ") ,
//            "status"=>          array("id"=>" distinct status as [value] ", "text"=> " (CASE status WHEN 1 THEN N'Açıq' WHEN 2 THEN N'Bağlı' Else  N'Ləğv olunan' END) as [text] ", "from"=> "v_daxil_olan_senedler", "where"=>" WHERE tip<>3 AND status is not null") ,
            "bolme"=>           array("id"=>" struktur_id as [value] ", "text"=> " struktur_bolmesi as [text] ", "from"=> "tb_Struktur", "where"=>" WHERE TenantId=".$activeTenant." AND silinib=0 ") ,

        ),
        "xaric_olan_senedler"=>array(
            "sened_novu"=>      array("id"=>" id as [value] ", "text"=> " ad as [text] ", "from"=> "tb_prodoc_muraciet_tip", "where"=>" where silinib=0 ") ,
            "bolme"=>           array("id"=>" struktur_id as [value] ", "text"=> " struktur_bolmesi as [text] ", "from"=> "tb_Struktur", "where"=>" WHERE TenantId=".$activeTenant." AND silinib=0 ") ,
            "icrachi"=>         array("id"=>" created_by as [value] ", "text"=> " ( SELECT Concat ( Adi, ' ', Soyadi ) FROM tb_users WHERE USERID = created_by )  as [text], ( SELECT struktur_id from v_users where USERID=created_by) as [has] ", "from"=> "v_chixan_senedler", "where"=>" ") ,
            "teyinat"=>         getProjectName()===TS ? array("id"=>" [value] ", "text"=> "  [text] ", "from"=> "( SELECT 4 as [value], N'Vətəndaş' as [text] UNION SELECT 3 as [value], N'Aidiyyatı orqan' as [text]  ) aa", "where"=>" ORDER BY [value] desc  ") : array("id"=>" [value] ", "text"=> "  [text] ", "from"=> "( SELECT 4 as [value], N'Vətəndaş' as [text] UNION SELECT 3 as [value], N'Aidiyyatı orqan' as [text] UNION SELECT 5 as [value], N'Tabeli qurum' as [text]  ) aa", "where"=>" ORDER BY [value] desc  ")  ,
            "hara_gonderilib"=> array("id"=>" [value] ", "text"=> "  [text],  [has] ", "from"=> "(SELECT id as [value], [name] as [text], 1 as has FROM  tb_prodoc_qurumlar WHERE deleted='0' 
                                    UNION SELECT  customer.id as [value], Adi AS [text], 3 as has FROM tb_CustomersCompany customer LEFT JOIN tb_Company_type type ON type.id  = customer.Tipi WHERE customer.silinib=0 AND customer.TenantId = ".DB::quote($user->getActiveTenantId())." AND type.silinib = 0 ) dd", "where"=>"   ") ,

        )
    );

    $sql="";
    isset($filter['daxil_olan_senedler'][$istiqamet_tipi])?
        $sql.= "Select ".$filter['daxil_olan_senedler'][$istiqamet_tipi]['id']." , 'daxil_olan_senedler' as [key], '".$istiqamet_tipi."' as istiqamet_tipi,  ".$filter['daxil_olan_senedler'][$istiqamet_tipi]['text']."  from ".$filter['daxil_olan_senedler'][$istiqamet_tipi]['from']." ".$filter['daxil_olan_senedler'][$istiqamet_tipi]['where']: " ";
    isset($filter['daxil_olan_senedler'][$istiqamet_tipi])&&(isset($filter['daxili_senedler'][$istiqamet_tipi])||isset($filter['xaric_olan_senedler'][$istiqamet_tipi]))?
        $sql.=" UNION ": "";
    isset($filter['daxili_senedler'][$istiqamet_tipi])?
        $sql.="Select ".$filter['daxili_senedler'][$istiqamet_tipi]['id']." , 'daxili_senedler' as [key],  '".$istiqamet_tipi."' as istiqamet_tipi,  ".$filter['daxili_senedler'][$istiqamet_tipi]['text']." 
        from ".$filter['daxili_senedler'][$istiqamet_tipi]['from']." ".$filter['daxili_senedler'][$istiqamet_tipi]['where']:" ";
    ((isset($filter['daxil_olan_senedler'][$istiqamet_tipi])||isset($filter['daxili_senedler'][$istiqamet_tipi]))&&isset($filter['xaric_olan_senedler'][$istiqamet_tipi]))?
        $sql.=" UNION ": " ";
    isset($filter['xaric_olan_senedler'][$istiqamet_tipi])?
        $sql.="Select ".$filter['xaric_olan_senedler'][$istiqamet_tipi]['id']." ,  'xaric_olan_senedler' as [key], '".$istiqamet_tipi."' as istiqamet_tipi , ".$filter['xaric_olan_senedler'][$istiqamet_tipi]['text']." from ".$filter['xaric_olan_senedler'][$istiqamet_tipi]['from']." ".$filter['xaric_olan_senedler'][$istiqamet_tipi]['where']: " ";


    if (isset($_POST['keys'])){
        $keys = $_POST['keys'];
        if (count($keys)>0&& $keys!=""){
            $filteredForReport="";

            $filteredForReport=" AND (";
            foreach ($keys as $key => $value){
                $key == 0 ? $filteredForReport.="  general.[key] = '".$value."'":
                    $filteredForReport.=" OR  general.[key] = '".$value."'";
            }
            $filteredForReport.=")";
            $sql= " SELECT * FROM (".$sql.")  as general WHERE 1=1 ".$filteredForReport;
        }else{
            $sql= " SELECT * FROM (".$sql.")  as general WHERE 1<>1 ";
        }

    }

    if ($istiqamet_tipi=="status"){
        $data=  array(
            array("value"=>"1","key"=>"daxil_olan_senedler","istiqamet_tipi"=>"status","text"=>"Açıq"),
            array("value"=>"2","key"=>"daxil_olan_senedler","istiqamet_tipi"=>"status","text"=>"Bağlı"),
            array("value"=>"5","key"=>"daxil_olan_senedler","istiqamet_tipi"=>"status","text"=>"Ləğv olunan"),
            array("value"=>"1","key"=>"daxili_senedler","istiqamet_tipi"=>"status","text"=>"Açıq"),
            array("value"=>"2","key"=>"daxili_senedler","istiqamet_tipi"=>"status","text"=>"Bağlı"),
            array("value"=>"5","key"=>"daxili_senedler","istiqamet_tipi"=>"status","text"=>"Ləğv olunan"),
            array("value"=>"4","key"=>"xaric_olan_senedler","istiqamet_tipi"=>"status","text"=>"Ləğv olunan"),
            array("value"=>"1","key"=>"xaric_olan_senedler","istiqamet_tipi"=>"status","text"=>"Göndərilib"),
            array("value"=>"0","key"=>"xaric_olan_senedler","istiqamet_tipi"=>"status","text"=>"Göndərilməyib"),
        );
    }
    elseif ($istiqamet_tipi=="power_of_attorney"){
        $data=  array(
            array("value"=>"emeliyyat_aparilan","key"=>"daxil_olan_senedler","istiqamet_tipi"=>"power_of_attorney","text"=>"Əməliyyat aparılan"),
            array("value"=>"icra_olunan","key"=>"daxil_olan_senedler","istiqamet_tipi"=>"power_of_attorney","text"=>"İcra olunan"),
            array("value"=>"emeliyyat_aparilan","key"=>"daxili_senedler","istiqamet_tipi"=>"power_of_attorney","text"=>"Əməliyyat aparılan"),
            array("value"=>"icra_olunan","key"=>"daxili_senedler","istiqamet_tipi"=>"power_of_attorney","text"=>"İcra olunan"),
            array("value"=>"emeliyyat_aparilan","key"=>"xaric_olan_senedler","istiqamet_tipi"=>"power_of_attorney","text"=>"Əməliyyat aparılan"),
            array("value"=>"icra_olunan","key"=>"xaric_olan_senedler","istiqamet_tipi"=>"power_of_attorney","text"=>"İcra olunan"),
        );
    }

    else{
        $data=DB::fetchAll($sql,true, PDO::FETCH_ASSOC);
    }

    $netice=[];
    $netice['results']=[];
    if(isset($_POST['report'])){
        foreach ($data as $value){
            $netice['results'][] = array (
                "id" => (int)$value['value'] ,
                "text" => htmlspecialchars( $value['text'],ENT_NOQUOTES),
                "key"=>$value['key']
            );
        }
    }else{
        $netice=$data;
    }

    print json_encode(array("status"=>"hazir","data"=>$netice));
}