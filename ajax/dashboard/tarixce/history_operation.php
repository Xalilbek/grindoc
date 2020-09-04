<?php

    $imzalayanGonderenAd = 'İmzalayan şəxs';

    $operationTextMap = [
        'create_approve_group_redakt_eden'       => 'Sənəd redaktəyə göndərildi',
        'create_approve_group_visa_veren'        => 'Sənəd vizaya göndərildi',
        'create_approve_group_razilashdiran'     => 'Sənəd razılaşdırmaya göndərildi',
        'create_approve_group_chap_eden'         => 'Sənəd çapa göndərildi',
        'create_approve_group_umumi_shobe'       => dsAlt('2616tarixce_umumi_shobeye_gonderildi', "SƏNƏD ÜMÜMİ ŞÖBƏYƏ GÖNDƏRİLDİ"),
        'create_approve_group_umumi_shobe_nomre' => 'Sənədin nömrəni qeyd etmək üçün sənəd ümümi şöbəyə göndərildi',
        'create_approve_group_rey_muelifi'       => dsAlt('2616tarixce_rey_muellifine_gonderildi', "SƏNƏD RƏY MÜƏLLİFİNƏ GÖNDƏRİLDİ"),
        'create_approve_group_kim_gonderir'      => dsAlt('2616tarixce_imzayalana_gonderildi', "SƏNƏD İMZALAYAN ŞƏXSƏ GÖNDƏRİLDİ"),
        'create_approve_group_hesabat_ver_pts'   => 'Hesabata göndərildi',
        'create_approve_group_tesdiqleme'        => dsAlt('2616tarixce_teqdiqlenmeye_gonderildi', "SƏNƏD TƏSDİQLƏMƏYƏ GÖNDƏRİLDİ"),
        'create_approve_group_derkenar'          => 'Sənəd dərkənara göndərildi',
        'create_approve_group_neticeni_qeyd_eden_sexs'    => dsAlt('2616tarixce_netice_qeyd_gonderildi', "NƏTİCƏNİ QEYD ETMƏK ÜÇÜN GÖNDƏRİLDİ"),
        'create_approve_group_qiymetlendirme'    => dsAlt('2616tarixce_qiymetlendirmeye_gonderildi', "SƏNƏD QİYMƏTLƏNDİRMƏYƏ GÖNDƏRİLDİ"),
        'create_approve_group_melumatlandirma'   => dsAlt('2616tarixce_melumatlanacaq_shexslere_gonderildi', "SƏNƏD MƏLUMATLANACAQ ŞƏXSLƏRƏ GÖNDƏRİLDİ"),
        'create_approve_group_sedr'              => dsAlt('2616tarixce_sedre_gonderildi', "SƏNƏD SƏDRƏ GÖNDƏRİLDİ"),
        'create_approve_group_mesul_shexs_testiq'  => 'Sənəd icraçıya göndərildi',

        'create_approve_group_kurator'    => sprintf('Sənəd həmicraçı şəxslərə göndərildi', $imzalayanGonderenAd),
        'create_approve_group_ishtrakchi' => dsAlt('2616tarixce_nezaret_edenlere_gonderildi', "SƏNƏD NƏZARƏT EDƏN ŞƏXSLƏRƏ GÖNDƏRİLDİ"),

        'legv_et'                   => dsAlt('2616tarixce_legv_olunub', "SƏNƏD LƏĞV OLUNUB"),
        'chixan_sened_gonder'       => dsAlt('2616tarixce_gonderilib', "SƏNƏD GÖNDƏRİLİB"),
        'answer_is_not_required'    => 'Sənədə cavab tələb olunmamağı haqda məlumat verildi',
        'approve_redakt_eden'       => 'Redaktə edən şəxs sənədi təsdiq etdi',
        'approve_visa_veren'        => 'Viza verən şəxs sənədi təsdiq etdi',
        'approve_razilashdiran'     => 'Razılaşdıran şəxs sənədi təsdiq etdi',
        'approve_chap_eden'         => 'Çap edən şəxs sənədi çap etdi',
        'approve_rey_muelifi'       => dsAlt('2616tarixce_rey_muellifi_tesdiq', "RƏY MÜƏLLİFİ SƏNƏDİ TƏSDİQ ETDİ"),
        'approve_kurator'           => 'İcraya nəzarət edən şəxs sənədi təsdiq etdi',
        'approve_tanish_ol'         => 'Sənədlə tanış oldu',
        'approve_ishtrakchi'        => 'Həm icraçı sənədi təsdiq etdi',
        'approve_mesul_shexs'       => 'İcraçı sənədi təsdiq etdi',
        'approve_hesabat_ver_pts'   => 'Hesabat verildi',
        'approve_kim_gonderir'      => dsAlt('2616tarixce_imzalayan_shexs', "İMZALAYAN ŞƏXS SƏNƏDİ TƏSDİQ ETDİ"),
        'approve_umumi_shobe'       => dsAlt('2616tarixce_umumi_shobe_tesdiq', "ÜMUMİ ŞÖBƏ SƏNƏDİ TƏSDİQ ETDİ"),
        'send_general_department'       => dsAlt('2616tarixce_umumi_shobeye_gonderildi', "SƏNƏD ÜMÜMİ ŞÖBƏYƏ GÖNDƏRİLDİ"),
        'approve_umumi_shobe_nomre' => 'Ümümi şöbə sənədin nömrəsini qeyd etdi',
        'approve_tesdiqleme'        => dsAlt('2616tarixce_tesdiqlendi', "SƏNƏD TƏSDİQLƏNDİ"),
        'approve_qiymetlendirme'    => dsAlt('2616tarixce_sifarish_qiymetlendirildi', "SİFARİŞ QİYMƏTLƏNDİRİLDİ"),
        'approve_tesdiq_sifaris'    => 'Sifarişçi şəxs sənədi təhvil aldı',
        'approve_neticeni_qeyd_eden_sexs'    => dsAlt('2616tarixce_netice_qeyd', "SƏNƏDİN NƏTİCƏSİ QEYD EDİLDİ"),
        'approve_mezuniyet_emri'    => 'Məzuniyyət əmri hazırlandı',
        'approve_ezamiyet_emri'     => 'Ezamiyyət əmri hazırlandı',
        'approve_melumatlandirma'   => 'Sənədlə tanış oldu',
        'approve_sedr'              => 'Sədr sənədi təsdiq etdi',
        'approve_mesul_shexs_testiq'  => 'İcraçı sənədi təsdiq etdi',

        'cancel_tesdiqleme'     => 'Sənəddən imtina edildi',
        'cancel_redakt_eden'    => 'Redaktə edən şəxs sənəddən imtina etdi',
        'cancel_visa_veren'     => 'Viza verən şəxs sənəddən imtina etdi',
        'cancel_razilashdiran'  => 'Razılaşdıran şəxs sənəddən imtina etdi',
        'cancel_chap_eden'      => 'Çap edən şəxs sənəddən imtina etdi',
        'cancel_rey_muelifi'    => dsAlt('2616tarixce_rey_muellifi_imtina', "RƏY MÜƏLLİFİ SƏNƏDDƏN İMTİNA ETDİ"),
        'cancel_kurator'        => dsAlt('2616tarixce_icraya_nezaretchi_imtina', "İCRAYA NƏZARƏT EDƏN ŞƏXS SƏNƏDDƏN İMTİNA ETDİ"),
        'cancel_mesul_shexs'    => 'İcraçı sənəddən imtina etdi',
        'cancel_kim_gonderir'   => dsAlt('2616tarixce_imzayalan_imtina', "İMZALAYAN ŞƏXS SƏNƏDDƏN İMTİNA ETDİ"),
        'cancel_umumi_shobe'    => dsAlt('2616tarixce_umumi_shobe_imtina', "ÜMUMİ ŞÖBƏ SƏNƏDDƏN İMTİNA ETDİ"),
        'cancel_ishtrakchi'     => 'Həm icraçı sənəddən imtina etdi',
        'cancel_sedr'           => 'Sədr sənəddən imtina etdi',
        'cancel_mesul_shexs_testiq'  => 'İcraçı sənəddən imtina etdi',
        'approve'               => 'Sənəd təsdiq olundu',
        'reject'                => dsAlt('2616tarixce_imtina_olundu', "SƏNƏDDƏN İMTİNA OLUNDU"),

        'create_approve_group_umumi_shobe_netice' => 'Sənəd ümumi şöbəyə nəticəni qeyd etmək üçün göndərildi',
        'approve_umumi_shobe_netice' => 'Ümümi şöbə sənədin nəticəsini qeyd etdi',

        'create_approve_group_qeydiyyatchi_netice' => 'Sənəd qeydiyyatçıya nəticəni qeyd etmək üçün göndərildi',
        'approve_qeydiyyatchi_netice' => 'Qeydiyyatçı sənədin nəticəsini qeyd etdi',
        'delete' => 'Sənəd silindi',
    ];

    $relatedKeyAndOperationTextMap = [
        'document_registration'                         => dsAlt('2616tarixce_qeydiyyatdan_kecib',  "SƏNƏD QEYDİYYATDAN KEÇİRİLİB"),
        'document_edit'                                 => 'Sənədə düzəliş edilib',
        'document_yoxlamaya_gonderildi'                 => 'Sənəd yoxlamaya göndərildi',
        'document_yoxlayan_testiq'                      => dsAlt('2616tarixce_yoxlayan_shexs_tesdiq', "YOXLAYAN ŞƏXS SƏNƏDİ TƏSDİQ ETDİ"),
        'yoxlayan_testiq'                               => dsAlt('2616tarixce_yoxlayan_shexs_tesdiq', "YOXLAYAN ŞƏXS SƏNƏDİ TƏSDİQ ETDİ"),
        'document_rey_muelife_gonderildi'               => dsAlt('2616tarixce_rey_muellifine_gonderildi', "SƏNƏD RƏY MÜƏLLİFİNƏ GÖNDƏRİLDİ"),
        'document_rey_muelifi_testiq'                   =>  dsAlt('2616tarixce_yoxlayan_shexs_tesdiq', "YOXLAYAN ŞƏXS SƏNƏDİ TƏSDİQ ETDİ"),
        'document_state_changed_to_4_by_yoxlayan_shexs' => dsAlt('2616tarixce_yoxlayan_imtina', "YOXLAYAN ŞƏXS SƏNƏDDƏN İMTİNA ETDİ"),
        'document_state_changed_to_4_by_rey_muellifi'   => dsAlt('2616tarixce_rey_muellifi_imtina', "RƏY MÜƏLLİFİ SƏNƏDDƏN İMTİNA ETDİ"),
        'task_registration' 							=> dsAlt('2616tarixce_derkenar_yazildi', "DƏRKƏNAR YAZILDI"),
        'alt_derkenar' 							        => dsAlt('2616tarixce_alt_derkenar_yazildi', "ALT DƏRKƏNAR YAZILDI"),
        'task_edit' 							        => 'Dərkənara düzəliş edildi',
        'task_status_change_to_2'						=> 'Dərkənar icraya götürüldü',
        'task_status_change_to_3'						=> 'Dərkənardan imtina edildi',
        'appeal_registration' 							=> 'Ümumi forma qeydiyyatdan keçirilib',
        'appeal_ishe_tikilsin' 							=> 'Sənəd şərhlə bağlandı',
        'outgoing_document_registration'				=> 'Sənəd %s göndərildi',
        'outgoing_document_edit'				        => 'Sənədə düzəliş edilərək %s göndərildi',
    ];