<?php
session_start();
include_once '../../../class/class.functions.php';
include_once DIRNAME_INDEX . 'modules/module_builder/model.php';
include_once DIRNAME_INDEX . 'prodoc/component/Form.php';

$user = new User();

if(!$user->get_session())
{
    print "daxil_olmayib";
    exit();
}

$nm = get('user_interface');

if(isset($_POST['user_interface'])){
    if ($nm != 'yan_yana' && $nm != 'alt_alta') {
        $user->error_msg();
    }

    DB::query("UPDATE tb_options SET value = '$nm' WHERE option_name = 'user_interface'");
    $user->success_msg();
}
elseif (isset($_POST['pdf_redact'])){
    $pdf_redact = get('pdf_redact');
    $isset = DB::fetchColumn("SELECT id FROM tb_options WHERE option_name= 'pdf_redact' ");
    if($isset){
        DB::query("UPDATE tb_options SET value = '$pdf_redact' WHERE option_name = 'pdf_redact'");
    }else{
        DB::insert('tb_options',
            [
                'option_name'=>'pdf_redact',
                'value'=>$pdf_redact,
                'TenantId'=>0
            ]
            );
    }
    $user->success_msg();
}
elseif (isset($_POST['cari_emeliyyatlar'])){
    $cari_emeliyyatlar = get('cari_emeliyyatlar');
    $isset = DB::fetchColumn("SELECT id FROM tb_options WHERE option_name= 'cari_emeliyyatlar' ");
    if($isset){
        DB::query("UPDATE tb_options SET value = '$cari_emeliyyatlar' WHERE option_name = 'cari_emeliyyatlar'");
    }else{
        DB::insert('tb_options',
            [
                'option_name'=>'cari_emeliyyatlar',
                'value'=>$cari_emeliyyatlar,
                'TenantId'=>0
            ]
        );
    }
    $user->success_msg();
}