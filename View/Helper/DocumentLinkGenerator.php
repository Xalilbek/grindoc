<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 15.10.2018
 * Time: 16:46
 */

namespace View\Helper;


class DocumentLinkGenerator
{
    public static function generateDashboardLink($text, $id, $type = null)
    {
        $b = $type === "outgoing" ? 'kurator' : 'prodoc_sened_qeydiyyatdan_kecib';

        return sprintf("<a href='index.php?module=prodoc_new&id=%s&bolme=%s'>%s</a>",
            $id,
            $b,
            htmlspecialchars($text)
        );
    }

    public static function generateHref($id, $type = null)
    {
        $b = $type === "outgoing" ? 'kurator' : 'prodoc_sened_qeydiyyatdan_kecib';

        return sprintf("index.php?module=prodoc_new&id=%s&bolme=%s",
            $id,
            $b
        );
    }
}