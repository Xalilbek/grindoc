<?php

if(isset($parametrler) && isset($parametrler['sid']) && is_numeric($parametrler['sid']) && $parametrler['sid']>0)
{
    $TenantId = $user->getActiveTenantId();

    $userId = (int)$_SESSION['erpuserid'];
    $sid = (int)$parametrler['sid'];
    $VocInfo = pdof()->query("SELECT *,(SELECT user_ad FROM v_user_adlar s WHERE s.USERID=tb1.created_user) as user_name, 
                                       (SELECT TOP 1 vezife FROM v_users v WHERE v.USERID=tb1.employe) as vezife,
                                       (SELECT TOP 1 struktur_bolmesi FROM v_users st WHERE st.USERID=tb1.employe) as shobe,
                                       (SELECT TOP 1 user_ad FROM v_user_adlar s WHERE s.USERID=tb1.employe) as employee,
                                       (SELECT TOP 1 state FROM tb_daxil_olan_senedler as tb3 WHERE tb3.id = tb1.document_id) as state,
                                       (SELECT TOP 1 name FROM tb_arayish_teqdim_edilen_qurum tb2 WHERE is_deleted = 0 AND tb2.id = tb1.organization_id) as qurum_ad FROM tb_prodoc_certificate tb1 WHERE tb1.id='$sid' ")->fetch();

    $approveBtn = ((int)$VocInfo['status']!=3 && (int)$VocInfo['status']!=1 && pdof()->query("SELECT * FROM tb_prodoc_certificate_tesdiqleme WHERE user_id='$userId' AND status='0' AND qrup=(SELECT MIN(qrup) FROM tb_prodoc_certificate_tesdiqleme WHERE status='0')")->fetch()) ? true : false;
    $cancelBtn = ((int)$VocInfo['status']!==3 && (in_array($userId,explode(",",$VocInfo['tesdiqleme_geden_userler'])) || $userId==$VocInfo['employe']))? true : false;
//        DB::fetchColumn("SELECT file_original_name FROM tb_prodoc_sened_novleri_periods WHERE file_name!='' AND sened_novu_id='$sened_novu_id' AND TenantId='$TenantId' AND deleted=0 ");

    require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument.php';
    $intDoc = new InternalDocument($VocInfo['document_id']);

    require_once DIRNAME_INDEX . 'prodoc/includes/internal_document.php';
    $file_original_name= getIxracFileName('arayish',$TenantId);

    require_once DIRNAME_INDEX . 'prodoc/includes/etrafli/etrafli_functions.php';

    $elementler = getButtonPositionKeys('ds_arayish');
    $priv = new Privilegiya();
    $senedlerin_etraflisinin_tenzimlenmesi = $priv->getByExtraId('senedlerin_etraflisinin_tenzimlenmesi');

    $sened_legv_edilib = (int)$VocInfo['state'] === Document::STATE_IN_TRASH ? dsAlt('2616etrafli_legv', "Sənəd ləğv olunub")  : '';
    $state_html = (int)$VocInfo['state'] === Document::STATE_IN_TRASH ? "<div data-position='". addButtonPositionKey($elementler, 'sened_legv_olunub') ."' id='sened_legv_olunub'>
                        <div class='alert alert-danger' style='padding: 13px; color: #ff0000;'>
                            <strong>".$sened_legv_edilib."</strong>
                        </div>
                    </div>" : "";

    $infoMass = array(
        'state' => $state_html,
        "senedlerin_etraflisinin_tenzimlenmesi" => $senedlerin_etraflisinin_tenzimlenmesi,
        "tree" => $intDoc->getRelatedInternalDocumentsHTMLTree([], $elementler),
        "detailed_information" => $intDoc->getDetailedInformationHTML(['elementler' => $elementler]),
        "sid"=>$sid,
        "order_number"=>htmlspecialchars($VocInfo["order_number"]),
        "order_date"=>date("d-m-Y",strtotime($VocInfo["order_date"])),
        "work_reception_date"=>date("d-m-Y",strtotime($VocInfo["work_reception_date"])),
        "vezife"=>htmlspecialchars($VocInfo['vezife']),
        "vezife_key" => addButtonPositionKey($elementler,'selahiyyetli_vezife'),
        "shobe"=>htmlspecialchars($VocInfo['shobe']),
        'shobe_key' => addButtonPositionKey($elementler,'shobe'),
        "employee_id"=>(int)$VocInfo["employe"],
        "employee"=>htmlspecialchars($VocInfo["employee"]),
        "file_original_name"=>$file_original_name,
        "approveBtn"=>(int)$approveBtn,
        "cancelBtn"=>(int)$cancelBtn,
        "editBtn"=>($userId==(int)$VocInfo["created_user"])?'true':'false',
        "who_added"=>htmlspecialchars($VocInfo["user_name"]),
        "added_date"=>date("d-m-Y",strtotime($VocInfo["date"])),
        "qurum_ad"=>htmlspecialchars($VocInfo["qurum_ad"]),
        "47bashlama_tarix" => dsAlt("2616etrafli_bashlama_tarix", "Başlama tarixi"),
        "47emrin_nomresi" => dsAlt("2616qeydiyyat_pencereleri_emrin_nomresi", "Əmrin nömrəsi"),
        "47emrin_tarixi" => dsAlt("2616qeydiyyat_pencereleri_emrin_tarixi", "Əmrin tarixi"),
        "47elave_eden" => dsAlt("2616qeydiyyat_pencereleri_elave_eden", "Əlavə edən"),
        "47elave_olunma_tarixi" => dsAlt("2616qeydiyyat_pencereleri_elave_olunma_tarixi", "Əlavə olunma tarixi"),
        "47qoshma" => dsAlt("2616qeydiyyat_pencereleri_qoshma",  "Qoşma"),
        "47esas" => dsAlt("2616qeydiyyat_pencereleri_esas", "Əsas"),
        "47tarixce" => dsAlt("2616tarixce", "Tarixçə"),
        "47deyishdir" => dsAlt("2616qeydiyyat_pencereleri_deyishdir", "Dəyişdir"),
        "47tesdiqle" => dsAlt("2616testiqle", "Təsdiqlə"),
        "47bagla" => dsAlt("2616qeydiyyat_pencereleri_bagla", "Bağla"),
        "47hamemrtar" => dsAlt("2616qeydiyyat_pencereleri_hemilelik_emri_tarixce", "Hamiləlik əmri - Tarixcə"),
        "47hamemredit" => dsAlt("2616qeydiyyat_pencereleri_hemilelik_emri_duzelish", "Hamiləlik əmri - düzəliş"),
        "47imtina_et" => dsAlt("2616qeydiyyat_pencereleri_imtina", 'Imtina et'),
        "47sebeb" => dsAlt("2616qeydiyyat_pencereleri_sebeb", "Səbəb"),
        "47sebebi_daxil_edin" => dsAlt("2616qeydiyyat_pencereleri_sebebi_daxil_edin", "Səbəbi daxil edin..."),
        "47asa" => dsAlt("2616etrafli_asa", '"A.S.A"'),
        "asa_key" => addButtonPositionKey($elementler,'arayis_user_id_ad'),
        "47contract_date" => dil::soz("47contract_date"),
        "47sum" => dil::soz("47sum"),
        "47tohmetemrtar" => dil::soz("47tohmetemrtar"),
        "47tohmetemredit" => dil::soz("47tohmetemredit"),
        "47certificate__elave_et" => dil::soz("47certificate__elave_et"),
        "47certificate" => dil::soz("47certificate"),
        "47certiftar" => dil::soz("47certiftar"),
        "47certifedit" => dil::soz("47certifedit"),
        "47arayish_nomre" => dil::soz("47arayish_nomre"),
        "47arayish_tarix" => dsAlt("2616etrafli_arayish_tarix", "Arayışın tarixi"),
        'arayish_tarix_key' => addButtonPositionKey($elementler,'arayis_tarixi'),
        "47arayish_qurum" => dsAlt("2616etrafli_arayish_qurum", "Arayış təqdim edilən qurum:"),
        "arayish_qurum_key" => addButtonPositionKey($elementler,'arayish_teqdim_edilen_qurum'),
        "47arayish_iq_tarix" => dil::soz("47arayish_iq_tarix"),
        "arayish_iq_tarix_key" => addButtonPositionKey($elementler,'arayis_ise_qebul_tarixi'),
        "qeyd_key" => addButtonPositionKey($elementler,'qeyd'),
        'dom_position_result' => json_encode($elementler),
    );
    print json_encode(array("status"=>"success","html"=>($user->template_yukle('daxili_senedler/arayish_etrafli',$infoMass, 'prodoc'))));
}