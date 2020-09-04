<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 12.04.2018
 * Time: 18:49
 */
require_once DIRNAME_INDEX . 'prodoc/model/TestiqleyecekShexs.php';

class Testiqleme
{
    public static function modulunTestiqleyecekShexsleriniQaytar($class, $recordId)
    {
        $class = pdo()->quote($class);
        $recordId = (int)$recordId;

        $sql = "
            SELECT user_id, tip, id
            FROM tb_prodoc_testiqleyecek_shexs
            WHERE
            related_class = {$class} AND 
            related_record_id = {$recordId} AND 
            status = 0
        ";

        return DB::fetchAllIndexed($sql, 'user_id');
    }

    public static function butunShexslerinStatuslariniYenile($class, $recordId)
    {
        $class = pdo()->quote($class);
        $recordId = (int)$recordId;

        $sql = sprintf("
            UPDATE tb_prodoc_testiqleyecek_shexs
            SET status = %s, status_changed_at = GETDATE()
            WHERE
            related_class = {$class} AND 
            related_record_id = {$recordId}
        ", TestiqleyecekShexs::STATUS_TESTIQLEMEYIB);

        DB::query($sql);
    }
}