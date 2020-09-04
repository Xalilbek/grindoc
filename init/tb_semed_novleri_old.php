<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 24.05.2018
 * Time: 11:16
 */

require_once '../../class/class.functions.php';

$lastId = DB::insertAndReturnId('tb_sened_novleri', [
    'ad' => 'HR',
    'forma_ad' => 'hr'
]);
DB::insert('tb_sened_novleri', [
    'ad' => 'Xəstəlik vərəqi',
    'forma_ad' => 'xestelik_vereqi',
    'sub_id' => $lastId
]);
DB::insert('tb_sened_novleri', [
    'ad' => 'E-Etibarnamə',
    'forma_ad' => 'power_of_attorney',
    'sub_id' => $lastId
]);
DB::insert('tb_sened_novleri', [
    'ad' => 'Arayış',
    'forma_ad' => 'arayish',
    'sub_id' => $lastId
]);
DB::insert('tb_sened_novleri', [
    'ad' => 'İcazə',
    'forma_ad' => 'icaze',
    'sub_id' => $lastId
]);
DB::insert('tb_sened_novleri', [
    'ad' => 'İş tapşırığı',
    'forma_ad' => 'ish_taphsirigi',
    'sub_id' => $lastId
]);
DB::insert('tb_sened_novleri', [
    'ad' => 'E-vəkalətnamə',
    'forma_ad' => 'e_vekaletname',
    'sub_id' => $lastId
]);

$lastId = DB::insertAndReturnId('tb_sened_novleri', [
    'ad' => 'Müqavilə',
    'forma_ad' => 'muqavile'
]);
DB::insert('tb_sened_novleri', [
    'ad' => 'Əmək müqaviləsi',
    'forma_ad' => 'emek_muqavilesi',
    'sub_id' => $lastId
]);

$lastId = DB::insertAndReturnId('tb_sened_novleri', [
    'ad' => 'Əmrlər',
    'forma_ad' => 'emrler'
]);
DB::insert('tb_sened_novleri', [
    'ad' => 'Məzuniyyət əmri',
    'forma_ad' => 'mezuniyet_emri',
    'sub_id' => $lastId
]);
DB::insert('tb_sened_novleri', [
    'ad' => 'Ezamiyyət əmri',
    'forma_ad' => 'ezamiyet_emri',
    'sub_id' => $lastId
]);
DB::insert('tb_sened_novleri', [
    'ad' => 'İşə qəbul əmri',
    'forma_ad' => 'ishe_qebul_emri',
    'sub_id' => $lastId
]);
DB::insert('tb_sened_novleri', [
    'ad' => 'Əmək müqaviləsinə xitam əmri',
    'forma_ad' => 'emek_muqavilesine_xitam_emri',
    'sub_id' => $lastId
]);
DB::insert('tb_sened_novleri', [
    'ad' => 'Məzuniyyətə görə kompensasiya',
    'forma_ad' => 'mezuniyyete_gore_kompensasiya',
    'sub_id' => $lastId
]);

$lastId = DB::insertAndReturnId('tb_sened_novleri', [
    'ad' => 'Ərizələr',
    'forma_ad' => 'erizeler'
]);
DB::insert('tb_sened_novleri', [
    'ad' => 'Məzuniyyət ərizəsi',
    'forma_ad' => 'mezuniyet_erizesi',
    'sub_id' => $lastId
]);
DB::insert('tb_sened_novleri', [
    'ad' => 'İşə qəbul ərizəsi',
    'forma_ad' => 'ishe_qebul_erizesi',
    'sub_id' => $lastId
]);
DB::insert('tb_sened_novleri', [
    'ad' => 'İşə xitam ərizəsi',
    'forma_ad' => 'ishe_xitam_erizesi',
    'sub_id' => $lastId
]);
DB::insert('tb_sened_novleri', [
    'ad' => 'Başqa işə keçirtmə ərizəsi',
    'forma_ad' => 'bashqa_ishe_kechirtme_erizsesi',
    'sub_id' => $lastId
]);
DB::insert('tb_sened_novleri', [
    'ad' => 'Ezamiyyət ərizəsi',
    'forma_ad' => 'ezamiyyet_erizesi',
    'sub_id' => $lastId
]);

$lastId = DB::insertAndReturnId('tb_sened_novleri', [
    'ad' => 'Maliyyə',
    'forma_ad' => 'maliye'
]);
DB::insert('tb_sened_novleri', [
    'ad' => 'Avans tələbi',
    'forma_ad' => 'avans_telebi',
    'sub_id' => $lastId
]);
DB::insert('tb_sened_novleri', [
    'ad' => 'Avans hesabatı',
    'forma_ad' => 'avans_hesabati',
    'sub_id' => $lastId
]);
DB::insert('tb_sened_novleri', [
    'ad' => 'Əməkhaqqı avansı',
    'forma_ad' => 'emekhaqqi_avansi',
    'sub_id' => $lastId
]);

$lastId = DB::insertAndReturnId('tb_sened_novleri', [
    'ad' => 'Ümumi forma',
    'forma_ad' => 'umumi_forma'
]);

$lastId = DB::insertAndReturnId('tb_sened_novleri', [
    'ad' => 'Digər',
    'forma_ad' => 'diger'
]);

DB::insert('tb_sened_novleri', [
    'ad' => 'Əlavə razılaşdırma',
    'forma_ad' => 'elave_razilashdirma',
    'sub_id' => $lastId
]);

$lastId = DB::insertAndReturnId('tb_sened_novleri', [
    'ad' => 'Sifariş tipi',
    'forma_ad' => 'satin_alma'
]);