<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 11.05.2018
 * Time: 16:50
 */

require_once '../class/class.functions.php';

try {
    DB::beginTransaction();

    DB::query("INSERT INTO [prospect_dpb].[dbo].[tb_prodoc_muraciet_tip] ([ad], [serbest], [emeliyyat], [silinib], [extra_id]) VALUES (N'Sorğu', '1', '1', '0', 'sorgu')");
    DB::query("INSERT INTO [prospect_dpb].[dbo].[tb_prodoc_muraciet_tip] ([ad], [serbest], [emeliyyat], [silinib], [extra_id]) VALUES (N'Yönləndirmə', '1', '1', '0', 'yonlendirme')");
    DB::query("INSERT INTO [prospect_dpb].[dbo].[tb_prodoc_muraciet_tip] ([ad], [serbest], [emeliyyat], [silinib], [extra_id]) VALUES (N'Cavab məktubu', '1', '1', '0', 'cavab_mektubu')");
    DB::query("INSERT INTO [prospect_dpb].[dbo].[tb_prodoc_muraciet_tip] ([ad], [serbest], [emeliyyat], [silinib], [extra_id]) VALUES (N'Arayış', '1', '1', '0', 'arayis')");
    DB::query("INSERT INTO [prospect_dpb].[dbo].[tb_prodoc_muraciet_tip] ([ad], [serbest], [emeliyyat], [silinib], [extra_id]) VALUES (N'Etibarnamə (Yanacaq doldurulması barədə)', '1', '1', '0', 'etibarname')");
    DB::query("INSERT INTO [prospect_dpb].[dbo].[tb_prodoc_muraciet_tip] ([ad], [serbest], [emeliyyat], [silinib], [extra_id]) VALUES (N'İcra müddətinin dəyişdirilməsi', '1', '1', '0', 'icra_muddeti')");

    DB::query("INSERT INTO [prospect_dpb].[dbo].[tb_prodoc_daxil_olma_menbeleri] ([name], [deleted], [key]) VALUES (N'Fiziki şəxs daxil olma mənbələri', '0','fiziki_shexs')");
    DB::query("INSERT INTO [prospect_dpb].[dbo].[tb_prodoc_daxil_olma_menbeleri] ([name], [deleted], [key]) VALUES (N'Qurumlardan', '0','qurum')");

    DB::query("INSERT INTO [prospect_dpb].[dbo].[tb_prodoc_muraciet_eden_tip] ([name], [extra_id]) VALUES (N'Şəxs', 'person')");
    DB::query("INSERT INTO [prospect_dpb].[dbo].[tb_prodoc_muraciet_eden_tip] ([name]) VALUES (N'Kollektiv')");
    DB::query("INSERT INTO [prospect_dpb].[dbo].[tb_prodoc_muraciet_eden_tip] ([name]) VALUES (N'İmzasız')");

    require_once DIRNAME_INDEX . 'prodoc/init/tb_prodoc_inner_document_type.php';
    require_once DIRNAME_INDEX . 'prodoc/init/tb_semed_novleri.php';

    DB::commit();
} catch (Exception $e) {
    DB::rollback();
}