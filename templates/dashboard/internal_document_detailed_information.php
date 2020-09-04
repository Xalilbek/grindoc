<?php ob_start() ?>
<?php
require_once DIRNAME_INDEX . 'prodoc/model/Task/Task.php';
require_once DIRNAME_INDEX . 'prodoc/model/TestiqleyecekShexs.php';
require_once DIRNAME_INDEX . 'prodoc/service/Confirmation.php';
require_once DIRNAME_INDEX . 'prodoc/Util/Template.php';
require_once DIRNAME_INDEX . 'prodoc/Util/View.php';

use Util\View;
use View\Helper\Proxy;

$sql = "
		SELECT
			tb1.*,
			(
				SELECT
					CONCAT (Adi, ' ', Soyadi, ' ', AtaAdi)
				FROM
					tb_users
				WHERE
					USERID = tb1.created_by
			) AS created_by_name,
			(
				SELECT
					CONCAT (Adi, ' ', Soyadi, ' ', AtaAdi)
				FROM
					tb_users
				WHERE
					USERID = tb1.rey_muellifi
			) AS rey_muellifi_ad,
			tb8.user_ad AS yoxlayan_shexs_ad,
			tb9.name AS netice_ad
		FROM
			v_daxil_olan_senedler_corrected AS tb1
		LEFT JOIN v_user_adlar tb8 ON tb8.USERID = tb1.yoxlayan_shexs
		LEFT JOIN tb_prodoc_neticeler AS tb9 ON tb9.id = tb1.netice
		WHERE tb1.id = '$id'
	";

$senedler = DB::fetch($sql);

$incomingDocument = new Document($id);

$relatedTasks = Task::getAllTasks($incomingDocument);

$confirmation = new Service\Confirmation\Confirmation($incomingDocument);
$approvingUsers = array_column($confirmation->getApprovingUsers(TestiqleyecekShexs::STATUS_TESTIQLEYIB), 'user_id', 'user_id');

$taskIds = [];
$relatedMainTasks = [];
$relatedSubTasks  = [];
foreach ($relatedTasks as $relatedTask) {
    $taskIds[] = $relatedTask['id'];

    if($relatedTask['parentTaskId'] > 0)
    {
        $relatedMainTasks[] = $relatedTask;
    }
    else
    {
        $relatedSubTasks[] = $relatedTask;
    }

}

$taskRelatedUsers = [];
if (count($taskIds)) {
    $sql = "SELECT
				v_user_adlar.user_ad_qisa, v_user_adlar.USERID as user_id, tb_derkenar_elave_shexsler.tip
			FROM
				tb_derkenar_elave_shexsler
			LEFT JOIN v_user_adlar ON v_user_adlar.USERID = tb_derkenar_elave_shexsler.user_id
			WHERE
				derkenar_id IN (%s) AND (tip = 'kurator' OR tip = 'ishtrakchi')
			GROUP BY v_user_adlar.USERID, v_user_adlar.user_ad_qisa, tb_derkenar_elave_shexsler.tip";
    $taskRelatedUsers = DB::fetchAll(sprintf($sql, implode(',', $taskIds)));
}

$listOfPrincipals = [
    $senedler['created_by'],
    $senedler['yoxlayan_shexs'],
    $senedler['rey_muellifi']
];

foreach ($relatedTasks as $relatedTask) {
    $listOfPrincipals[] = $relatedTask['mesul_shexs'];
}

foreach ($taskRelatedUsers as $taskRelatedUser) {
    $listOfPrincipals[] = $taskRelatedUser['user_id'];
}

$proxy = new Proxy($incomingDocument);
$proxy->setListOfPrincipals($listOfPrincipals);

function renderTaskRelatedUser($taskRelatedUser, $key, $approvingUsers, $proxy)
{
    return sprintf(
        "<span> %s %s </span><br>",
		$proxy->getProxyNameByPrincipal($taskRelatedUser['user_id'], $taskRelatedUser['user_ad_qisa']),
        array_key_exists($taskRelatedUser['user_id'], $approvingUsers) ? '<i class="fa fa-check"></i>' : ''
    );
}
?>
	<p>
		<small>Sənədin nömrəsi:</small><br>
		<span><?php cap($senedler['document_number']) ?></span>
	</p>
	<p>
		<small>Status:</small><br>
		<span><?php cap(Document::getStatusTitle($senedler['status'])) ?></span>
	</p>
	<p>
		<small>Qeydiyyatçı:</small><br>
		<span><?= $proxy->getProxyNameByPrincipal($senedler['created_by'], $senedler['created_by_name']); ?></span>
	</p>
	<p>
		<small>Yoxlayan şəxs:</small><br>
		<span><?= $proxy->getProxyNameByPrincipal($senedler['yoxlayan_shexs'], $senedler['yoxlayan_shexs_ad']); ?></span>
	</p>
	<p>
		<small>Rəy müəllifi:</small><br>
		<span><?= $proxy->getProxyNameByPrincipal($senedler['rey_muellifi'], $senedler['rey_muellifi_ad']) ?></span>
	</p>
    <p><small>İcraçı</small></p>
    <?php foreach ($relatedSubTasks as $relatedTask): ?>
        <p><strong><?= $proxy->getProxyNameByPrincipal($relatedTask['mesul_shexs'], $relatedTask['mesul_shexs_ad']); ?></strong></p>
    <?php endforeach; ?>

    <p>
			<small>Kurator şəxs</small><br>
            <?php
				View::altPrintArray(
                    array_filter($taskRelatedUsers, function($approvingUser) {
                    	return 'kurator' === $approvingUser['tip'];
                    }),
					function ($taskRelatedUser, $taskRelatedUserKey) use ($approvingUsers, $proxy) {
                    	return renderTaskRelatedUser($taskRelatedUser, $taskRelatedUserKey, $approvingUsers, $proxy);
					},
					'<i>Əlavə olunmayıb</i>'
				);
            ?>
		</p>

		<p>
			<small>Həm icraçı</small><br>
			<?php
				View::altPrintArray(
					array_filter($taskRelatedUsers, function($approvingUser) {
						return 'ishtrakchi' === $approvingUser['tip'];
					}),
					function ($taskRelatedUser, $taskRelatedUserKey) use ($approvingUsers, $proxy) {
						return renderTaskRelatedUser($taskRelatedUser, $taskRelatedUserKey, $approvingUsers, $proxy);
					},
					'<i>Əlavə olunmayıb</i>'
				);
			?>
		</p>

        <p><small>Məsul icraçı</small></p>
        <?php foreach ($relatedMainTasks as $relatedTask): ?>
            <p><strong><?= $proxy->getProxyNameByPrincipal($relatedTask['mesul_shexs'], $relatedTask['mesul_shexs_ad']); ?></strong></p>
        <?php endforeach; ?>

        <p><small>İcradadır</small></p>
        <?php foreach ($relatedTasks as $relatedTask): ?>
            <p><strong><?= $proxy->getProxyNameByPrincipal($relatedTask['mesul_shexs'], $relatedTask['mesul_shexs_ad']); ?></strong></p>
        <?php endforeach; ?>

	<!--<?php //endif; ?>-->
<?php $content = ob_get_contents(); ob_end_clean(); return $content; ?>