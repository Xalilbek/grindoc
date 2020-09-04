<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 24.09.2018
 * Time: 16:17
 */
defineView("v_daxil_olan_senedler_elave_shexsler", "
    SELECT
        DS.id,
        (
            CASE
            WHEN DS.group_id IS NULL THEN
                DS.user_id
            ELSE
                GROUP_USER.user_id
            END
        ) user_id,
        DS.daxil_olan_sened_id,
        DS.tip,
        DS.group_id
    FROM
        tb_daxil_olan_senedler_elave_shexsler DS
    LEFT JOIN tb_prodoc_group_user GROUP_USER ON DS.group_id = GROUP_USER.group_id
");

defineView("v_derkenar_elave_shexsler", "
    SELECT
        DS.id,
        (
            CASE
            WHEN DS.group_id IS NULL THEN
                DS.user_id
            ELSE
                GROUP_USER.user_id
            END
        ) user_id,
        DS.derkenar_id,
        DS.tip,
        DS.group_id
    FROM
        tb_derkenar_elave_shexsler DS
    LEFT JOIN tb_prodoc_group_user GROUP_USER ON DS.group_id = GROUP_USER.group_id
");

defineView("v_prodoc_outgoing_document_relation", "
    SELECT
	t2.*, t1.outgoing_document_id
    FROM
        tb_prodoc_appeal_outgoing_document AS t1
    LEFT JOIN tb_prodoc_muraciet AS t2 ON t2.id = t1.appeal_id
");

defineView("v_prodoc_muraciet", "
    SELECT
	t1.id,
	t1.derkenar_id,
	(CASE WHEN t1.derkenar_id > 0 THEN t2.daxil_olan_sened_id ELSE t1.daxil_olan_sened_id END) AS daxil_olan_sened_id,
	t1.created_at,
	t1.netice_id,
	t1.tip,
	t1.note,
	t1.status,
	t1.created_by,
	t1.related_document_id,
	t1.dos_status
    FROM
        tb_prodoc_muraciet AS t1
    LEFT JOIN tb_derkenar AS t2 ON t2.id = t1.derkenar_id
");

defineView("v_prodoc_document_number", "
    SELECT
	doc_number.id,
	doc_number.document_number_pattern_id,
	doc_number.year,
	doc_number.serial_number,
	doc_number.additional_serial_number,
	doc_number.parent_id,
	doc_number.sender_person_id,
	doc_number.created_at,
  (CASE WHEN approved = 0 THEN '-' ELSE doc_number.document_number END) AS document_number,
	doc_number.set_number_after_approval,
	doc_number.approved
  FROM tb_prodoc_document_number AS doc_number
");

defineView("v_daxil_olan_senedler_corrected", "
    WITH Vars (IdDocumentTypeAnswerToPoll, IdDocumentTypeRelatedDocument, IdPollAppeal)
  AS (
    SELECT
			(SELECT TOP 1 id FROM tb_mektubun_tipleri WHERE sened_tip = 'poll_answer') AS IdDocumentTypeAnswerToPoll,
			(SELECT TOP 1 id FROM tb_mektubun_tipleri WHERE sened_tip = 'related_document') AS IdDocumentTypeRelatedDocument,
			(SELECT TOP 1 id FROM tb_prodoc_muraciet_tip WHERE extra_id = 'sorgu') AS IdPollAppeal
  )

    SELECT
	d.document_number AS document_number,
	qisa_mezmun.name AS qisa_mezmun_name,
	d.document_number_id,
	d.id,
	d.qisa_mezmun_id,
	d.senedin_nomresi,
	d.senedin_daxil_olma_tarixi,
	d.mektubun_alt_tipi,
	d.vereq_sayi,
	d.qoshma_sayi,
	d.gonderen_teshkilatin_nomresi,
	d.senedin_tarixi,
	d.mektub_nezaretdedir,
	d.mektubun_qisa_mezmunu,
	d.son_icra_tarixi,
	d.daxil_olma_yolu_id,
	d.created_by,
	d.created_at,
	d.is_deleted,
	d.derkenar_metn_id,
	d.TenantId,
	d.status,
	d.rey_muellifi,
	d.yoxlayan_shexs,
	d.tip,
	d.netice,
	d.outgoing_document_id,
	d.icra_edilme_tarixi,
	d.state,
	d.internal_document_type_id,
	d.sened_tip,
	d.baglanma_tarixi,
	d.gonderen_aidiyyati_tabeli_id,
	o.name as gonderen_aidiyyati_tabeli_ad,
	d.state_before_canceled,
	d.tibb_muessisesi,
	d.nazalogiya,
	d.mektubun_tipi_third,
	d.mektubun_mezmunu,
	(
		CASE WHEN d.outgoing_document_id > 0
			THEN (
				CASE WHEN c.muraciet_tip_id = (SELECT IdPollAppeal FROM Vars)
					THEN (SELECT IdDocumentTypeAnswerToPoll FROM Vars)
					ELSE (SELECT IdDocumentTypeRelatedDocument FROM Vars)
				END
			)
			ELSE d.mektubun_tipi
		END
	) AS mektubun_tipi,
	(
		CASE WHEN (d.outgoing_document_id > 0 AND (c.teyinat = 3))
			THEN c.gonderen_teshkilat
			ELSE d.gonderen_teshkilat
		END
	) AS gonderen_teshkilat,
	(
		CASE WHEN (d.outgoing_document_id > 0 AND (c.teyinat = 3))
			THEN c.gonderen_shexs
			ELSE d.gonderen_shexs
		END
	) AS gonderen_shexs,
	c.gonderen_shexs AS outgoing_document_person_id,
	y.name as daxil_olma_yolu_ad
    FROM
        tb_daxil_olan_senedler d
    LEFT JOIN tb_chixan_senedler c ON d.outgoing_document_id = c.id
    LEFT JOIN tb_prodoc_aidiyyati_tabeli_qurum o on o.id=d.gonderen_aidiyyati_tabeli_id
    LEFT JOIN tb_prodoc_nazalogiya qisa_mezmun ON qisa_mezmun.id = d.qisa_mezmun_id
    LEFT JOIN tb_daxil_olma_yollari y ON y.id = d.daxil_olma_yolu_id
");

defineView("v_daxil_olan_senedler", "
        SELECT
        tb1.*, (
            SELECT
                Adi
            FROM
                tb_CustomersCompany
            WHERE
                id = tb1.gonderen_teshkilat
        ) AS gonderen_teshkilat_ad,
        (
            SELECT
                CONCAT (Adi, ' ', Soyadi)
            FROM
                tb_Customers
            WHERE
                id = tb1.gonderen_shexs
        ) AS gonderen_shexs_ad,
        (
            SELECT
                CONCAT (Adi, ' ', Soyadi)
            FROM
                tb_users
            WHERE
                USERID = tb1.created_by
        ) AS created_by_name,
        (
            SELECT
                CONCAT (Adi, ' ', Soyadi)
            FROM
                tb_users
            WHERE
                USERID = tb1.rey_muellifi
        ) AS rey_muellifi_ad,
        tb2.ad AS mektubun_tipi_ad,
        tb3.ad AS mektubun_alt_tipi_ad,
        t.ad as tibb_muessiseleri_ad,
        t.dovlet as tibb_muessiseleri_tip,
        n.name as nazalogiya_ad,
        m.ad as mektubun_tipi_third_ad,
        mt.ad as mektubun_mezmunu_ad
    FROM
        v_daxil_olan_senedler_corrected AS tb1
    LEFT JOIN tb_daxil_olan_senedler_fiziki join_fiziki ON join_fiziki.daxil_olan_sened_id = tb1.id
    LEFT JOIN tb_mektubun_tipleri tb2 ON tb2.id = tb1.mektubun_tipi
    LEFT JOIN tb_mektubun_tipleri tb3 ON tb3.id = tb1.mektubun_alt_tipi
    LEFT JOIN tb_prodoc_tibb_muessiseleri t ON t.id = tb1.tibb_muessisesi
    LEFT JOIN tb_prodoc_nazalogiya n ON n.id = tb1.nazalogiya
    LEFT JOIN tb_mektubun_tipleri m ON m.id = tb1.mektubun_tipi_third
    LEFT JOIN tb_mektubun_tipleri mt ON mt.id = tb1.mektubun_mezmunu
");

defineView("v_incoming_document_all", "
    SELECT
	tb1.*, (
		SELECT
			Adi
		FROM
			tb_CustomersCompany
		WHERE
			id = tb1.gonderen_teshkilat
	) AS gonderen_teshkilat_ad,
	(
		SELECT
			CONCAT (Adi, ' ', Soyadi, ' ', AtaAdi)
		FROM
			tb_Customers
		WHERE
			id = tb1.gonderen_shexs
	) AS gonderen_shexs_ad,
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
	tb2.ad AS mektubun_tipi_ad,
	tb3.ad AS mektubun_alt_tipi_ad,
	join_fiziki.muraciet_eden_tip_id,
	join_fiziki.muraciet_eden,
	tb4.Adi    AS muraciet_eden_Adi,
	tb4.Soyadi AS muraciet_eden_Soyadi
    FROM
        v_daxil_olan_senedler_corrected AS tb1
    LEFT JOIN tb_daxil_olan_senedler_fiziki join_fiziki ON join_fiziki.daxil_olan_sened_id = tb1.id
    LEFT JOIN tb_mektubun_tipleri tb2 ON tb2.id = tb1.mektubun_tipi
    LEFT JOIN tb_mektubun_tipleri tb3 ON tb3.id = tb1.mektubun_alt_tipi
    LEFT JOIN tb_Customers        tb4 ON tb4.id = join_fiziki.muraciet_eden
");

defineView("v_derkenar", "
    SELECT
	tb1.*,
	tb2.user_ad_qisa AS mesul_shexs_ad,
	tb3.name AS derkenar_metn_ad,
	tb4.rey_muellifi
    FROM
    tb_derkenar tb1
    LEFT JOIN v_user_adlar           AS tb2 ON tb2.USERID = tb1.mesul_shexs
    LEFT JOIN tb_derkenar_metnler    AS tb3 ON tb3.id = tb1.derkenar_metn_id
    LEFT JOIN tb_daxil_olan_senedler AS tb4 ON tb4.id = tb1.daxil_olan_sened_id
");

defineView("v_daxil_olan_senedler_fiziki", "
    SELECT
    tb1.id,
    tb1.muraciet_eden_tip_id,
    tb1.muraciet_eden,
    tb1.unvan,
    tb1.region,
    tb1.telefon,
    tb1.shexsiyyet_vesiqesi_teqdim_edilmeyib,
    tb1.shexsiyyet_vesiqesi_seria,
    tb1.shexsiyyet_vesiqesi_pin_kod,
    tb1.hardan_daxil_olub,
    tb1.movzu,
    tb1.daxil_olan_sened_id,
    tb1.tekrar_eyni,
    tb1.tekrar_eyni_sened_id,
    CONCAT(tb2.Adi, ' ', tb2.Soyadi, ' ', tb2.AtaAdi) AS muraciet_eden_ad,
    tb3.name AS region_ad,
    tb4.name AS hardan_daxil_olub_ad,
    tb5.ad AS movzu_ad,
    tb6.gonderen_teshkilat AS gonderen_teshkilat,
    tb6.gonderen_teshkilat_ad AS gonderen_teshkilat_ad,
    tb6.created_by,
    tb6.created_at,
    tb6.document_number AS fiziki_document_number,
    tb7.name as muraciet_eden_tip_ad
    
    FROM
    dbo.tb_daxil_olan_senedler_fiziki AS tb1
    LEFT JOIN dbo.tb_Customers AS tb2 ON tb2.id = tb1.muraciet_eden
    LEFT JOIN dbo.tb_prodoc_regionlar AS tb3 ON tb3.id = tb1.region
    LEFT JOIN dbo.tb_prodoc_daxil_olma_menbeleri AS tb4 ON tb4.id = tb1.hardan_daxil_olub
    LEFT JOIN dbo.tb_mektubun_tipleri AS tb5 ON tb5.id = tb1.movzu
    LEFT JOIN dbo.v_daxil_olan_senedler AS tb6 ON tb6.id = tb1.daxil_olan_sened_id
    LEFT JOIN dbo.tb_prodoc_muraciet_eden_tip AS tb7 ON tb7.id = tb1.muraciet_eden_tip_id
");


defineView("v_chixan_senedler_eleva_melumat", "
    SELECT
	ISNULL(tb5.document_number, '-') AS document_number,
	tb1.*,
	qisa_mezmun.name AS qisa_mezmun_name,
	tb3.daxil_olan_sened_id AS daxil_olan_sened_id,
	tb3.daxil_olan_sened_id AS derkenar_id,
	tb3.netice_id           AS muraciet_netice_id,
	(
		CASE
			WHEN tb1.teyinat = '5' THEN N'Tabeli quruma'
			WHEN tb1.teyinat = '3' THEN N'Aidiyyatı orqana'
			ELSE N'Fiziki şəxsə'
		END
	) AS teyinat_ad,
	(
		CASE
			WHEN tb1.teyinat = '5' 
			THEN (SELECT [name] FROM tb_prodoc_qurumlar WHERE id = tb1.gonderen_teshkilat )
			WHEN tb1.teyinat = '3' 
			THEN (SELECT Adi FROM tb_CustomersCompany WHERE id = tb1.gonderen_teshkilat )
			ELSE N'Fiziki şəxs'
		END
	) AS gonderen_teshkilat_ad,
	(
		CASE
			WHEN tb1.teyinat = '5' 
			THEN ( SELECT CONCAT(Soyadi,' ',Adi,' ',AtaAdi) AS ad FROM tb_users WHERE USERID = tb1.gonderen_shexs )
			ELSE ( SELECT CONCAT (Adi, ' ', Soyadi, ' ', AtaAdi) FROM tb_Customers WHERE id = tb1.gonderen_shexs )
		END
	) AS gonderen_shexs_ad,
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
			USERID = tb1.arayis_user_id
	) AS arayis_user_id_ad,
	(
		SELECT
			CONCAT (Adi, ' ', Soyadi, ' ', AtaAdi)
		FROM
			tb_users
		WHERE
			USERID = tb1.etibarname_sexs
	) AS etibarname_sexs_ad,
	(
		SELECT
			CONCAT (Adi, ' ', Soyadi, ' ', AtaAdi)
		FROM
			tb_users
		WHERE
			USERID = tb1.etibarname_icraci_direktor
	) AS etibarname_icraci_direktor_ad,
	(
		SELECT
			CONCAT (Adi, ' ', Soyadi, ' ', AtaAdi)
		FROM
			tb_users
		WHERE
			USERID = tb1.etibarname_bas_muhasib
	) AS etibarname_bas_muhasib_ad,
	(
		SELECT
			[ad]
		FROM
			tb_mektubun_tipleri
		WHERE
			id = tb1.senedin_novu
	) AS senedin_novu_ad,
	CONCAT (tb2.Adi, ' ', tb2.Soyadi, ' ', tb2.AtaAdi) AS kim_gonderir_ad,
	tb4.ad AS muraciet_tip_ad,
	tb4.extra_id AS muraciet_tip_extra_id,
	tb6.ad AS muraciet_alt_tip_ad
    FROM
        tb_chixan_senedler tb1
    LEFT JOIN tb_users tb2 ON tb2.USERID = tb1.kim_gonderir
    LEFT JOIN tb_prodoc_muraciet tb3     ON tb3.id = tb1.muraciet_id
    LEFT JOIN tb_prodoc_muraciet_tip AS tb4 ON tb4.id = tb1.muraciet_tip_id
    LEFT JOIN tb_prodoc_nazalogiya qisa_mezmun ON qisa_mezmun.id = tb1.qisa_mezmun_id
    LEFT JOIN v_prodoc_document_number AS tb5 ON tb5.id = tb1.document_number_id
    LEFT JOIN tb_prodoc_muraciet_alt_tipler AS tb6 ON tb6.id = tb1.muraciet_alt_tip_id
");

defineView("v_chixan_senedler", "
    SELECT
	ISNULL(tb5.document_number, '-') AS document_number,
	tb1.*,
	tb3.daxil_olan_sened_id AS daxil_olan_sened_id,
	tb3.daxil_olan_sened_id AS derkenar_id,
	tb3.netice_id           AS muraciet_netice_id,
	(
		CASE
			WHEN tb1.teyinat = '5' THEN N'Tabeli quruma'
			WHEN tb1.teyinat = '3' THEN N'Aidiyyatı orqana'
			ELSE N'Fiziki şəxsə'
		END
	) AS teyinat_ad,
	(
		CASE
			WHEN tb1.teyinat = '5' 
			THEN (SELECT [name] FROM tb_prodoc_qurumlar WHERE id = tb1.gonderen_teshkilat )
			WHEN tb1.teyinat = '3' 
			THEN (SELECT Adi FROM tb_CustomersCompany WHERE id = tb1.gonderen_teshkilat )
			ELSE N'Fiziki şəxs'
		END
	) AS gonderen_teshkilat_ad,
	(
		CASE
			WHEN tb1.teyinat = '5' 
			THEN ( SELECT CONCAT(Soyadi,' ',Adi,' ',AtaAdi) AS ad FROM tb_users WHERE USERID = tb1.gonderen_shexs )
			ELSE ( SELECT CONCAT (Adi, ' ', Soyadi, ' ', AtaAdi) FROM tb_Customers WHERE id = tb1.gonderen_shexs )
		END
	) AS gonderen_shexs_ad,
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
			USERID = tb1.arayis_user_id
	) AS arayis_user_id_ad,
	(
		SELECT
			[ad]
		FROM
			tb_mektubun_tipleri
		WHERE
			id = tb1.senedin_novu
	) AS senedin_novu_ad,
	CONCAT (tb2.Adi, ' ', tb2.Soyadi, ' ', tb2.AtaAdi) AS kim_gonderir_ad,
	tb4.ad AS muraciet_tip_ad,
	tb4.extra_id AS muraciet_tip_extra_id,
	tb6.ad AS muraciet_alt_tip_ad,
	4 AS umumi_tip
    FROM
        tb_chixan_senedler tb1
    LEFT JOIN tb_users tb2 ON tb2.USERID = tb1.kim_gonderir
    LEFT JOIN tb_prodoc_muraciet tb3     ON tb3.id = tb1.muraciet_id
    LEFT JOIN tb_prodoc_muraciet_tip AS tb4 ON tb4.id = tb1.muraciet_tip_id
    LEFT JOIN v_prodoc_document_number AS tb5 ON tb5.id = tb1.document_number_id
    LEFT JOIN tb_prodoc_muraciet_alt_tipler AS tb6 ON tb6.id = tb1.muraciet_alt_tip_id
");

defineView("v_daxil_olan_senedler_dashboard", "
    SELECT tb1.*, tb1.tip AS umumi_tip
    FROM tb_daxil_olan_senedler AS tb1
");

