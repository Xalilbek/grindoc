<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 17.09.2018
 * Time: 10:36
 */

$esasParentId = setTableData('tb_modules', ['module' => 'msk'], [
    'module' => 'msk',
    'module_name' => 'Ayarlar',
    'parent_id' => '0',
    'icon' => 'icon-wrench',
    'esas_parent' => '0',
    'menu_tipi' => 'sol_menu',
    'sira' => '1',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);

$id = setTableData('tb_modules', ['module' => 'msk_ayarlar'], [
    'module' => 'msk_ayarlar',
    'module_name' => 'Ayarlar',
    'parent_id' => $esasParentId,
    'icon' => 'icon-wrench',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'sol_menu',
    'sira' => '2',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);

setTableData('tb_modules', ['module' => 'prodoc_new'], [
    'module' => 'prodoc_new',
    'module_name' => 'ProDoc',
    'parent_id' => '0',
    'icon' => 'icon-docs',
    'esas_parent' => '0',
    'menu_tipi' => 'sol_menu',
    'sira' => '43',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);

$esasParentId  = getModuleIdByName('prodoc_new');

$id = setTableData('tb_modules', ['module' => 'msk:daxil_olan_senedler'], [
    'module' => 'msk:daxil_olan_senedler',
    'module_name' => 'Daxil olan sənədlər',
    'parent_id' => $id,
    'icon' => 'fa fa-book',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'setting_folder',
    'sira' => '1',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);

setTableData('tb_modules', ['module' => 'msk:msk_prodoc_sened_novleri_dpb'], [
    'module' => 'msk:msk_prodoc_sened_novleri_dpb',
    'module_name' => 'Daxil olan sənəd növləri "Məktubun tipləri"',
    'parent_id' => $id,
    'icon' => 'icon-docs',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'settings',
    'sira' => '1',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);

setTableData('tb_modules', ['module' => 'msk:msk_movzular'], [
    'module' => 'msk:msk_movzular',
    'module_name' => 'Daxil olan sənəd növləri "Mövzu"',
    'parent_id' => $id,
    'icon' => 'icon-note',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'settings',
    'sira' => '2',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);

setTableData('tb_modules', ['module' => 'msk:msk_derkenar_metnler'], [
    'module' => 'msk:msk_derkenar_metnler',
    'module_name' => 'Dərkənar mətnləri',
    'parent_id' => $id,
    'icon' => 'icon-note',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'settings',
    'sira' => '3',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);

setTableData('tb_modules', ['module' => 'msk:msk_melumat_statuslar'], [
    'module' => 'msk:msk_melumat_statuslar',
    'module_name' => 'Məlumat statuslar',
    'parent_id' => $id,
    'icon' => 'icon-note',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'settings',
    'sira' => '4',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);

setTableData('tb_modules', ['module' => 'msk:msk_prodoc_neticeler'], [
    'module' => 'msk:msk_prodoc_neticeler',
    'module_name' => 'Nəticələr',
    'parent_id' => $id,
    'icon' => 'icon-note',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'settings',
    'sira' => '5',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);

setTableData('tb_modules', ['module' => 'msk:msk_prodoc_regionlar'], [
    'module' => 'msk:msk_prodoc_regionlar',
    'module_name' => 'Regionlar',
    'parent_id' => $id,
    'icon' => 'icon-note',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'settings',
    'sira' => '6',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);

setTableData('tb_modules', ['module' => 'msk:msk_prodoc_mektub_tipleri_fiziki'], [
    'module' => 'msk:msk_prodoc_mektub_tipleri_fiziki',
    'module_name' => 'Məktub tipləri "Fiziki sənəd"',
    'parent_id' => $id,
    'icon' => 'icon-envelope-open',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'settings',
    'sira' => '7',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);

setTableData('tb_modules', ['module' => 'msk:msk_prodoc_daxil_olma_menbeleri'], [
    'module' => 'msk:msk_prodoc_daxil_olma_menbeleri',
    'module_name' => 'Daxil olma mənbələri "Fiziki sənəd"',
    'parent_id' => $id,
    'icon' => 'icon-login',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'settings',
    'sira' => '8',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);

setTableData('tb_modules', ['module' => 'msk:msk_prodoc_nazalogiya'], [
    'module' => 'msk:msk_prodoc_nazalogiya',
    'module_name' => 'Məktubun qısa məzmunu',
    'parent_id' => $id,
    'icon' => 'icon-note',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'settings',
    'sira' => '9',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);

$id = setTableData('tb_modules', ['module' => 'msk:daxili_senedler'], [
    'module' => 'msk:daxili_senedler',
    'module_name' => 'Daxili sənədlər',
    'parent_id' => $id,
    'icon' => 'fa fa-book',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'setting_folder',
    'sira' => '2',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);

setTableData('tb_modules', ['module' => 'msk:msk_prodoc_daxili_sened_novleri_prodoc'], [
    'module' => 'msk:msk_prodoc_daxili_sened_novleri_prodoc',
    'module_name' => 'Daxili sənəd növləri',
    'parent_id' => $id,
    'icon' => 'icon-docs',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'settings',
    'sira' => '1',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);

setTableData('tb_modules', ['module' => 'msk:msk_prodoc_emeliyyatlar'], [
    'module' => 'msk:msk_prodoc_emeliyyatlar',
    'module_name' => 'Daxili sənəd növlərində "Təsdiqləmə"',
    'parent_id' => $id,
    'icon' => 'icon-wrench',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'settings',
    'sira' => '2',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);

setTableData('tb_modules', ['module' => 'msk:msk_sened_novu'], [
    'module' => 'msk:msk_sened_novu',
    'module_name' => 'Ümumi forma "Sənəd növü"',
    'parent_id' => $id,
    'icon' => 'icon-note',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'settings',
    'sira' => '3',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);

$id = setTableData('tb_modules', ['module' => 'msk:prodoc_muessise'], [
    'module' => 'msk:prodoc_muessise',
    'module_name' => 'Müəssisə/Təşkilat',
    'parent_id' => $id,
    'icon' => 'fa fa-book',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'setting_folder',
    'sira' => '3',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);

setTableData('tb_modules', ['module' => 'msk:msk_prodoc_CustomersCompany'], [
    'module' => 'msk:msk_prodoc_CustomersCompany',
    'module_name' => 'Aidiyyatı orqan',
    'parent_id' => $id,
    'icon' => 'icon-home',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'settings',
    'sira' => '1',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);

setTableData('tb_modules', ['module' => 'msk:msk_prodoc_qurumlar'], [
    'module' => 'msk:msk_prodoc_qurumlar',
    'module_name' => 'Tabeli qurum',
    'parent_id' => $id,
    'icon' => 'icon-home',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'settings',
    'sira' => '2',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);

setTableData('tb_modules', ['module' => 'msk:msk_prodoc_elaqeli_shexs'], [
    'module' => 'msk:msk_prodoc_elaqeli_shexs',
    'module_name' => 'Əlaqəli şəxs',
    'parent_id' => $id,
    'icon' => 'icon-user',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'settings',
    'sira' => '4',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);

$id = setTableData('tb_modules', ['module' => 'msk:prodoc_istifadeci'], [
    'module' => 'msk:prodoc_istifadeci',
    'module_name' => 'İstifadəçi',
    'parent_id' => $id,
    'icon' => 'fa fa-book',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'setting_folder',
    'sira' => '4',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);

setTableData('tb_modules', ['module' => 'msk:msk_prodoc_privilegiyalar'], [
    'module' => 'msk:msk_prodoc_privilegiyalar',
    'module_name' => 'Privilegiyalar',
    'parent_id' => $id,
    'icon' => 'icon-wrench',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'settings',
    'sira' => '1',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);

setTableData('tb_modules', ['module' => 'msk:msk_prodoc_sobeler'], [
    'module' => 'msk:msk_prodoc_sobeler',
    'module_name' => 'Şöbələr',
    'parent_id' => $id,
    'icon' => 'icon-home',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'settings',
    'sira' => '2',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);

setTableData('tb_modules', ['module' => 'msk:msk_prodoc_reyveren'], [
    'module' => 'msk:msk_prodoc_reyveren',
    'module_name' => 'Dərkənar icraçıları',
    'parent_id' => $id,
    'icon' => 'icon-user',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'settings',
    'sira' => '3',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);

setTableData('tb_modules', ['module' => 'msk:msk_prodoc_redakt_edecek_shexsler'], [
    'module' => 'msk:msk_prodoc_redakt_edecek_shexsler',
    'module_name' => 'Redakt eden şəxs',
    'parent_id' => $id,
    'icon' => 'icon-user',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'settings',
    'sira' => '4',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);

setTableData('tb_modules', ['module' => 'msk:msk_prodoc_razilashdiracaq_shexsler'], [
    'module' => 'msk:msk_prodoc_razilashdiracaq_shexsler',
    'module_name' => 'Razılaşdıran şəxs',
    'parent_id' => $id,
    'icon' => 'icon-user',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'settings',
    'sira' => '5',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);

setTableData('tb_modules', ['module' => 'msk:msk_prodoc_visa_verecek_shexsler'], [
    'module' => 'msk:msk_prodoc_visa_verecek_shexsler',
    'module_name' => 'Viza verən şəxs',
    'parent_id' => $id,
    'icon' => 'icon-user',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'settings',
    'sira' => '6',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);

setTableData('tb_modules', ['module' => 'msk:msk_prodoc_chap_edecek_shexsler'], [
    'module' => 'msk:msk_prodoc_chap_edecek_shexsler',
    'module_name' => 'Çap edən şəxs',
    'parent_id' => $id,
    'icon' => 'icon-user',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'settings',
    'sira' => '7',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);

setTableData('tb_modules', ['module' => 'msk:msk_prodoc_skan_merkezi'], [
    'module' => 'msk:msk_prodoc_skan_merkezi',
    'module_name' => 'Scan Mərkəzi',
    'parent_id' => $id,
    'icon' => 'icon-printer',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'settings',
    'sira' => '8',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);


$id = setTableData('tb_modules', ['module' => 'msk:prodoc_diger'], [
    'module' => 'msk:prodoc_diger',
    'module_name' => 'Digər',
    'parent_id' => $id,
    'icon' => 'fa fa-book',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'setting_folder',
    'sira' => '5',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);



setTableData('tb_modules', ['module' => 'msk:msk_prodoc_muraciet_tipleri'], [
    'module' => 'msk:msk_prodoc_muraciet_tipleri',
    'module_name' => 'Müraciətin tipləri',
    'parent_id' => $id,
    'icon' => 'icon-users',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'settings',
    'sira' => '1',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);

setTableData('tb_modules', ['module' => 'msk:msk_prodoc_umumi_forma_sened_novu'], [
    'module' => 'msk:msk_prodoc_umumi_forma_sened_novu',
    'module_name' => 'Ümumi forma sənəd növü/rol',
    'parent_id' => $id,
    'icon' => 'icon-users',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'settings',
    'sira' => '2',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);

setTableData('tb_modules', ['module' => 'msk:msk_prodoc_mektubun_tipi_rol'], [
    'module' => 'msk:msk_prodoc_mektubun_tipi_rol',
    'module_name' => 'Məktubun tipi/rol',
    'parent_id' => $id,
    'icon' => 'icon-users',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'settings',
    'sira' => '3',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);

setTableData('tb_modules', ['module' => 'msk:msk_login_page'], [
    'module' => 'msk:msk_login_page',
    'module_name' => 'Login page',
    'parent_id' => $id,
    'icon' => 'icon-users',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'settings',
    'sira' => '10',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);

setTableData('tb_modules', ['module' => 'msk:msk_structor_position_text'], [
    'module' => 'msk:msk_structor_position_text',
    'module_name' => 'Struktur, vəzifə',
    'parent_id' => $id,
    'icon' => 'icon-users',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'settings',
    'sira' => '1',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);

setTableData('tb_modules', ['module' => 'msk:msk_prodoc_sablon'], [
    'module' => 'msk:msk_prodoc_sablon',
    'module_name' => ' Word - Şablonlar',
    'parent_id' => $id,
    'icon' => 'icon-docs',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'settings',
    'sira' => '7',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);

setTableData('tb_modules', ['module' => 'msk:msk_prodoc_muraciet_alt_tipleri'], [
    'module' => 'msk:msk_prodoc_muraciet_alt_tipleri',
    'module_name' => 'Müraciətin alt tipləri',
    'parent_id' => $id,
    'icon' => 'icon-users',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'settings',
    'sira' => '9',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);

setTableData('tb_modules', ['module' => 'msk:msk_prodoc_xo_tipi_rol'], [
    'module' => 'msk:msk_prodoc_xo_tipi_rol',
    'module_name' => 'Xaric olan sənəd tipi/rol',
    'parent_id' => $id,
    'icon' => 'icon-users',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'settings',
    'sira' => '10',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);

setTableData('tb_modules', ['module' => 'msk:msk_senedlerin_etraflisi'], [
    'module' => 'msk:msk_senedlerin_etraflisi',
    'module_name' => 'Sənədlərin ətraflısı',
    'parent_id' => $id,
    'icon' => 'icon-users',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'settings',
    'sira' => '10',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);

if (getProjectName() === TS || getProjectName() === AP ) {

    setTableData('tb_modules', ['module' => 'msk:msk_prodoc_sifaris_tipi'], [
        'module' => 'msk:msk_prodoc_sifaris_tipi',
        'module_name' => 'Sifariş tipi',
        'parent_id' => $id,
        'icon' => 'icon-docs',
        'esas_parent' => $esasParentId,
        'menu_tipi' => 'settings',
        'sira' => '10',
        'module_iden' => NULL,
        'TenantId' => '0',
        'TabAllExists' => '0',
        'ActiveFromAdminPanel' => '0',
        'ActiveFromUserPanel' => '1',
        'system_type' => 'all',
    ]);

    setTableData('tb_modules', ['module' => 'msk:msk_prodoc_olcu_vahidi'], [
        'module' => 'msk:msk_prodoc_olcu_vahidi',
        'module_name' => 'Ölcü vahidi',
        'parent_id' => $id,
        'icon' => '',
        'esas_parent' => $esasParentId,
        'menu_tipi' => 'settings',
        'sira' => '7',
        'module_iden' => NULL,
        'TenantId' => '0',
        'TabAllExists' => '0',
        'ActiveFromAdminPanel' => '0',
        'ActiveFromUserPanel' => '1',
        'system_type' => 'all',
    ]);
}

setTableData('tb_modules', ['module' => 'msk:msk_prodoc_nomrelenme'], [
    'module' => 'msk:msk_prodoc_nomrelenme',
    'module_name' => 'Nömrələnmə',
    'parent_id' => $id,
    'icon' => 'icon-list',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'settings',
    'sira' => '2',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);

setTableData('tb_modules', ['module' => 'msk:msk_prodoc_shobelerin_indeksleri'], [
    'module' => 'msk:msk_prodoc_shobelerin_indeksleri',
    'module_name' => 'Şöbələrin indeksləri',
    'parent_id' => $id,
    'icon' => 'icon-list',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'settings',
    'sira' => '3',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);

setTableData('tb_modules', ['module' => 'msk:msk_prodoc_sheherlerin_indeksleri'], [
    'module' => 'msk:msk_prodoc_sheherlerin_indeksleri',
    'module_name' => 'Şəhərlərin indeksləri',
    'parent_id' => $id,
    'icon' => 'icon-list',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'settings',
    'sira' => '4',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);

setTableData('tb_modules', ['module' => 'msk:msk_general_istehsalat_teqvimi'], [
    'module' => 'msk:msk_general_istehsalat_teqvimi',
    'module_name' => 'Nəzarət müddəti',
    'parent_id' => $id,
    'icon' => 'icon-calendar',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'settings',
    'sira' => '5',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);

setTableData('tb_modules', ['module' => 'msk:msk_prodoc_qr_kod'], [
    'module' => 'msk:msk_prodoc_qr_kod',
    'module_name' => 'Forma QR-Kod',
    'parent_id' => $id,
    'icon' => 'fa fa-qrcode',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'settings',
    'sira' => '6',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);

setTableData('tb_modules', ['module' => 'msk:msk_prodoc_user_interface'], [
    'module' => 'msk:msk_prodoc_user_interface',
    'module_name' => 'User Interface',
    'parent_id' => $id,
    'icon' => 'icon-users',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'settings',
    'sira' => '7',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);

setTableData('tb_modules', ['module' => 'msk:msk_prodoc_qruplashdirilacaq_shexsler'], [
    'module' => 'msk:msk_prodoc_qruplashdirilacaq_shexsler',
    'module_name' => 'Qruplaşdırılacaq şəxslər',
    'parent_id' => $id,
    'icon' => 'icon-users',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'settings',
    'sira' => '8',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);

setTableData('tb_modules', ['module' => 'msk:msk_arayish_teqdim_edilen_qurum'], [
    'module' => 'msk:msk_arayish_teqdim_edilen_qurum',
    'module_name' => 'Arayış təqdim edilən qurum',
    'parent_id' => $id,
    'icon' => '',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'settings',
    'sira' => '10',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);

$id = getModuleIdByName('profile');

setTableData('tb_modules', ['module' => 'sened_dovriyyesi_privilegiyalari'], [
    'module' => 'sened_dovriyyesi_privilegiyalari',
    'module_name' => 'Sənəd dövriyyəsi privilegiyaları',
    'parent_id' => $id,
    'icon' => 'document',
    'esas_parent' => $id,
    'menu_tipi' => 'profile',
    'sira' => '10',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
]);


$id = setTableData('tb_modules', ['module' => 'prodoc_general_report'], [
    'module' => 'prodoc_general_report',
    'module_name' => 'Ümumi hesabat',
    'parent_id' => $esasParentId,
    'icon' => 'icon-bar-chart',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'sol_menu',
    'sira' => '8',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
    'module_folder' => 'ProDoc',
]);

$id = setTableData('tb_modules', ['module' => 'emeliyyat_sehifesi'], [
    'module' => 'emeliyyat_sehifesi',
    'module_name' => 'Əməliyyat səhifəsi',
    'parent_id' => $esasParentId,
    'icon' => 'icon-docs',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'sol_menu',
    'sira' => '9',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all'
]);

$id = setTableData('tb_modules', ['module' => 'prodoc_umumi_hesabat_visual'], [
    'module' => 'prodoc_umumi_hesabat_visual',
    'module_name' => 'Ümumi hesabat ( visual )',
    'parent_id' => $esasParentId,
    'icon' => 'icon-bar-chart',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'sol_menu',
    'sira' => '9',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
    'module_folder' => 'ProDoc',
]);

$id = setTableData('tb_modules', ['module' => 'prodoc_daxili_senedler'], [
    'module' => 'prodoc_daxili_senedler',
    'module_name' => 'Daxili sənədlər',
    'parent_id' => $esasParentId,
    'icon' => 'icon-bar-chart',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'sol_menu',
    'sira' => '3',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
    'module_folder' => 'ProDoc',
]);


$id = setTableData('tb_modules', ['module' => 'prodoc_report_for_ts'], [
    'module' => 'prodoc_report_for_ts',
    'module_name' => 'Hesabat',
    'parent_id' => $esasParentId,
    'icon' => 'icon-bar-chart',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'sol_menu',
    'sira' => '8',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
    'module_folder' => 'ProDoc',
]);

$id = setTableData('tb_modules', ['module' => 'prodoc_daxil_olan_senedler'], [
    'module' => 'prodoc_daxil_olan_senedler',
    'module_name' => 'Daxil olan sənədlər',
    'parent_id' => $esasParentId,
    'icon' => 'fa fa-files-o',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'sol_menu',
    'sira' => '1',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
    'module_folder' => 'ProDoc',
]);

$id = setTableData('tb_modules', ['module' => 'prodoc_xaric_olan_senedler'], [
    'module' => 'prodoc_xaric_olan_senedler',
    'module_name' => 'Xaric olan sənədlər',
    'parent_id' => $esasParentId,
    'icon' => 'fa fa-files-o',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'sol_menu',
    'sira' => '2',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
    'module_folder' => 'ProDoc',
]);

$id = setTableData('tb_modules', ['module' => 'prodoc_nezaret_sehifesi'], [
    'module' => 'prodoc_nezaret_sehifesi',
    'module_name' => 'Nəzarət səhifəsi',
    'parent_id' => $esasParentId,
    'icon' => 'fa fa-files-o',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'sol_menu',
    'sira' => '4',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
    'module_folder' => 'ProDoc',
]);

$id = setTableData('tb_modules', ['module' => 'prodoc_dos_fiziki_hesabat'], [
    'module' => 'prodoc_dos_fiziki_hesabat',
    'module_name' => 'Daxil olan sənədlər (Fiziki)',
    'parent_id' => $esasParentId,
    'icon' => 'icon-bar-chart',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'sol_menu',
    'sira' => '7',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
    'module_folder' => 'ProDoc',
]);

$id = setTableData('tb_modules', ['module' => 'prodoc_dos_huquqi_hesabat'], [
    'module' => 'prodoc_dos_huquqi_hesabat',
    'module_name' => 'Daxil olan sənədlər (Hüquqi)',
    'parent_id' => $esasParentId,
    'icon' => 'icon-bar-chart',
    'esas_parent' => $esasParentId,
    'menu_tipi' => 'sol_menu',
    'sira' => '8',
    'module_iden' => NULL,
    'TenantId' => '0',
    'TabAllExists' => '0',
    'ActiveFromAdminPanel' => '0',
    'ActiveFromUserPanel' => '1',
    'system_type' => 'all',
    'module_folder' => 'ProDoc',
]);

if (getProjectName() === TS || getProjectName() === AP ) {
    setTableData('tb_modules', ['module' => 'prodoc_new&filter=satin_alma'], [
        'module' => 'prodoc_new&filter=satin_alma',
        'module_name' => 'Sifariş',
        'parent_id' => '0',
        'icon' => 'icon-docs',
        'esas_parent' => '0',
        'menu_tipi' => 'sol_menu',
        'sira' => '43',
        'module_iden' => NULL,
        'TenantId' => '0',
        'TabAllExists' => '0',
        'ActiveFromAdminPanel' => '0',
        'ActiveFromUserPanel' => '1',
        'system_type' => 'all',
    ]);
}


function getModuleIdByName($name)
{
    $id = DB::fetchOneColumnBy('tb_modules', 'id', [
        'module' => $name
    ]);

    if (FALSE === $id) {
        throw new Exception(sprintf("Module with the name '%s' is not defined", $name));
    }

    return $id;
}