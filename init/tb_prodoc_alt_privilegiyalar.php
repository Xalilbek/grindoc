<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 18.09.2018
 * Time: 10:10
 */

$parentId = setTableData('tb_prodoc_alt_privilegiyalar', ['name' => 'Məlumatların birbaşa msk-ya əlavə edilməsi'], [
    'name' => 'Məlumatların birbaşa msk-ya əlavə edilməsi',
    'parent_id' => NULL,
    'TenantId' => 0,
    'key'  => 'dashboard',
    'is_dir' => '1',
    'sira' => 5,
    'string_id' => NULL
]);

setTableData('tb_prodoc_alt_privilegiyalar', ['name' => 'Hüquqi sənəd'], [
    'name' => 'Hüquqi sənəd',
    'parent_id' => $parentId,
    'TenantId' => 0,
    'key'  => 'dashboard',
    'is_dir' => '0',
    'sira' => 1,
    'string_id' => 'qeydiyyat_huquqi',
], ['string_id']);

setTableData('tb_prodoc_alt_privilegiyalar', ['name' => 'Vətəndaş müraciəti'], [
    'name' => 'Vətəndaş müraciəti',
    'parent_id' => $parentId,
    'TenantId' => 0,
    'key'  => 'dashboard',
    'is_dir' => '0',
    'sira' => 2,
    'string_id' => 'qeydiyyat_fiziki',
], ['string_id']);


setTableData('tb_prodoc_alt_privilegiyalar', ['name' => 'Daxili sənəd(Ümumi forma)'], [
    'name' => 'Daxili sənəd(Ümumi forma)',
    'parent_id' => $parentId,
    'TenantId' => 0,
    'key'  => 'dashboard',
    'is_dir' => '0',
    'sira' => 4,
    'string_id' => 'daxili_umumi_forma',
], ['string_id']);

setTableData('tb_prodoc_alt_privilegiyalar', ['name' => 'Xaric olan sənəd'], [
    'name' => 'Xaric olan sənəd',
    'parent_id' => $parentId,
    'TenantId' => 0,
    'key'  => 'dashboard',
    'is_dir' => '0',
    'sira' => 5,
    'string_id' => 'xaric_olan_sened',
], ['string_id']);

$parentId = setTableData('tb_prodoc_alt_privilegiyalar', ['string_id' => 'neticenin_qeyd_olunmasi_qovluq'], [
    'name' => 'Nəticənin qeyd olunması',
    'parent_id' => NULL,
    'TenantId' => 0,
    'key'  => 'dashboard',
    'is_dir' => '1',
    'sira' => 6,
    'string_id' => 'neticenin_qeyd_olunmasi_qovluq'
]);

setTableData('tb_prodoc_alt_privilegiyalar', ['string_id' => 'neticenin_qeyd_olunmasi_butun_senedler'], [
    'name' => 'Bütün sənədlər ',
    'parent_id' => $parentId,
    'TenantId' => 0,
    'key'  => 'dashboard',
    'is_dir' => '0',
    'sira' => 1,
    'string_id' => 'neticenin_qeyd_olunmasi_butun_senedler',
], ['string_id']);
setTableData('tb_prodoc_alt_privilegiyalar', ['string_id' => 'yekun_senedsiz'], [
    'name' => 'Yekun sənədsiz ',
    'parent_id' => 0,
    'TenantId' => 0,
    'key'  => 'dashboard',
    'is_dir' => '1',
    'sira' => 5,
    'string_id' => 'yekun_senedsiz',
], ['string_id']);

setTableData('tb_prodoc_alt_privilegiyalar', ['string_id' => 'umumi_shobe'], [
    'name' => 'Ümumi şöbə',
    'parent_id' => 0,
    'TenantId' => 0,
    'key'  => 'dashboard',
    'is_dir' => '1',
    'sira' => 15,
    'string_id' => 'umumi_shobe',
], ['string_id']);

setTableData('tb_prodoc_alt_privilegiyalar', ['string_id' => 'arayis_tipli_senedler'], [
    'name' => 'Arayış tipli sənədlər',
    'parent_id' => 0,
    'TenantId' => 0,
    'key'  => 'dashboard',
    'is_dir' => '1',
    'sira' => 16,
    'string_id' => 'arayis_tipli_senedler',
], ['string_id']);

setTableData('tb_prodoc_alt_privilegiyalar', ['string_id' => 'neticenin_qeyd_olunmasi_huquqi'], [
    'name' => 'Hüquqi sənəd ',
    'parent_id' => $parentId,
    'TenantId' => 0,
    'key'  => 'dashboard',
    'is_dir' => '0',
    'sira' => 2,
    'string_id' => 'neticenin_qeyd_olunmasi_huquqi',
], ['string_id']);

setTableData('tb_prodoc_alt_privilegiyalar', ['string_id' => 'neticenin_qeyd_olunmasi_fiziki'], [
    'name' => 'Fiziki sənəd ',
    'parent_id' => $parentId,
    'TenantId' => 0,
    'key'  => 'dashboard',
    'is_dir' => '0',
    'sira' => 3,
    'string_id' => 'neticenin_qeyd_olunmasi_fiziki',
], ['string_id']);

$parentId = setTableData('tb_prodoc_alt_privilegiyalar', [ 'string_id' => 'achiq'], [
    'name' => 'İcrada',
    'parent_id' => 0,
    'TenantId' => 0,
    'key'  => 'dashboard',
    'is_dir' => '1',
    'sira' => 2,
    'string_id' => 'achiq'
]);

setTableData('tb_prodoc_alt_privilegiyalar', [  'string_id' => 'aciq_icraci_oldugum'], [
    'name' => 'İcraçı olduğum',
    'parent_id' => $parentId,
    'TenantId' => 0,
    'key'  => 'dashboard',
    'is_dir' => '0',
    'sira' => 4,
    'string_id' => 'aciq_icraci_oldugum'
]);

setTableData('tb_prodoc_alt_privilegiyalar', ['string_id' => 'aciq_icrasina_nezaret_etdiyim'], [
    'name' => 'İcrasına nəzarət etdiyim',
    'parent_id' => $parentId,
    'TenantId' => 0,
    'key'  => 'dashboard',
    'is_dir' => '0',
    'sira' => 5,
    'string_id' => 'aciq_icrasina_nezaret_etdiyim'
]);

setTableData('tb_prodoc_alt_privilegiyalar', [ 'string_id' => 'aciq_hemicracisi_oldugum'], [
    'name' => 'Həm icraçısı olduğum',
    'parent_id' => $parentId,
    'TenantId' => 0,
    'key'  => 'dashboard',
    'is_dir' => '0',
    'sira' => 6,
    'string_id' => 'aciq_hemicracisi_oldugum'
]);
setTableData('tb_prodoc_alt_privilegiyalar', [ 'string_id' => 'yekun_sened_eleva_et'], [
    'name' => 'Yekun sənəd əlavə ətmək',
    'parent_id' => 0,
    'TenantId' => 0,
    'key'  => 'dashboard',
    'is_dir' => '1',
    'sira' => 7,
    'string_id' => 'yekun_sened_eleva_et'
]);
$parentId = setTableData('tb_prodoc_alt_privilegiyalar', [ 'string_id' => 'cari_emeliyyat'], [
    'name' => 'Cari əməliyyat',
    'parent_id' => 0,
    'TenantId' => 0,
    'key'  => 'dashboard',
    'is_dir' => '1',
    'sira' => 16,
    'string_id' => 'cari_emeliyyat'
]);
setTableData('tb_prodoc_alt_privilegiyalar', [ 'string_id' => 'daxil_olan_senedler'], [
    'name' => 'Daxil olan sənədlər',
    'parent_id' => $parentId,
    'TenantId' => 0,
    'key'  => 'dashboard',
    'is_dir' => '0',
    'sira' => 17,
    'string_id' => 'daxil_olan_senedler'
]);
setTableData('tb_prodoc_alt_privilegiyalar', [ 'string_id' => 'xaric_olan_senedler'], [
    'name' => 'Xaric olan sənədlər',
    'parent_id' => $parentId,
    'TenantId' => 0,
    'key'  => 'dashboard',
    'is_dir' => '0',
    'sira' => 18,
    'string_id' => 'xaric_olan_senedler'
]);
setTableData('tb_prodoc_alt_privilegiyalar', [ 'string_id' => 'daxili_senedler'], [
    'name' => 'Daxili sənədlər',
    'parent_id' => $parentId,
    'TenantId' => 0,
    'key'  => 'dashboard',
    'is_dir' => '0',
    'sira' => 19,
    'string_id' => 'daxili_senedler'
]);

$parentId = setTableData('tb_prodoc_alt_privilegiyalar', [ 'string_id' => 'ana_sehifenin_dinamik_tenzimlenmesi'], [
    'name' => 'Ana səhifənin dinamik tənzimlənməsi',
    'parent_id' => 0,
    'TenantId' => 0,
    'key'  => 'dashboard',
    'is_dir' => '1',
    'sira' => 22,
    'string_id' => 'ana_sehifenin_dinamik_tenzimlenmesi'
]);

setTableData('tb_prodoc_alt_privilegiyalar', [ 'string_id' => 'senedin_esas_melumatlarinin_tenzimlenmesi'], [
    'name' => 'Sənədin əsas məlumatlarının tənzimlənməsi',
    'parent_id' => $parentId,
    'TenantId' => 0,
    'key'  => 'dashboard',
    'is_dir' => '0',
    'sira' => 1,
    'string_id' => 'senedin_esas_melumatlarinin_tenzimlenmesi'
]);

setTableData('tb_prodoc_alt_privilegiyalar', [ 'string_id' => 'tablarin_tenzimlenmesi'], [
    'name' => 'Tabların tənzimlənməsi',
    'parent_id' => $parentId,
    'TenantId' => 0,
    'key'  => 'dashboard',
    'is_dir' => '0',
    'sira' => 2,
    'string_id' => 'tablarin_tenzimlenmesi'
]);

setTableData('tb_prodoc_alt_privilegiyalar', [ 'string_id' => 'senedlerin_etraflisinin_tenzimlenmesi'], [
    'name' => 'Sənədlərin ətraflısının tənzimlənməsi',
    'parent_id' => $parentId,
    'TenantId' => 0,
    'key'  => 'dashboard',
    'is_dir' => '0',
    'sira' => 3,
    'string_id' => 'senedlerin_etraflisinin_tenzimlenmesi'
]);

$parentId = setTableData('tb_prodoc_alt_privilegiyalar', [ 'string_id' => 'rey_yazmaq_huququ'], [
    'name' => 'Rəy yazmaq hüququ',
    'parent_id' => 0,
    'TenantId' => 0,
    'key'  => 'dashboard',
    'is_dir' => '1',
    'sira' => 24,
    'string_id' => 'rey_yazmaq_huququ'
]);

setTableData('tb_prodoc_alt_privilegiyalar', [ 'string_id' => 'emeliyyat_huququ_uzre'], [
    'name' => 'Əməliyyat hüququ üzrə',
    'parent_id' => $parentId,
    'TenantId' => 0,
    'key'  => 'dashboard',
    'is_dir' => '0',
    'sira' => 25,
    'string_id' => 'emeliyyat_huququ_uzre'
]);

setTableData('tb_prodoc_alt_privilegiyalar', [ 'string_id' => 'butun_senedler_uzre'], [
    'name' => 'Bütün sənədlər üzrə',
    'parent_id' => $parentId,
    'TenantId' => 0,
    'key'  => 'dashboard',
    'is_dir' => '0',
    'sira' => 26,
    'string_id' => 'butun_senedler_uzre'
]);

$parentId = setTableData('tb_prodoc_alt_privilegiyalar', ['string_id' => 'nezaret_sehifesinde_senedleri_gormek_huququ'], [
    'name' => 'Nəzarət səhifəsində icra müddətli sənədləri görmək hüququ',
    'parent_id' => 0,
    'TenantId' => 0,
    'key'  => 'dashboard',
    'is_dir' => '1',
    'sira' => 27,
    'string_id' => 'nezaret_sehifesinde_senedleri_gormek_huququ'
]);

setTableData('tb_prodoc_alt_privilegiyalar', ['string_id' => 'aid_oldugu_senedler_nezaret'], [
    'name' => 'Yalnız öz sənədləri',
    'parent_id' => $parentId,
    'TenantId' => 0,
    'key'  => 'dashboard',
    'is_dir' => '0',
    'sira' => 28,
    'string_id' => 'aid_oldugu_senedler_nezaret'
]);

setTableData('tb_prodoc_alt_privilegiyalar', ['string_id' => 'butun_senedler_nezaret'], [
    'name' => 'Bütün sənədlər',
    'parent_id' => $parentId,
    'TenantId' => 0,
    'key'  => 'dashboard',
    'is_dir' => '0',
    'sira' => 29,
    'string_id' => 'butun_senedler_nezaret'
]);

$parentId = setTableData('tb_prodoc_alt_privilegiyalar', ['string_id' => 'emeliyyat_sehifesinde_senedleri_gormek_huququ'], [
    'name' => 'Əməliyyat səhifəsində icra müddətli sənədləri görmək hüququ',
    'parent_id' => 0,
    'TenantId' => 0,
    'key'  => 'dashboard',
    'is_dir' => '1',
    'sira' => 30,
    'string_id' => 'emeliyyat_sehifesinde_senedleri_gormek_huququ'
]);

setTableData('tb_prodoc_alt_privilegiyalar', ['string_id' => 'aid_oldugu_senedler_emeliyyat'], [
    'name' => 'Yalnız öz sənədləri',
    'parent_id' => $parentId,
    'TenantId' => 0,
    'key'  => 'dashboard',
    'is_dir' => '0',
    'sira' => 31,
    'string_id' => 'aid_oldugu_senedler_emeliyyat'
]);

setTableData('tb_prodoc_alt_privilegiyalar', ['string_id' => 'butun_senedler_emeliyyat'], [
    'name' => 'Bütün sənədlər',
    'parent_id' => $parentId,
    'TenantId' => 0,
    'key'  => 'dashboard',
    'is_dir' => '0',
    'sira' => 32,
    'string_id' => 'butun_senedler_emeliyyat'
]);


$parentId = setTableData('tb_prodoc_alt_privilegiyalar', ['string_id' => 'daxil_olan_sened_qeydiyyata_almaq_huququ'], [
    'name' => 'Daxil olan sənədi qeydiyyata almaq hüququ',
    'parent_id' => 0,
    'TenantId' => 0,
    'key'  => 'dashboard',
    'is_dir' => '1',
    'sira' => 30,
    'string_id' => 'daxil_olan_sened_qeydiyyata_almaq_huququ'
]);

setTableData('tb_prodoc_alt_privilegiyalar', ['string_id' => 'huquqi_shexs_qeydiyyata_almaq_huququ'], [
    'name' => 'Hüquqi şəxs',
    'parent_id' => $parentId,
    'TenantId' => 0,
    'key'  => 'dashboard',
    'is_dir' => '0',
    'sira' => 30,
    'string_id' => 'huquqi_shexs_qeydiyyata_almaq_huququ'
]);

setTableData('tb_prodoc_alt_privilegiyalar', ['string_id' => 'vetendash_muracieti_qeydiyyata_almaq_huququ'], [
    'name' => 'Vətəndaş müraciəti',
    'parent_id' => $parentId,
    'TenantId' => 0,
    'key'  => 'dashboard',
    'is_dir' => '0',
    'sira' => 30,
    'string_id' => 'vetendash_muracieti_qeydiyyata_almaq_huququ'
]);


$parentId = '';

$parentId_tab = setTableData('tb_prodoc_alt_privilegiyalar',
    [
        'string_id' => 'tab'
    ],
    [
        'name' => 'Tab',
        'parent_id' => '0',
        'TenantId' => 0,
        'key'  => 'daxili_senedler',
        'is_dir' =>'1',
        'sira' => '1',
        'string_id' =>'tab'
    ]
);


$parentId_qeydiyyat = setTableData('tb_prodoc_alt_privilegiyalar',
    [
        'string_id' => 'qeydiyyat'
    ],
    [
        'name' => 'Qeydiyyat pəncərəsi',
        'parent_id' => $parentId_tab,
        'TenantId' => 0,
        'key'  => 'daxili_senedler',
        'is_dir' =>'1',
        'sira' => '',
        'string_id' =>'qeydiyyat',
    ]);


$dsTypes = DB::fetchAll('SELECT * FROM tb_prodoc_inner_document_type 
                              WHERE silinib=0');

$count=0;
foreach ($dsTypes as $dsType){

    $count++;
    if ($dsType['parent_id'] == NULL){
        $parentId = $parentId_tab;
    }else{
        $parentId = $parentId_qeydiyyat;
    }

    setTableData('tb_prodoc_alt_privilegiyalar',
        [
            'string_id' => $dsType['extra_id']
        ],
        [
            'name' => $dsType['name'],
            'parent_id' => $parentId,
            'TenantId' => 0,
            'key'  => 'daxili_senedler',
            'is_dir' =>'0',
            'sira' => $count,
            'string_id' => $dsType['extra_id']
        ],['parent_id']
    );

}