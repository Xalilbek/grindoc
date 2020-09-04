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

if (isset($_POST['pdf_vacib_sahe'])){
    $pdf_vacib_sahe = DB::quote(get('pdf_vacib_sahe'));
    $isset = DB::fetchColumn("SELECT id FROM tb_options WHERE option_name= 'pdf_vacib_sahe' ");
    if($isset){
        DB::query("UPDATE tb_options SET value = $pdf_vacib_sahe WHERE option_name = 'pdf_vacib_sahe'");
    }else{
        DB::insert('tb_options',
            [
                'option_name'=>'pdf_vacib_sahe',
                'value'=>$pdf_vacib_sahe,
                'TenantId'=>0
            ]
        );
    }
    $user->success_msg();
}
