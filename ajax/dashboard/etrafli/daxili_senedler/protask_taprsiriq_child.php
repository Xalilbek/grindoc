<?php

use  View\Helper\Proxy;

include_once DIRNAME_INDEX  . '/class/class.grouparray.php';

if(isset($parametrler) && isset($parametrler['sid']) && is_numeric($parametrler['sid']) && $parametrler['sid']>0)
{
    $userId = (int)$_SESSION['erpuserid'];
    $sid = (int)$parametrler['sid'];

    $sql = "
       Select 
            tb_protask_task_document.*
        from tb_protask_task_document
        LEFT JOIN tb_protask_task_theme on tb_protask_task_theme.id=tb_protask_task_document.task_theme_id
        left JOIN tb_protask_task_type on  tb_protask_task_type.id=tb_protask_task_document.task_type_id
        where tb_protask_task_document.id = $id
        ";

    $poa = DB::fetch($sql);

    $intDoc = new InternalDocument($poa['document_id']);

    require_once DIRNAME_INDEX . 'prodoc/Util/View.php';
    require_once DIRNAME_INDEX . 'prodoc/Util/Template.php';

    ob_start();
    require DIRNAME_INDEX . '/' . 'prodoc/templates/default/daxili_senedler/protask_taprsiriq_child.php';
    $html = ob_get_clean();

    print json_encode(array("status"=>"success","html"=>$html));
}