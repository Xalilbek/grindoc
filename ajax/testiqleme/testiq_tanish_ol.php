<?php

$sql = "
        SELECT
            tb_prodoc_testiqleyecek_shexs.id,
            tb_prodoc_testiqleyecek_shexs.related_record_id
        FROM
            tb_prodoc_testiqleyecek_shexs
            LEFT JOIN tb_derkenar ON tb_derkenar.id = tb_prodoc_testiqleyecek_shexs.related_record_id
        WHERE
            tb_derkenar.daxil_olan_sened_id =
            (
            SELECT
                daxil_olan_sened_id
            FROM
                tb_derkenar
            WHERE
            id = ".$testiqleyecekShexs->getInfo()['related_record_id']."
            ) AND tip = 'tanish_ol' AND related_class = 'Task' AND user_id = ". $testiqleyecekShexs->getInfo()['user_id'];

foreach (DB::fetchAll($sql) as $ids) {
    if ($testiqleyecekShexs->getInfo()['id'] != $ids['id']) {
        $acceptedUsers = new TestiqleyecekShexs($ids['id']);
        $acceptedUsers->testiqle(['note' => get('note')]);
    }
}


