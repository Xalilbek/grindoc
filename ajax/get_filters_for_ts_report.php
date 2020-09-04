<?php


session_start();
include_once '../../class/class.functions.php';
$user = new User();



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
//SELECT TOP 100 t1.id, t1.name FROM tb_prodoc_nazalogiya t1 WHERE  (CAST(t1.name AS NVARCHAR) COLLATE Azeri_Cyrillic_100_CI_AI LIKE N'%{$soz}%') AND t1.deleted = 0

    $filter = array(
        "daxil_olan_senedler"=>array(
            "sened_tip"=>           array("id"=>" sened_tip as [value] ", "text"=> " (CASE sened_tip WHEN 1 THEN N'Məlumat üçün' WHEN 2 THEN N'İcra üçün' Else  N'Qeyd olunmayıb' END) as [text] ", "from"=> "v_daxil_olan_senedler", "where"=>" WHERE tip<>3 ") ,
            "haradan_daxil_olub"=>  array("id"=>" id as [value] ", "text"=> " name as [text] ", "from"=> "tb_prodoc_daxil_olma_menbeleri", "where"=>" WHERE deleted=0 ") ,
            "gonderen_teshkilat"=>  array("id"=>" tb_CustomersCompany.id as [value] ", "text"=> " Adi as [text], (Select  id  from tb_prodoc_daxil_olma_menbeleri  WHERE [key] = 'qurum') as [has]  ",
                "from"=> "tb_CustomersCompany LEFT JOIN tb_Company_type AS ct ON tb_CustomersCompany.Tipi = ct.id", "where"=>" WHERE tb_CustomersCompany.silinib=0 AND ct.silinib=0") ,
//            "status"=>              array("id"=>" distinct status as [value] ", "text"=> " (CASE status WHEN 1 THEN N'Açıq' WHEN 2 THEN N'Bağlı' Else  N'Ləğv olunan' END) as [text] ", "from"=> "v_daxil_olan_senedler", "where"=>" WHERE tip<>3 AND status is not null") ,
            "bolme"=>               array("id"=>" struktur_id as [value] ", "text"=> " struktur_bolmesi as [text] ", "from"=> "tb_Struktur", "where"=>" WHERE TenantId=".$activeTenant." AND silinib=0 ") ,
            "muraciet_tipi"=>         array("id"=>" id as [value] ", "text"=> " CONCAT(ad, N'(Hüquqi)') as [text] ", "from"=> "tb_mektubun_tipleri", "where"=>" WHERE (select_type<>'movzu')  AND silinib = 0 AND huquqi=1 AND (parent_id = 0 OR parent_id IS NULL) AND TenantId=".$activeTenant) ,
            "rey_muellifi"=>        array("id"=>" user_id as [value] ", "text"=> " (Select Concat(Adi,' ',Soyadi) from tb_users where USERID=user_id) as [text], ( SELECT struktur_id from v_users where USERID=user_id) as [has] ", "from"=> "tb_prodoc_icrachi_shexsler", "where"=>" WHERE sened_tip=1 OR sened_tip=2") ,
            "icrachi"=>             array("id"=>" DISTINCT mesul_shexs as [value] ", "text"=> " (Select Concat(Adi,' ',Soyadi) from tb_users where USERID=mesul_shexs) as [text], ( SELECT struktur_id from v_users where USERID=mesul_shexs) as [has] ", "from"=> "v_derkenar", "where"=>" WHERE mesul_shexs in (SELECT USERID FROM v_users WHERE TenantId=".$activeTenant.")" ) ,
            "document_result"=>             array("id"=>" id as [value] ", "text"=> " name as [text] ", "from"=> "tb_prodoc_neticeler", "where"=>" WHERE deleted=0 AND TenantId=".$activeTenant." ") ,
            "incoming_ways"=>             array("id"=>" id as [value] ", "text"=> " name as [text] ", "from"=> "tb_daxil_olma_yollari", "where"=>" WHERE deleted=0 AND TenantId=".$activeTenant." ") ,
//            "qisa_mezmun_id"=>             array("id"=>" id as [value] ", "text"=> " name as [text] ", "from"=> "tb_prodoc_nazalogiya", "where"=>" WHERE deleted = 0 AND TenantId=".$activeTenant." ") ,
        ),
        "vetendash"=>array(
            "sened_tip"=>           array("id"=>" sened_tip as [value] ", "text"=> " (CASE sened_tip WHEN 1 THEN N'Məlumat üçün' WHEN 2 THEN N'İcra üçün' Else  N'Qeyd olunmayıb' END) as [text] ", "from"=> "v_daxil_olan_senedler", "where"=>" WHERE tip<>3 ") ,
            "haradan_daxil_olub"=>  array("id"=>" id as [value] ", "text"=> " name as [text] ", "from"=> "tb_prodoc_daxil_olma_menbeleri", "where"=>" WHERE deleted=0 ") ,
            "gonderen_teshkilat"=>  array("id"=>" tb_CustomersCompany.id as [value] ", "text"=> " Adi as [text], (Select  id  from tb_prodoc_daxil_olma_menbeleri  WHERE [key] = 'qurum') as [has]  ",
                "from"=> "tb_CustomersCompany LEFT JOIN tb_Company_type AS ct ON tb_CustomersCompany.Tipi = ct.id", "where"=>" WHERE tb_CustomersCompany.silinib=0 AND ct.silinib=0") ,
//            "status"=>              array("id"=>" distinct status as [value] ", "text"=> " (CASE status WHEN 1 THEN N'Açıq' WHEN 2 THEN N'Bağlı' Else  N'Ləğv olunan' END) as [text] ", "from"=> "v_daxil_olan_senedler", "where"=>" WHERE tip<>3 AND status is not null") ,
            "bolme"=>               array("id"=>" struktur_id as [value] ", "text"=> " struktur_bolmesi as [text] ", "from"=> "tb_Struktur", "where"=>" WHERE TenantId=".$activeTenant." AND silinib=0 ") ,
            "muraciet_tipi"=>         array("id"=>" id as [value] ", "text"=> " CONCAT(ad, N'(Vətəndaş)') as [text] ", "from"=> "tb_mektubun_tipleri", "where"=>" WHERE (select_type<>'movzu' )  AND silinib = 0  AND (parent_id = 0 OR parent_id IS NULL) AND fiziki=1 AND TenantId=".$activeTenant) ,
            "rey_muellifi"=>        array("id"=>" user_id as [value] ", "text"=> " (Select Concat(Adi,' ',Soyadi) from tb_users where USERID=user_id) as [text], ( SELECT struktur_id from v_users where USERID=user_id) as [has] ", "from"=> "tb_prodoc_icrachi_shexsler", "where"=>" WHERE sened_tip=1 OR sened_tip=2") ,
            "icrachi"=>             array("id"=>" DISTINCT mesul_shexs as [value] ", "text"=> " (Select Concat(Adi,' ',Soyadi) from tb_users where USERID=mesul_shexs) as [text], ( SELECT struktur_id from v_users where USERID=mesul_shexs) as [has] ", "from"=> "v_derkenar", "where"=>" WHERE mesul_shexs in (SELECT USERID FROM v_users WHERE TenantId=".$activeTenant.")") ,
//            "document_result"=>             array("id"=>" id as [value] ", "text"=> " name as [text] ", "from"=> "tb_prodoc_neticeler", "where"=>" WHERE deleted=0 AND TenantId=".$activeTenant." "),
            "incoming_ways"=>             array("id"=>" id as [value] ", "text"=> " name as [text] ", "from"=> "tb_daxil_olma_yollari", "where"=>" WHERE deleted=0 AND TenantId=".$activeTenant." ") ,
//            "qisa_mezmun_id"=>             array("id"=>" id as [value] ", "text"=> " name as [text] ", "from"=> "tb_prodoc_nazalogiya", "where"=>" WHERE deleted = 0 AND TenantId=".$activeTenant." ") ,

        ),
        "daxili_senedler"=> array(
            "sened_novu"=>      array("id"=>" id as [value] ", "text"=> " name  as  [text] ", "from"=> "tb_prodoc_inner_document_type", "where" => " where  parent_id is null AND silinib = 0") ,
            "sened_tip"=>       array("id"=>" sened_tip as [value] ", "text"=> " (CASE sened_tip WHEN 1 THEN N'Məlumat üçün' WHEN 2 THEN N'İcra üçün' Else  N'Qeyd olunmayıb' END) as [text] ", "from"=> "v_daxil_olan_senedler", "where"=>" WHERE tip<>3 ") ,
            "alt_novu"=>        array("id"=>" id as [value] ", "text"=> " name  as  [text], parent_id as [has] ", "from"=> "tb_prodoc_inner_document_type", "where" => " where  parent_id > 0") ,
            "rey_muellifi"=>    array("id"=>" user_id as [value] ", "text"=> " (Select Concat(Adi,' ',Soyadi) from tb_users where USERID=user_id) as [text] ,( SELECT struktur_id from v_users where USERID=user_id) as [has] ", "from"=> "tb_prodoc_icrachi_shexsler", "where"=>" WHERE  sened_tip=3") ,
            "icrachi"=>         array("id"=>" DISTINCT mesul_shexs as [value] ", "text"=> " (Select Concat(Adi,' ',Soyadi) from tb_users where USERID=mesul_shexs) as [text], ( SELECT struktur_id from v_users where USERID=mesul_shexs) as [has] ", "from"=> "v_derkenar", "where"=>" WHERE mesul_shexs in (SELECT USERID FROM v_users WHERE TenantId=".$activeTenant.")") ,
//            "status"=>          array("id"=>" distinct status as [value] ", "text"=> " (CASE status WHEN 1 THEN N'Açıq' WHEN 2 THEN N'Bağlı' Else  N'Ləğv olunan' END) as [text] ", "from"=> "v_daxil_olan_senedler", "where"=>" WHERE tip<>3 AND status is not null") ,
            "bolme"=>           array("id"=>" struktur_id as [value] ", "text"=> " struktur_bolmesi as [text] ", "from"=> "tb_Struktur", "where"=>" WHERE TenantId=".$activeTenant." AND silinib=0 ") ,
//            "document_result"=>             array("id"=>" id as [value] ", "text"=> " name as [text] ", "from"=> "tb_prodoc_neticeler", "where"=>" WHERE deleted=0 AND TenantId=".$activeTenant." "),
//         "qisa_mezmun_id"=>             array("id"=>" id as [value] ", "text"=> " name as [text] ", "from"=> "tb_prodoc_nazalogiya", "where"=>" WHERE deleted = 0 AND TenantId=".$activeTenant." ") ,

        ),
        "xaric_olan_senedler"=>array(
//            "sened_novu"=>      array("id"=>" id as [value] ", "text"=> " ad as [text] ", "from"=> "tb_prodoc_muraciet_tip", "where"=>" where silinib=0 ") ,
            "bolme"=>           array("id"=>" struktur_id as [value] ", "text"=> " struktur_bolmesi as [text] ", "from"=> "tb_Struktur", "where"=>" WHERE TenantId=".$activeTenant." AND silinib=0 ") ,
            "icrachi"=>         array("id"=>" created_by as [value] ", "text"=> " ( SELECT Concat ( Adi, ' ', Soyadi ) FROM tb_users WHERE USERID = created_by )  as [text], ( SELECT struktur_id from v_users where USERID=created_by) as [has] ", "from"=> "v_chixan_senedler", "where"=>" WHERE created_by in (SELECT USERID FROM v_users WHERE TenantId=".$activeTenant.")" ) ,
            "teyinat"=>         array("id"=>" [value] ", "text"=> "  [text] ", "from"=> "( SELECT 4 as [value], N'Vətəndaş' as [text] UNION SELECT 3 as [value], N'Aidiyyatı orqan' as [text] UNION SELECT 5 as [value], N'Tabeli qurum' as [text]  ) aa", "where"=>" ORDER BY [value] desc  ") ,
            "muraciet_tipi"=>   array("id"=>" id as [value] ", "text"=> " ad as [text] ", "from"=> "tb_prodoc_muraciet_tip", "where"=>" WHERE  silinib=0 AND ( serbest<>0 OR emeliyyat<>0 )") ,
            "haradan_daxil_olub"=> array("id"=>" [value] ", "text"=> "  [text] ", "from"=> "(SELECT id as [value], [name] as [text] FROM  tb_prodoc_qurumlar WHERE deleted='0' AND TenantId=".$activeTenant." UNION SELECT  id as [value], Adi AS [text] FROM tb_CustomersCompany where silinib=0 AND TenantId=".$activeTenant." ) dd", "where"=>"   ") ,
//             "qisa_mezmun_id"=>             array("id"=>" id as [value] ", "text"=> " name as [text] ", "from"=> "tb_prodoc_nazalogiya", "where"=>" WHERE deleted = 0 AND TenantId=".$activeTenant." ") ,
        )
    );




    $sql="";
    isset($filter['daxil_olan_senedler'][$istiqamet_tipi])||isset($filter['vetendash'][$istiqamet_tipi])?
        $sql.= "Select ".$filter['daxil_olan_senedler'][$istiqamet_tipi]['id']." , 'daxil_olan_senedler' as [key], '".$istiqamet_tipi."' as istiqamet_tipi,  ".$filter['daxil_olan_senedler'][$istiqamet_tipi]['text']."  from ".$filter['daxil_olan_senedler'][$istiqamet_tipi]['from']." ".$filter['daxil_olan_senedler'][$istiqamet_tipi]['where']: " ";
    isset($filter['daxil_olan_senedler'][$istiqamet_tipi])&&(isset($filter['daxili_senedler'][$istiqamet_tipi]))?
        $sql.=" UNION ": "";
    isset($filter['daxili_senedler'][$istiqamet_tipi])?
        $sql.="Select ".$filter['daxili_senedler'][$istiqamet_tipi]['id']." , 'daxili_senedler' as [key],  '".$istiqamet_tipi."' as istiqamet_tipi,  ".$filter['daxili_senedler'][$istiqamet_tipi]['text']." 
        from ".$filter['daxili_senedler'][$istiqamet_tipi]['from']." ".$filter['daxili_senedler'][$istiqamet_tipi]['where']:" ";
    ((isset($filter['daxil_olan_senedler'][$istiqamet_tipi])||isset($filter['daxili_senedler'][$istiqamet_tipi]))&&isset($filter['xaric_olan_senedler'][$istiqamet_tipi]))?
        $sql.=" UNION ": " ";
    isset($filter['xaric_olan_senedler'][$istiqamet_tipi])?
        $sql.="Select ".$filter['xaric_olan_senedler'][$istiqamet_tipi]['id']." ,  'xaric_olan_senedler' as [key], '".$istiqamet_tipi."' as istiqamet_tipi , ".$filter['xaric_olan_senedler'][$istiqamet_tipi]['text']." from ".$filter['xaric_olan_senedler'][$istiqamet_tipi]['from']." ".$filter['xaric_olan_senedler'][$istiqamet_tipi]['where']: " ";

    if (isset($_POST['keys'])) {
        $sql = filterle($_POST['keys'],$sql);
    }




    if ($istiqamet_tipi=="status"){
        $data=  array(
            array("value"=>"1","key"=>"daxil_olan_senedler","istiqamet_tipi"=>"status","text"=>"Açıq"),
            array("value"=>"2","key"=>"daxil_olan_senedler","istiqamet_tipi"=>"status","text"=>"Bağlı"),
            array("value"=>"5","key"=>"xaric_olan_senedler","istiqamet_tipi"=>"status","text"=>"Ləğv olunan"),
            array("value"=>"1","key"=>"xaric_olan_senedler","istiqamet_tipi"=>"status","text"=>"Göndərilib"),
            array("value"=>"0","key"=>"xaric_olan_senedler","istiqamet_tipi"=>"status","text"=>"Göndərilməyib"),
        );
    }
    else if ($istiqamet_tipi=="tekrar_eyni"){
        $data=  array(

            array("value"=>"1","key"=>"vetendash","istiqamet_tipi"=>"tekrar_eyni","text"=>"Təkrar"),
            array("value"=>"2","key"=>"vetendash","istiqamet_tipi"=>"tekrar_eyni","text"=>"Eyni"),
        );
    }
    else if ($istiqamet_tipi=="mektub_nezaretdedir"){
        $data=  array(

            array("value"=>"1","istiqamet_tipi"=>"mektub_nezaretdedir","text"=>"Nəzarətdədir"),
            array("value"=>"0","istiqamet_tipi"=>"mektub_nezaretdedir","text"=>"Nəzarətdə deyil"),
        );
    }
    else if ($istiqamet_tipi=="qisa_mezmun_id"){

        $data=DB::fetchAll("SELECT id as [value], name as [text] FROM tb_prodoc_nazalogiya WHERE deleted = 0 AND TenantId=".$activeTenant);
    }
    else if ($istiqamet_tipi=="document_result"){


        $data=DB::fetchAll("SELECT id as [value], name as [text] FROM tb_prodoc_neticeler  WHERE deleted=0 AND TenantId=".$activeTenant);
    }
    else if ($istiqamet_tipi=="muraciet_tipi"){
        $sql = specialKeyForDos($filter,$istiqamet_tipi,$_POST['keys']);
        $data=DB::fetchAll($sql,true, PDO::FETCH_ASSOC);
    }
    else{
        $data=DB::fetchAll($sql,true, PDO::FETCH_ASSOC);
    }

    $netice=[];
    $netice['results']=[];
    if ($istiqamet_tipi=="mektub_nezaretdedir"||$istiqamet_tipi="qisa_mezmun_id"){
        foreach ($data as $value){
            $netice['results'][] = array (
                "id" => (int)$value['value'] ,
                "text" => htmlspecialchars( $value['text'],ENT_NOQUOTES)
            );
        }
    }
    else if(isset($_POST['report'])){
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


function specialKeyForDos($filter,$istiqamet_tipi,$keys){


    $sql="";
    isset($filter['daxil_olan_senedler'][$istiqamet_tipi])?
        $sql.= "Select ".$filter['daxil_olan_senedler'][$istiqamet_tipi]['id']." , 'daxil_olan_senedler' as [key], '".$istiqamet_tipi."' as istiqamet_tipi,  ".$filter['daxil_olan_senedler'][$istiqamet_tipi]['text']."  from ".$filter['daxil_olan_senedler'][$istiqamet_tipi]['from']." ".$filter['daxil_olan_senedler'][$istiqamet_tipi]['where']: " ";
    isset($filter['daxil_olan_senedler'][$istiqamet_tipi])&&(isset($filter['daxili_senedler'][$istiqamet_tipi]))?
        $sql.=" UNION ": "";
    isset($filter['daxili_senedler'][$istiqamet_tipi])?
        $sql.="Select ".$filter['daxili_senedler'][$istiqamet_tipi]['id']." , 'daxili_senedler' as [key],  '".$istiqamet_tipi."' as istiqamet_tipi,  ".$filter['daxili_senedler'][$istiqamet_tipi]['text']." 
        from ".$filter['daxili_senedler'][$istiqamet_tipi]['from']." ".$filter['daxili_senedler'][$istiqamet_tipi]['where']:" ";
    ((isset($filter['daxil_olan_senedler'][$istiqamet_tipi])||isset($filter['daxili_senedler'][$istiqamet_tipi]))&&  isset($filter['vetendash'][$istiqamet_tipi]))?
        $sql.=" UNION ":" ";
    isset($filter['vetendash'][$istiqamet_tipi])?
        $sql.="Select ".$filter['vetendash'][$istiqamet_tipi]['id']." , 'vetendash' as [key],  '".$istiqamet_tipi."' as istiqamet_tipi,  ".$filter['vetendash'][$istiqamet_tipi]['text']." 
        from ".$filter['vetendash'][$istiqamet_tipi]['from']." ".$filter['vetendash'][$istiqamet_tipi]['where']:" ";
    ((isset($filter['daxil_olan_senedler'][$istiqamet_tipi])||isset($filter['daxili_senedler'][$istiqamet_tipi])||isset($filter['vetendash'][$istiqamet_tipi]))&&isset($filter['xaric_olan_senedler'][$istiqamet_tipi]))?
        $sql.=" UNION ": " ";
    isset($filter['xaric_olan_senedler'][$istiqamet_tipi])?
        $sql.="Select ".$filter['xaric_olan_senedler'][$istiqamet_tipi]['id']." ,  'xaric_olan_senedler' as [key], '".$istiqamet_tipi."' as istiqamet_tipi , ".$filter['xaric_olan_senedler'][$istiqamet_tipi]['text']." from ".$filter['xaric_olan_senedler'][$istiqamet_tipi]['from']." ".$filter['xaric_olan_senedler'][$istiqamet_tipi]['where']: " ";

    return filterle($keys,$sql,true);

}

function filterle($keys,$sql,$vetendash=false){

    if (count($keys)>0&& $keys!=""){
        $filteredForReport=" AND (";
        foreach ($keys as $key => $value){
            if(!$vetendash){
                if ($value=='vetendash'){
                    $value='daxil_olan_senedler';
                }
            }
            $key == 0 ? $filteredForReport.="  general.[key] = '".$value."'":
                $filteredForReport.=" OR  general.[key] = '".$value."'";
        }
        $filteredForReport.=")";
        $sql= " SELECT * FROM (".$sql.")  as general WHERE 1=1 ".$filteredForReport;
    }else{
        $sql= " SELECT * FROM (".$sql.")  as general WHERE 1<>1 ";
    }


    return $sql;


}
