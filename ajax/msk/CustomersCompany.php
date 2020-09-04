<?php
session_start();
include_once '../../../class/class.functions.php';
$user = new User();

if(!$user->get_session())
{
    print "daxil_olmayib";
    exit();
}

$pr = (int)$user->checkPrivilegia("msk:msk_prodoc_CustomersCompany");
if ($pr !== 2)
{
    print json_encode(array("status"=>"hazir","template"=>htmlspecialchars('<div>Olmaz!</div>',ENT_QUOTES)));
    exit();
}

if(isset($_POST['tid']) && is_numeric($_POST['tid']) && isset($_POST['ne']) && $_POST['ne']=='sil')
{

    $tid = (int)$_POST['tid'];
    pdof() -> query("UPDATE tb_CustomersCompany SET silinib='1' WHERE id='$tid'");
    pdof() -> query("UPDATE tb_prodoc_aidiyyati_tabeli_qurum SET parentCompanyId=0 WHERE parentCompanyId = ".$tid);
    pdof()->query("UPDATE tb_Customers SET company_id=0 where company_id=".$tid);
}

else if(isset($_POST['tid']) && is_numeric($_POST['tid']) && $_POST['tid']>=0 && isset($_POST['ad']) && is_string($_POST['ad']) && trim($_POST['ad'])!="" )
{
    $tid = (int)$_POST['tid'];
    $ad = $user->tmzle(trim($_POST['ad']));
    $fildType = (int)$_POST['fildType'];
    $tekrarYoxla = pdof() -> query("SELECT 1 FROM tb_CustomersCompany WHERE silinib<>'1' AND Adi=N'$ad' ".($tid>0?" AND id<>'$tid'":"")." ")->fetch();
    if($tekrarYoxla)
    {
        print "error";
        exit();
    }
    if($tid>0)
    {
        pdof() -> query("UPDATE tb_CustomersCompany SET Adi=N'$ad', Tipi = '{$fildType}' WHERE id='$tid'");

    }
    else
    {

        $userTenantId = $user->getActiveTenantId();


        pdof() -> query("INSERT INTO tb_CustomersCompany (Adi,Tipi, TenantId) VALUES (N'$ad', '{$fildType}', '$userTenantId')");
        $tid = pdof() -> query("SELECT MAX(id) FROM tb_CustomersCompany")->fetch();
        $tid = $tid[0];

    }
    if(isset($_POST['alt_qurum'])&&is_string($_POST['alt_qurum'])&&$_POST['alt_qurum']!=''){
        pdof()->query("UPDATE tb_prodoc_aidiyyati_tabeli_qurum SET parentCompanyId=0 where parentCompanyId=".$tid);
        $qurumlar=array_map('intval', explode(',',$_POST['alt_qurum']));
        $qurumlar=implode(',',$qurumlar);

        pdof() -> query("UPDATE tb_prodoc_aidiyyati_tabeli_qurum SET parentCompanyId=".$tid." WHERE id IN(".$qurumlar.") ");
    }
    else
    {
        pdof()->query("UPDATE tb_prodoc_aidiyyati_tabeli_qurum SET parentCompanyId=0 where parentCompanyId=".$tid);
    }

    if(isset($_POST['elaqeli_sexs'])&&is_string($_POST['elaqeli_sexs'])&&$_POST['elaqeli_sexs']!=''){
        pdof()->query("UPDATE tb_Customers SET company_id=0 where company_id=".$tid);
        $elaqeli_sexs=array_map('intval', explode(',',$_POST['elaqeli_sexs']));
        $elaqeli_sexsler=implode(',',$elaqeli_sexs);

        pdof() -> query("UPDATE tb_Customers SET company_id=".$tid." WHERE id IN(".$elaqeli_sexsler.") ");
    }
    else
    {
        pdof()->query("UPDATE tb_Customers SET company_id=0 where company_id=".$tid);
    }

    print $tid;
}
