<?php
session_start();
include_once '../../../class/class.functions.php';
include_once DIRNAME_INDEX . 'modules/module_builder/model.php';
include_once DIRNAME_INDEX . 'prodoc/component/Form.php';
$user = new User();

if (!$user->get_session()) {
    header("Location: login.php");
    exit;
}

$userId = (int)$_SESSION['erpuserid'];


$chixanSenedFields = [
    [
        "IsRequired" => true,
        "Title" => "Kim göndərir",
        "InputType" => "select",
        "ColumnName" => "kim_gonderir"
    ],
    [
        "IsRequired" => true,
        "Title" => "Sənədin növü",
        "InputType" => "select",
        "ColumnName" => "senedin_novu"
    ],
    [
        "IsRequired" => true,
        "Title" => "Vərəqlərin sayı",
        "InputType" => "text",
        "ColumnName" => "vereq_sayi"
    ],
    [
        "InputType" => "text.datetime",
        "ColumnName" => "gonderme_tarixi"
    ],
    [
        "InputType" => "text",
        "ColumnName" => "mektubun_qisa_mezmunu"
    ],
    [
        "InputType" => "text",
        "ColumnName" => "qeyd"
    ],
    [
        "Title" => "Sənəd",
        "InputType" => "file",
        "ColumnName" => "sened_fayl"
    ],
    [
        "InputType" => "file",
        "ColumnName" => "qoshma_fayl"
    ],
    [
        "InputType" => "text",
        "ColumnName" => "forma"
    ],
    [
        "IsRequired" => true,
        "InputType" => "arrayOfIds",
        "ColumnName" => "teyinat",
        "Title" => "Təyinat"
    ],
    [
        "IsRequired" => true,
        "InputType" => "arrayOfIds",
        "ColumnName" => "gonderen_teshkilat",
        "Title" => "Göndərən təşkilat"
    ],
    [
        "IsRequired" => true,
        "InputType" => "arrayOfIds",
        "ColumnName" => "gonderen_shexs",
        "Title" => "Göndərən şəxs"
    ]
//    [
//        "InputType" => "text",
//        "ColumnName" => "senedin_nomresi"
//    ],
//    [
//        "InputType" => "text",
//        "ColumnName" => "baglanti"
//    ],
//    [
//        "InputType" => "text",
//        "ColumnName" => "daxil_olan_sened_novu"
//    ],
//    [
//        "InputType" => "checkbox",
//        "ColumnName" => "daxil_olan_sened_chckbx"
//    ]
];

$chixanSenedForm = new Form($chixanSenedFields);
$chixanSenedForm->check();
$dataToBeInsertedChixanSened = $chixanSenedForm->collectDataToBeInserted();

$_module_entry_id = 0;

try {
	pdo()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	pdo()->beginTransaction();

    $_module_entry_ids = array();

	if ($_module_entry_id) {
		// updating
		SQL::update($tableName, $dataToBeInserted, $_module_entry_ids);

        $_module_entry_ids[] = $_module_entry_id;

		pdof()->query("DELETE FROM tb_fb_approves WHERE fb_module_entry_id='$_module_entry_id' AND tip='$_module_identifier'");

		// removing old files
		foreach (FB_getModuleFiles($_module_identifier, $_module_entry_id) as $file) {
			unlink(PRODOC_FILES_SAVE_PATH . "/{$file['file_actual_name']}");
		}
		pdof()->query("DELETE FROM tb_fb_files WHERE fb_module_entry_id='$_module_entry_id' AND fb_module_id='$_module_identifier'");

		SQL::insert('tb_fb_logs', [
			'fb_module_entry_id' => $_module_entry_id,
			'user_id' => $userId,
			'ne' => '1',
			// 'qeyd' 			=> $qeyd,
			'tip' => $_module_identifier
		]);

	} else {

		$dataToBeInsertedChixanSened['created_by'] = $userId;
		$dataToBeInsertedChixanSened['created_at'] = date('Y-m-d H:i:s');
		$dataToBeInsertedChixanSened['is_deleted'] = 0;
		$dataToBeInsertedChixanSened['TenantId'] = $user->getActiveTenantId();
//		$dataToBeInsertedChixanSened['son_emeliyyat'] = $dataToBeInsertedChixanSened['yoxlayan_shexs'] > 0 ? IncomingDocument::SON_EMELIYYAT_YOXLAMADA : IncomingDocument::SON_EMELIYYAT_SENED_QEYDIYYATDAN_KECIB;
//		$dataToBeInsertedChixanSened['status'] = 1;

        $teyinatlar = $dataToBeInsertedChixanSened['teyinat'];
        $gonderen_teshkilatlar = $dataToBeInsertedChixanSened['gonderen_teshkilat'];
        $gonderen_shexslar = $dataToBeInsertedChixanSened['gonderen_shexs'];

        $razilashdiranlar = isset($_POST['razilashdiran']) && is_array($_POST['razilashdiran']) ? $_POST['razilashdiran'] : [];

        $tableDatasCount = count($teyinatlar);

        for ($i = 0; $i < $tableDatasCount; $i++)
        {
            $dataToBeInsertedChixanSened['teyinat'] = $teyinatlar[$i];
            $dataToBeInsertedChixanSened['gonderen_teshkilat'] = $gonderen_teshkilatlar[$i];
            $dataToBeInsertedChixanSened['gonderen_shexs'] = $gonderen_shexslar[$i];

            SQL::insert('tb_chixan_senedler', $dataToBeInsertedChixanSened);
            $_module_entry_id = SQL::getLastId('tb_chixan_senedler');
            $_module_entry_ids[] = $_module_entry_id;

            SQL::insert('tb_chixan_senedler_log', [
                'sened_id'  => $_module_entry_id,
                'user_id'   => $userId,
                'ne'        => 'sened_qeydiyyatdan_kecib'
            ]);

            if (isset($razilashdiranlar[$i]) && is_string($razilashdiranlar[$i])) {
                $testiqleyenler = explode(',', $razilashdiranlar[$i]);

                for ($j = 0, $len_j = count($testiqleyenler); $j < $len_j; ++$j) {
                    DB::insert('tb_prodoc_testiqleyecek_shexs', [
                        'user_id' => $testiqleyenler[$j],
                        'tip' => 'razilashdiran',
                        'related_record_id' => $_module_entry_id,
                        'related_class' => 'OutgoingDocument'
                    ]);
                }
            }

        }
	}

	foreach ($_module_entry_ids as $id) {
        $chixanSenedForm->saveFiles($id, 'chixan_senedler', PRODOC_FILES_SAVE_PATH);
    }

	// sending notification
	// DB::fetchColumnArray("");

//	$nGonderilib = array();
//	$sessionUserInfo = $user->getUserInfo();
//	$text = $user->tmzle($sessionUserInfo['user_name'] . " {$module['name']} əlavə etdi.");
//	foreach (explode(",", $all_of_them_tes['tesdiqleme_geden_userler']) AS $melumatlanacaq)
//	{
//		if ($userId != $melumatlanacaq && $melumatlanacaq > 0 && !isset($nGonderilib[$melumatlanacaq]))
//		{
//			$nGonderilib[$melumatlanacaq] = 1;
//
//			DB::insert('tb_notifications', [
//				'bashliq' => $text,
//				'bolme'   => $tableName,
//				'kid' => $_module_entry_id,
//				'user_id' => $melumatlanacaq,
//				'icon' => ''
//			]);
//		}
//	}

//	if ($dataToBeInsertedChixanSened['rey_muellifi'] > 0)
//    {
//        $sessionUserInfo = $user->getUserInfo();
//        $username = $user->tmzle($sessionUserInfo['user_name']);
//        $user->sendNotifications( true, true,
//            'prodoc_sened_qeydiyyatdan_kecib',
//            $username, "",
//            $_module_entry_id,
//            $dataToBeInsertedChixanSened['rey_muellifi'],
//            "prodoc_sened_qeydiyyatdan_kecib"
//        );
//    }

    pdo()->commit();
	$user->success_msg();

} catch (Exception $e) {
	pdo()->rollBack();
	$user->error_msg($e->getMessage());
}