<?php ob_start() ?>
<?php
require_once DIRNAME_INDEX . 'prodoc/model/Task/Task.php';
require_once DIRNAME_INDEX . 'prodoc/model/TestiqleyecekShexs.php';
require_once DIRNAME_INDEX . 'prodoc/service/Confirmation.php';
require_once DIRNAME_INDEX . 'prodoc/Util/Template.php';
require_once DIRNAME_INDEX . 'prodoc/Util/View.php';
require_once DIRNAME_INDEX . 'class/class.functions.php';

use View\Helper\Proxy;
$user = new User();

$sql = "
		SELECT
          tb1.*,
          document_type.extra_id,
        
          (
            SELECT
              CONCAT(Adi, ' ', Soyadi, ' ', AtaAdi)
            FROM
              tb_users
            WHERE
              USERID = tb1.created_by
          ) AS created_by_name,
          status,
          (CASE
             WHEN document_type.extra_id = 'create_act' THEN 
               CONCAT(
                 'Akt/',
                  (SELECT document_number
                                                                  FROM v_daxil_olan_senedler
                                                                  WHERE id = (SELECT TOP 1 task_command_id
                                                                              FROM tb_prodoc_aktlar
                                                                              WHERE document_id = tb1.id)
                    )
               )
                  
            ELSE document_number END 
            ) as doc_number
        
        FROM
          v_daxil_olan_senedler_corrected AS tb1
            LEFT JOIN tb_prodoc_inner_document_type document_type ON document_type.id = tb1.internal_document_type_id
        WHERE tb1.id = '$id'
	";

$senedler = DB::fetch($sql);
$document_extra_id = $senedler['extra_id'];

$incomingDocument = new Document($id, [
    'data' => $senedler
]);

require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument.php';
$tip = InternalDocument::getExtraIdById((int)$incomingDocument->getData()['internal_document_type_id']);

$internalDoc = new InternalDocument($id);

$relatedData = $internalDoc->getRelatedData();

$derkenarEmeliyyat = DB::fetchOneBy('tb_prodoc_formlar_tesdiqleme', [
    'emeliyyat_tip' => 'derkenar',
    'daxil_olan_sened_id' => $id
]);

$statusExists = true;
$status = '';

if (FALSE !== $derkenarEmeliyyat) {
    // derkenar emeliyati var
    if (!isset($incomingDocument->getData()['status'])) {
        $statusExists = false;
    } else {
        $statusId = $incomingDocument->getData()['status'];

        if (is_null($statusId)) {
            $statusId = 1;
        }

        $status = $incomingDocument->getStatusTitle($statusId);
    }
} else {
    if (isset($relatedData['status'])) {
        $status = (int)$relatedData['status'] === 1 ? 'Bağlı' : 'Açıq';
    } else {
        $statusExists = false;
    }
}

require_once DIRNAME_INDEX . 'prodoc/includes/internal_document.php';
$sql = "
	SELECT tb1.*, tb2.user_ad, tb3.ad as emeliyyat_ad
	FROM tb_prodoc_formlar_tesdiqleme tb1
	LEFT JOIN v_user_adlar tb2 ON tb2.USERID=tb1.user_id
	LEFT JOIN tb_prodoc_emeliyyatlar as tb3 on tb1.emeliyyat_tip=tb3.emeliyyat
	WHERE tb1.daxil_olan_sened_id='{$id}' AND tb3.silinib=0 ORDER BY emeliyyat_ad ASC
";

$confirmingUsers = DB::fetchAll($sql);

$listOfPrincipals = [
    $senedler['created_by'],
];

foreach ($confirmingUsers as $confirmingUser) {
    $listOfPrincipals[] = $confirmingUser['user_id'];
}

$TenantId = $user->getActiveTenantId();

$type_name= DB::fetchColumn("SELECT  [name] from tb_prodoc_inner_document_type where id=".(int)$incomingDocument->getData()['internal_document_type_id']);

?>

    <div data-position="<?php print addButtonPositionKey($params['elementler'], 'tipi'); ?>" id="tipi">
        <span><?= dsAlt('2616etrafli_nov', 'Növ')?>:</span>
        <br>
        <strong><?php cap($type_name) ?></strong>
    </div>
    <div data-position="<?php print addButtonPositionKey($params['elementler'], 'senedin_nomresi'); ?>" id="senedin_nomresi">
        <small><?= dsAlt('2616etrafli_sened_nomre', "Sənədin nömrəsi")?>:</small><br>
        <span class="document_number_etrafli"><?php cap($senedler['doc_number']) ?></span>
    </div>
<?php if ($statusExists && getProjectName() !== BIZIM_MARKET): ?>
    <div data-position="<?php print addButtonPositionKey($params['elementler'], 'status'); ?>" id="status">
        <small><?= dsAlt('2616etrafli_status', "Status")?>:</small><br>
        <span><?= $status; ?></span>
    </div>
<?php endif; ?>
    <script>

        let rest = $('div#tesdiqleme > p').slice(1).detach();
        $('#tesdiqleme').first().append(rest);
        $('div#tesdiqleme').slice(1).detach();


        $(function() {
            var documet_number = $('.document_number_etrafli').text().trim();
            var dashboard_doc_number =$(".prodoc-main-page #senedler-tbody .selected").find('.document_number');
            if(dashboard_doc_number.text()==""||dashboard_doc_number.text()=="-"||dashboard_doc_number.text().length<2){
                console.log(dashboard_doc_number.text());
                dashboard_doc_number.text(documet_number);
            }
        });
    </script>
<?php $content = ob_get_contents(); ob_end_clean(); return $content; ?>

<?php
function isHidden($key, $params)
{
    if (!isset($params['hidden_fields'])) {
        return false;
    }

    return in_array($key, $params['hidden_fields']);
}
?>