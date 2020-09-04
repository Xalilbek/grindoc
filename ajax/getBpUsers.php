<?php
session_start();
include_once '../../class/class.functions.php';
include_once DIRNAME_INDEX . 'modules/module_builder/model.php';
include_once DIRNAME_INDEX . 'prodoc/component/Form.php';

$user = new User();

$userId = $_SESSION['erpuserid'];

if(!$user->get_session()) {
    header("Location: login.php");
    exit;
}

$xosId = getInt('xosId', 0);

$sql = "
    SELECT daxil_olan_sened_id
    FROM v_prodoc_outgoing_document_relation
    WHERE outgoing_document_id = '$xosId'
";

$daxId = DB::fetchColumn($sql);

$bpUsers = [];

$document = new Document($daxId);
$document->setCustomQuery('v_daxil_olan_senedler', true);

$bpUsers['yoxlayan_shexs'] = [
    [
        'id' => $document->data['yoxlayan_shexs'],
        'text' => $user->getPersName($document->data['yoxlayan_shexs'])
    ]
];

$sql = "
    SELECT id, mesul_shexs, mesul_shexs_ad
    FROM v_derkenar
    WHERE daxil_olan_sened_id = $daxId
";

$taskIds = [];
$bpUsers['mesul_shexsler'] = [];
foreach (DB::fetchAll($sql) as $ms) {
    $bpUsers['mesul_shexsler'][] = [
        'id' => $ms['mesul_shexs'],
        'text' => $ms['mesul_shexs_ad']
    ];
    $taskIds[] = $ms['id'];
}

$bpUsers['kuratorlar'] = [];
$bpUsers['ishtrakchi_shexsler'] = [];
if (count($taskIds)) {
    $sql = sprintf("
        SELECT
        	(
                SELECT
                    CONCAT (Adi, ' ', Soyadi, ' ', AtaAdi)
                FROM
                    tb_users
                WHERE
                    USERID = tb_derkenar_elave_shexsler.user_id
            ) AS user_ad,
            tip,
            user_id
        FROM tb_derkenar_elave_shexsler
        WHERE derkenar_id IN (%s)
    ", implode(',', $taskIds));

    foreach (DB::fetchAll($sql) as $ms) {
        if ($ms['tip'] === "kurator") {
            $bpUsers['kuratorlar'][] = [
                'id' => $ms['user_id'],
                'text' => $ms['user_ad']
            ];
        } else {
            $bpUsers['ishtrakchi_shexsler'][] = [
                'id' => $ms['user_id'],
                'text' => $ms['user_ad']
            ];
        }
    }
}

print json_encode($bpUsers);