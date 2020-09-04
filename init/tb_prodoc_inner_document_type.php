<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 24.05.2018
 * Time: 11:16
 */

$lastId = setTableData('tb_prodoc_inner_document_type', ['extra_id' => 'diger'], [
    'name' => 'Digər',
    'silinib' => '0',
    'extra_id' => 'diger'
]);

 setTableData('tb_prodoc_inner_document_type', ['extra_id' => 'create_act'], [
    'name' => 'Akt',
    'silinib' => '0',
    'parent_id' => $lastId,
     'extra_id' => 'create_act'
 ]);