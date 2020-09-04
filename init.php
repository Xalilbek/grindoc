<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 11.05.2018
 * Time: 16:50
 */
session_start();
require_once 'init/deploy_utils.php';
createFileIfNotExist('../../project_config.ini',true);

require_once '../class/class.functions.php';

try {
    DB::exec('SET TRANSACTION ISOLATION LEVEL SERIALIZABLE');
    DB::beginTransaction();

    $user = new User();
    $activeTenant = $user->getActiveTenantId();
    $userId = (int)0;

    copy(DIRNAME_INDEX . 'class/library_files/TemplateProcessor.php',
          DIRNAME_INDEX . 'vendor/phpoffice/phpword/src/PhpWord/TemplateProcessor.php');

    require_once DIRNAME_INDEX . 'prodoc/init/views.php';

    require_once DIRNAME_INDEX . 'prodoc/init/tb_modules.php';
    require_once DIRNAME_INDEX . 'prodoc/init/tb_prodoc_muraciet_tip.php';
    require_once DIRNAME_INDEX . 'prodoc/init/tb_sened_novleri.php';
    require_once DIRNAME_INDEX . 'prodoc/init/tb_sened_novu.php';
    require_once DIRNAME_INDEX . 'prodoc/init/tb_prodoc_emeliyyatlar.php';
    require_once DIRNAME_INDEX . 'prodoc/init/tb_prodoc_inner_document_type.php';
    require_once DIRNAME_INDEX . 'prodoc/init/tb_prodoc_alt_privilegiyalar.php';
    require_once DIRNAME_INDEX . 'prodoc/init/tb_prodoc_document_number.php';
    require_once DIRNAME_INDEX . 'prodoc/init/tb_prodoc_document_number_pattern_option_list.php';
    require_once DIRNAME_INDEX . 'prodoc/migrate.php';

    createDirIfNotExist(UPLOADS_DIR_PATH,true);
    createDirIfNotExist(UPLOADS_DIR_PATH.'imzalar',true);
    createDirIfNotExist(UPLOADS_DIR_PATH.'fb_modules',true);
    createDirIfNotExist(UPLOADS_DIR_PATH.'prodoc',true);
    createDirIfNotExist(UPLOADS_DIR_PATH.'logos',true);
    createDirIfNotExist(UPLOADS_DIR_PATH.'profile-images',true);
    createDirIfNotExist(UPLOADS_DIR_PATH.'msk',true);
    createDirIfNotExist(UPLOADS_DIR_PATH.'msk/prodoc',true);
    createDirIfNotExist(UPLOADS_DIR_PATH.'msk/prodoc/word_templates',true);
    createDirIfNotExist(PRODOC_FILES_SAVE_PATH,true);
    createDirIfNotExist(PRODOC_FILES_SAVE_PATH.'doc_convert_to_pdf',true);
    createDirIfNotExist(PRODOC_FILES_SAVE_PATH.'formal',true);
    createDirIfNotExist(PRODOC_FILES_SAVE_PATH.'formal/export_templates',true);
    createDirIfNotExist(PRODOC_FILES_SAVE_PATH.'/pdf_background/',true);
    createDirIfNotExist(PRODOC_FILES_SAVE_PATH.'/comment/',true);
    createDirIfNotExist(PROJECT_ROOT_PATH.'/prodoc/asset/icons', true);

    chmod(DIRNAME_INDEX . 'vendor/mpdf/mpdf/tmp',0777);

    if (!file_exists(PROJECT_ROOT_PATH . '/.htaccess')) {
        $htaccessContent = "
            <Files \"project_config.ini\">  
              Order Allow,Deny
              Deny from all
            </Files>
        ";
        file_put_contents(PROJECT_ROOT_PATH . '/.htaccess', $htaccessContent);
    }

//    refreshViews();

    DB::commit();
    echo 'Database is updated successfully';
} catch (Exception $e) {
    http_response_code(404);
    DB::rollback();

    echo "There is an error during database update: ";
    echo $e->getMessage();
    echo PHP_EOL;
    echo 'Stack trace: ';
    echo PHP_EOL;
    echo $e->getTraceAsString();
}

