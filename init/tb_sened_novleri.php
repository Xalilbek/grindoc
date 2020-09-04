<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 24.05.2018
 * Time: 11:16
 */

$digerId = setTableData('tb_sened_novleri', ['forma_ad' => 'diger'], [
    'ad' => 'Digər',
    'silinib' => 0,
    'sened_tip' => 'daxili_sened_novu',
    'standart' => 0,
    'forma_ad' => 'diger',
    'TenantId' => 0,
    'sub_id' => NULL
]);

$etibarnameId = setTableData('tb_sened_novleri', ['forma_ad' => 'etibarname_esas'], [
    'ad' => 'Etibarnamə',
    'silinib' => 0,
    'sened_tip' => 'xo_sened_novu',
    'standart' => 0,
    'forma_ad' => 'etibarname_esas',
    'TenantId' => 0,
    'sub_id' => NULL
]);

setTableData('tb_sened_novleri', ['sub_id' => $etibarnameId], [
    'ad' => 'Etibarnamə',
    'silinib' => 0,
    'sened_tip' => 'xo_sened_novu',
    'standart' => 1,
    'forma_ad' => 'etibarname_esas',
    'TenantId' => 0,
    'sub_id' => $etibarnameId
]);


$arayishId = setTableData('tb_sened_novleri', ['forma_ad' => 'arayish_xos'], [
    'ad' => 'Arayış',
    'silinib' => 0,
    'sened_tip' => 'xo_sened_novu',
    'standart' => 0,
    'forma_ad' => 'arayish_xos',
    'TenantId' => 0,
    'sub_id' => NULL
]);

setTableData('tb_sened_novleri', ['sub_id' => $arayishId], [
    'ad' => 'Arayış',
    'silinib' => 0,
    'sened_tip' => 'xo_sened_novu',
    'standart' => 1,
    'forma_ad' => 'arayish_xos',
    'TenantId' => 0,
    'sub_id' => $arayishId
]);

if (getProjectName() === TS || getProjectName() === AP ) {

    $orderType  =  setTableData('tb_sened_novleri', ['forma_ad' => 'satin_alma', 'sened_tip' => 'daxili_sened_novu'], [
        'ad' => 'Sifariş tipi',
        'silinib' => 0,
        'sened_tip' => 'daxili_sened_novu',
        'standart' => 0,
        'forma_ad' => 'satin_alma',
        'TenantId' => 0,
        'sub_id' => NULL
    ]);
}
if (getProjectName() === ANAMA) {

    $actId = setTableData('tb_sened_novleri', ['forma_ad' => 'create_act'], [
        'ad' => 'Akt',
        'silinib' => 0,
        'sened_tip' => 'daxili_sened_novu',
        'standart' => 0,
        'forma_ad' => 'create_act',
        'TenantId' => 0,
        'sub_id' => NULL
    ]);
    setTableData('tb_sened_novleri', ['forma_ad' => 'muhafize'], [
        'ad' => 'Mina/PHS-lərin müvəqqəti mühafizə
                                          altında saxlanılmasına
                                          dair',
        'silinib' => 0,
        'sened_tip' => 'daxili_sened_novu',
        'standart' => 0,
        'forma_ad' => 'muhafize',
        'TenantId' => 0,
        'sub_id' => $actId
    ]);
    setTableData('tb_sened_novleri', ['forma_ad' => 'zerersizleshdirme'], [
        'ad' => ' Mina/PHS-lərin zərərsizləşdirilməsinə dair',
        'silinib' => 0,
        'sened_tip' => 'daxili_sened_novu',
        'standart' => 0,
        'forma_ad' => 'zerersizleshdirme',
        'TenantId' => 0,
        'sub_id' => $actId
    ]);
    setTableData('tb_sened_novleri', ['forma_ad' => 'dashinma'], [
        'ad' => ' Mina/PHS-lərin daşınmasına dair',
        'silinib' => 0,
        'sened_tip' => 'daxili_sened_novu',
        'standart' => 0,
        'forma_ad' => 'dashinma',
        'TenantId' => 0,
        'sub_id' => $actId
    ]);
    setTableData('tb_sened_novleri', ['forma_ad' => 'tek_sursat_ashkar'], [
        'ad' => '"Tək Sursat"
                                         əməliyyatı zamanı Mina/PHS-lərin
                                         aşkar olunmasına dair',
        'silinib' => 0,
        'sened_tip' => 'daxili_sened_novu',
        'standart' => 0,
        'forma_ad' => 'tek_sursat_ashkar',
        'TenantId' => 0,
        'sub_id' => $actId
    ]);

    setTableData('tb_sened_novleri', ['forma_ad' => 'tesk_sursat_dashinma'], [
        'ad' => '"Tək Sursat"
                                         əməliyyatı zamanı Mina/PHS-lərin
                                         aşkar olunmasına və daşınmasına dair',
        'silinib' => 0,
        'sened_tip' => 'daxili_sened_novu',
        'standart' => 0,
        'forma_ad' => 'tesk_sursat_dashinma',
        'TenantId' => 0,
        'sub_id' => $actId
    ]);
    setTableData('tb_sened_novleri', ['forma_ad' => 'ashkar_dashinma'], [
        'ad' => '"Aşkar olunan oldu/soyuq silah, onların qurğuları və
                                            ehtiyyat hissələri, tərkibi və təyinatı məlum olmayan
                                            Mina/PHS və qablaşdırılması pozulmayan və təkrar istifadəsi mümkün olan
                                            Mina/PHS-lərin Azərbaycan
                                            Respubilklası Müdafiə Nazirliyinı verilməsi üçün təhvil-təslim"',
        'silinib' => 0,
        'sened_tip' => 'daxili_sened_novu',
        'standart' => 0,
        'forma_ad' => 'ashkar_dashinma',
        'TenantId' => 0,
        'sub_id' => $actId
    ]);
    setTableData('tb_sened_novleri', ['forma_ad' => 'elave_razilashdirma'], [
        'ad' => 'Əlavə razılaşdırma',
        'silinib' => 0,
        'sened_tip' => 'daxili_sened_novu',
        'standart' => 0,
        'forma_ad' => 'elave_razilashdirma',
        'TenantId' => 0,
        'sub_id' => $digerId
    ]);

    setTableData('tb_sened_novleri', ['forma_ad' => 'task_command'], [
        'ad' => 'Tapşırıq əmri',
        'silinib' => 0,
        'sened_tip' => 'daxili_sened_novu',
        'standart' => 0,
        'forma_ad' => 'task_command',
        'TenantId' => 0,
        'sub_id' => $digerId
    ]);

    setTableData('tb_sened_novleri', ['forma_ad' => 'hesabat_yarat'], [
        'ad' => 'Hesabat formasi',
        'silinib' => 0,
        'sened_tip' => 'daxili_sened_novu',
        'standart' => 0,
        'forma_ad' => 'hesabat_yarat',
        'TenantId' => 0,
        'sub_id' => $digerId
    ]);


    setTableData('tb_sened_novleri', ['forma_ad' => 'teqdimat', 'sened_tip' => 'daxili_sened_novu'], [
        'ad' => 'Təqdimat',
        'silinib' => 0,
        'sened_tip' => 'daxili_sened_novu',
        'standart' => 0,
        'forma_ad' => 'teqdimat',
        'TenantId' => 0,
        'sub_id' => $digerId
    ]);
    setTableData('tb_sened_novleri', ['forma_ad' => 'etibarname_chixan_sened', 'sened_tip' => 'daxili_sened_novu'], [
        'ad' => 'Etibarnamə (Yanacaq doldurulması barədə)',
        'silinib' => 0,
        'sened_tip' => 'daxili_sened_novu',
        'standart' => 0,
        'forma_ad' => 'etibarname_chixan_sened',
        'TenantId' => 0,
        'sub_id' => $etibarnameId
    ]);
}
