
<?php use Util\View;
use View\Helper\Proxy;

print $intDoc->getDetailedInformationHTML() ?>

<?php
    $id = $intDoc->getId();
    $taskDocument = new \protask\model\TaskDocument($intDoc->getId());
	$confirmation = new Service\Confirmation\Confirmation($taskDocument);
	$allowedOps = $confirmation->getAllowedOperationsOfAllUsers();

    $listOfPrincipals = [];
    foreach ($allowedOps as $confirmingUser) {
        $listOfPrincipals[] = $confirmingUser['user_id'];
    }

    $proxy = new Proxy($incomingDocument);
    $proxy->setListOfPrincipals($listOfPrincipals);

    $user = new User();

    $sql = sprintf("
    	SELECT 
    		t1.*,
    		t2.user_name AS rey_muellifi_ad,
    		t3.user_name AS icrachi_ad,
    		t4.name AS task_theme_ad,
    		t5.name AS task_type_ad,
    		CONCAT('VT/',YEAR(v_task_toplantilar.elave_tarixi),'-',REPLACE(STR(v_task_toplantilar.id, 5), SPACE(1), 0)) AS meet_number,
    		v_task_toplantilar.id AS meet_id
    	FROM tb_protask_task_document AS t1
    	LEFT JOIN v_users AS t2 ON t2.USERID = t1.rey_muellifi
    	LEFT JOIN v_users AS t3 ON t3.USERID = t1.icrachi
    	LEFT JOIN tb_protask_task_theme AS t4 ON t4.id = t1.task_theme_id
    	LEFT JOIN tb_protask_task_type  AS t5 ON t5.id = t1.task_type_id
    	LEFT JOIN v_task_toplantilar ON v_task_toplantilar.id = t1.meet_id
    	WHERE t1.document_id = %s
    ", $id);
    $taskDocumentData = DB::fetch($sql);

    $sql = sprintf("
    	SELECT 
    		user_id, 
    		tip, 
    		v_users.user_name AS user_ad
    	FROM tb_protask_task_document_member AS M
    	LEFT JOIN
    	v_users ON v_users.USERID = M.user_id  
    	WHERE 
    	M.task_document_id = %s
    ", $taskDocumentData['id']);
    $grouppedSers = DB::fetchAllGroupped($sql, 'tip');

	require_once DIRNAME_INDEX . 'protask/ajax/emeliyyatlar_array.php';
?>

<p>
	<small>
		İcra statusu
	</small><br>
	<span>
		<?php
        $statuses = [
            'legv',
            'tehvil_aldim',
            'ishlenmeye_gonder',
            'icrachi_qebul'
        ];

        $statusesTitleMap = [
            'legv' => 'Ləğv edilib',
            'tehvil_aldim' => 'Yerinə yetirilib',
            'ishlenmeye_gonder' => 'Əlavə işlənməyə göndərildi',
            'icrachi_qebul' => 'İcradadır',
        ];

        $op = NULL;
        foreach ($statuses as $status) {
            if ($confirmation->hasOperationExecuted($status)) {
                $op = $status;
                break;
            }
        }

        $statusText = "";
        if (!is_null($op)) {
            $statusText = $statusesTitleMap[$op];
        } else {
            $statusText = "Başlanmayıb";
        }
        ?>
        <?php print $statusText; ?>
	</span>
</p>

<p>
	<small>
		Tapşırıqın tarixi
	</small><br>
	<span>
		<?php print \Util\Date::formatDateTime($taskDocumentData['task_date']); ?>
	</span>
</p>

<p>
	<small>
		Tapşırıqın növü
	</small><br>
	<span>
		<?php print htmlspecialchars($taskDocumentData['task_type_ad']); ?>
	</span>
</p>

<p>
	<small>
		İcra müddəti
	</small><br>
	<span>
		<?php print \Util\Date::formatDateTime($taskDocumentData['icra_edilme_tarixi']); ?>
	</span>
</p>

<p>
	<small>
		Tapşırıqın mövzusu
	</small><br>
	<span>
		<?php print htmlspecialchars($taskDocumentData['task_theme_ad']); ?>
	</span>
</p>
<p>
    <small>Tapşırıq daxili nəzarətdədir</small><br>
    <?php print($taskDocumentData['nezaret'] ? '<i class="fa fa-check"></i>' : "") ?>
</p>

<p>
	<small>
		Qeyd
	</small><br>
	<span>
		<?php print htmlspecialchars($taskDocumentData['qeyd']); ?>
	</span>
</p>

<p>
	<small>
		Qərar verəcək şəxs
	</small><br>
	<span>
		<?= $proxy->getProxyNameByPrincipal(
			$taskDocumentData['rey_muellifi'], $taskDocumentData['rey_muellifi_ad']
		) ?>
	</span>
</p>

<p>
	<small>
		İcraçı
	</small><br>
	<span>
		<?= $proxy->getProxyNameByPrincipal(
            $taskDocumentData['icrachi'], $taskDocumentData['icrachi_ad']
        ) ?>
	</span>
</p>

<?php foreach ($grouppedSers as $tip => $grouppedSer): ?>
	<p>
		<small>
			<?php
				switch ($tip) {
					case 'nezaret':
						print 'Nəzarət edən şəxs';
						break;
                    case 'ishtirakchi':
                        print 'İştirakçılar';
                        break;
                    case 'hem_icrachi':
                        print 'Həmicraçı';
                        break;
				}
			?>
		</small><br>
		<span>
			<?php
				foreach ($grouppedSer as $ggg):
			?>
				<?= $proxy->getProxyNameByPrincipal(
					$ggg['user_id'], $ggg['user_ad']
				); ?><br>
			<?php endforeach; ?>
		</span>
	</p>
<?php endforeach; ?>

<p>
	<small>
		Toplantı:
	</small><br>
	<span>
		<?php
			if ((int)$taskDocumentData['meet_id']) {
                $link = sprintf(
                    '<a target="_blank" href="../ProID/index.php?module=task_video_toplanti#toplanti_id=%s">%s</a>',
                    $taskDocumentData['meet_id'],
                    htmlspecialchars($taskDocumentData['meet_number'])
                );
                print $link;
			} else {
                print '<i>Yoxdur</i>';
			}
		?>
	</span>
</p>

<p>
	<small>Cari əməliyyatlar :</small>
</p>

<?php
    foreach ($allowedOps as $allowedOp):

	if (1 === preg_match('/^redd/', $allowedOp['type'])) {
        $allowedOp['type'] = 'redd_umumi';
	}
?>
    <p style="font-weight: bold;">
        <?= $proxy->getProxyNameByPrincipal($allowedOp['user_id'], $user->getPersName($allowedOp['user_id'])) ?>:

		<?php print
			isset($operationNamesMap[$allowedOp['type']])
				? (
					is_string($operationNamesMap[$allowedOp['type']])
						? $operationNamesMap[$allowedOp['type']]
						: $operationNamesMap[$allowedOp['type']]['title']
				)
				: $allowedOp['type']
			;
		?>
    </p>
<?php
    endforeach;
?>

<?php print $intDoc->getRelatedInternalDocumentsHTMLTree(); ?>