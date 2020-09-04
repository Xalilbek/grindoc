<?php

$parentId = DB::insertAndReturnId('tb_prodoc_alt_privilegiyalar',[
    'name' => 'Rollar',
    'parent_id' => 0,
    'TenantId' => 0,
    'key'  => 'dashboard',
    'is_dir' => '1',
    'sira' => 17,
    'string_id' => 'rollar'
]);


DB::insert('tb_prodoc_alt_privilegiyalar',[
    'name' => 'Daxil olan sənədlər',
    'parent_id' => $parentId,
    'TenantId' => 0,
    'key'  => 'dashboard',
    'is_dir' => '0',
    'sira' => 17,
    'string_id' => 'daxil_olan_senedler_rollar'
]);
DB::insert('tb_prodoc_alt_privilegiyalar',[
    'name' => 'Xaric olan sənədlər',
    'parent_id' => $parentId,
    'TenantId' => 0,
    'key'  => 'dashboard',
    'is_dir' => '0',
    'sira' => 18,
    'string_id' => 'xaric_olan_senedler_rollar'
]);
DB::insert('tb_prodoc_alt_privilegiyalar',[
    'name' => 'Daxili sənədlər',
    'parent_id' => $parentId,
    'TenantId' => 0,
    'key'  => 'dashboard',
    'is_dir' => '0',
    'sira' => 19,
    'string_id' => 'daxili_senedler_rollar'
]);