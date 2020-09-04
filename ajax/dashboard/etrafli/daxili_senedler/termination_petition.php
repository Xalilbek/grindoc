<?php
defined('DIRNAME_INDEX') or die("hara?");

Prodoc::activateClasses();

if(isset($_POST['sened_id']) && is_numeric($_POST['sened_id']) && (int)$_POST['sened_id']>0)
{
    $from_dashboard = isset($parametrler['from_dashboard']) && $parametrler['from_dashboard'] == '1' ? 1 : 0;
    $MN =time().rand(1,1000);
    $sid = (int)$_POST['sened_id'];
    $userId = (int)$_SESSION['erpuserid'];
    $language = $user->getLang();
    $dill = new dilStclass($language);

    $getUserInfo = ProdocInternal::getUserInfo($userId);
    $userGroup = (int)$getUserInfo['Groupp'];

    $emrInf = pdof()->query("SELECT * FROM tb_prodoc_formlar_xitam_erizesi WHERE id='$sid'")->fetch();

    $tip = "prodoc_formlar_xitam_erizesi";
    Prodoc::activateClasses();
    if (
        true ||
        (int)$emrInf['elave_edib']===$userId ||
        in_array($userId,explode(",",$emrInf['rehberler'])) ||
        in_array($userId,explode(",",$emrInf['tesdiqleme_geden_userler'])) ||
        pdof()->query("SELECT 1 FROM tb_prodoc_formlar_tesdiqleme WHERE document_id='$sid' AND tip='$tip' AND (user_id='$userId' OR vekaletname_user='$userId')")->fetch()
    )

    {
        $changeNotifStatus = ProdocInternal::updateNotifications($tip, $sid, $userId);

        $getUserInf2 = ProdocInternal::getUserInfo((int)$emrInf['elave_edib']);

        list($imtinaSebeb, $imtinaEden) = ProdocInternal::imtina($emrInf['status'], $emrInf['imtinaEden'], $emrInf['imtinaSebebi'], $sid, $tip);

        $edits = ProdocInternal::deyisdirib($sid, $tip);

        list($tr1, $tr2, $tesdiqBtn, $imtinaBtn) = ProdocInternal::tesdiqleyenler($sid, $tip, $userId, $emrInf['tesdiqleme_geden_userler'], (int)$emrInf['elave_edib']);


        $buttonlar = $user->template_yukle(
            'prodoc/formlar/buttonlar',
            array(
                'sid'=>$sid,
                'type'=>$tip,
                "MN"=>$MN,
                'tesdiqBtn'=>(int)$tesdiqBtn,
                'imtinaBtn'=>(int)$imtinaBtn,
                'editBtn'=>(int)((int)$emrInf['elave_edib'] == $userId),
                'imtinaEden'=>$imtinaEden,
                'status'=>(int)$emrInf['status'],
                "edits"=>$edits
            )
        );

        $elementler = array(
            "sid"				=> $sid,
            "MN"				=> $MN,
            "userId"			=> $userId,
            "edits"				=> $edits,
            'imtinaSebeb'		=> $imtinaSebeb,
            'imtinaEden'		=> $imtinaEden,
            'tr1'				=> ($tr1===""?" - ".dil::soz("yoxdur"):$tr1),
            'tr2'				=> ($tr2===''?" - ".dil::soz("yoxdur"):$tr2),

            "document_number" 	=> htmlspecialchars($emrInf['document_number']),
            "emekdash" 			=> htmlspecialchars($getUserInf2['user_ad']),
            "struktur" 			=> htmlspecialchars($getUserInf2['struktur_bolmesi']),
            "tarix" 			=> date("d-m-Y",strtotime($emrInf['tarix'])),
            "xitam_tarix" 		=> date("d-m-Y",strtotime($emrInf['xitam_tarix'])),
            'xitam_sebeb' 		=> htmlspecialchars($emrInf['sebeb']),

            'buttonlar'			=> $buttonlar,

            "current_date" 		=> date('d-m-Y'),
            "type" 				=> $tip,
            'status' 			=> (int)$emrInf['status'],
            'hide_btns' 		=> isset($parametrler['hide_buttons']) ? '1' : '0'
        );

        print json_encode(array("status"=>"success","html"=>($user->template_yukle('daxili_senedler/termination_petition',$elementler, 'prodoc'))));
    }
    else
    {
        print json_encode(array("status"=>"hazir","template"=>htmlspecialchars("<div style='color:red;'>".dil::soz("47sehv_olmaz")."</div>",ENT_QUOTES)));
        exit();
    }
}
