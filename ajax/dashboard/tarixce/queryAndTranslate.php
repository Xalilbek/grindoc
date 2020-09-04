<?php

function tarixceNe($tip = 'daxil_olan_sened', $ne = '') {

    switch ($tip) {
        case 'daxil_olan_sened':
            $ne_mesaj = Document::getLogMessage($ne);
            break;

        case 'daxili_sened_mezuniyyet':
        case 'daxili_sened_ezamiyyet':
        case 'daxili_sened_xestelik_vereqi':
        case 'daxili_sened_icazeler':
            $ne_mesaj = dil::soz($ne);
            break;

        default:
            $ne_mesaj = $ne;
            break;
    }

    return $ne_mesaj;
}

function sorguTip($tip, $sened_id) {
    $sql = '';

    switch ($tip) {
        case 'arayish':
            $sql = "SELECT loglar.*,v_user_adlar.user_ad
		FROM (SELECT '$sened_id' AS id) AS certificate
		OUTER APPLY (
		SELECT employe AS user_id,date AS [date],N'Sənəd qeydiyyatdan keçirilib' AS ne, '' AS text FROM tb_prodoc_certificate WHERE id=certificate.id
		UNION ALL
		SELECT tb2.user_id,tb2.date AS [date],CASE ne WHEN 1 THEN N'Dəyişdirilib' WHEN 3 THEN N'Təsdiqlənib' WHEN 4 THEN N'İmtina edilib' WHEN 5 THEN N'1 günlük əməkhaqqıya dəyişiklik' ELSE CAST(ne AS VARCHAR) END AS ne,qeyd FROM tb_prodoc_certificate tb1
		LEFT JOIN tb_prodoc_certificate_logs tb2 ON tb2.order_id=tb1.id
		WHERE tb1.id=certificate.id
		) loglar
		LEFT JOIN v_user_adlar ON loglar.user_id=v_user_adlar.USERID
		ORDER BY loglar.[date] ASC;";
            break;
        case 'mezuniyet_erizesi':
            $sql = "SELECT loglar.*,
                v_user_adlar.user_ad
              FROM (
                SELECT '$sened_id' AS id) AS mezuniyyet
                OUTER APPLY (
                  SELECT elave_eden_user AS user_id,
                    date AS [date],
                    N'Sənəd qeydiyyatdan keçirilib' AS ne,
                    about AS [text] 
                  FROM tb_mezuniyyetler 
                  WHERE id=mezuniyyet.id
                    UNION ALL
                  SELECT tb2.user_id,
                    tb2.date AS [date],
                    CASE ne 
                      WHEN 1 THEN '158changed' 
                      WHEN 3 THEN '158testiqlenib' 
                      WHEN 4 THEN '158imtinaedilib edilib' 
                      WHEN 5 THEN '158birgunlukemekhaqqiyadeyishiklik' 
                      ELSE CAST(ne AS VARCHAR) 
                    END AS ne,
                    qeyd as [text]
                  FROM tb_mezuniyyetler tb1
                  LEFT JOIN tb_mezuniyyetler_logs tb2 ON tb2.mezuniyyet_id=tb1.id
                  WHERE tb1.id=mezuniyyet.id
                ) loglar
              LEFT JOIN v_user_adlar ON loglar.user_id=v_user_adlar.USERID
              ORDER BY loglar.[date] ASC";
            break;


        case 'daxili_sened_salary_advance':
            $sql="SELECT loglar.*,v_user_adlar.user_ad
		FROM (SELECT '$sened_id' AS id) AS emekhaqqi_avans OUTER APPLY (
		SELECT elave_eden_user AS user_id,tarix as date,N'Sənəd qeydiyyatdan keçirilib' AS ne,'' AS [text] FROM tb_emekhaqqi_avansi WHERE id=emekhaqqi_avans.id
		UNION ALL
		SELECT tb2.user_id,tb2.date AS date,CASE ne WHEN 1 THEN N'Dəyişdirilib' WHEN 3 THEN N'Təsdiqlənib' WHEN 4 THEN N'İmtina edilib' ELSE CAST(ne AS VARCHAR) END AS ne,qeyd FROM tb_avanslar tb1
		INNER JOIN tb_emekhaqqi_avansi_logs tb2 ON tb2.avans_id=tb1.id
		WHERE tb1.id=emekhaqqi_avans.id  
		) loglar
		LEFT JOIN v_user_adlar ON loglar.user_id=v_user_adlar.USERID
		ORDER BY loglar.date ASC";
            break;
        case 'daxili_sened_advance_report':
            $sql="SELECT loglar.*,v_user_adlar.user_ad
		FROM (SELECT '$sened_id' AS id) AS avans_hesabat OUTER APPLY (
		SELECT elave_etdi AS user_id,tarix as date,N'Sənəd qeydiyyatdan keçirilib' AS ne,'' AS [text] FROM tb_avans_hesabat WHERE id=avans_hesabat.id
		UNION ALL
		SELECT tb2.user_id,tb2.date AS date,CASE ne WHEN 1 THEN N'Dəyişdirilib' WHEN 3 THEN N'Təsdiqlənib' WHEN 4 THEN N'İmtina edilib' ELSE CAST(ne AS VARCHAR) END AS ne, tb2.qeyd FROM tb_avans_hesabat tb1
		INNER JOIN tb_avans_hesabat_log tb2 ON tb2.avans_h_id=tb1.id
		WHERE tb1.id=avans_hesabat.id  
		) loglar
		LEFT JOIN v_user_adlar ON loglar.user_id=v_user_adlar.USERID
		ORDER BY loglar.date ASC";
            break;
        case 'daxili_sened_advance_request':
        $sql="SELECT loglar.*,v_user_adlar.user_ad
		FROM (SELECT '$sened_id' AS id) AS avanslar OUTER APPLY (
		SELECT elave_eden_user AS user_id,tarix as date,N'Sənəd qeydiyyatdan keçirilib' AS ne,'' AS [text] FROM tb_avanslar WHERE id=avanslar.id
		UNION ALL
		SELECT tb2.user_id,tb2.date AS date,CASE ne WHEN 1 THEN N'Dəyişdirilib' WHEN 3 THEN N'Təsdiqlənib' WHEN 4 THEN N'İmtina edilib' ELSE CAST(ne AS VARCHAR) END AS ne,qeyd FROM tb_avanslar tb1
		INNER JOIN tb_avanslar_logs tb2 ON tb2.avans_id=tb1.id
		WHERE tb1.id=avanslar.id  
		) loglar
		LEFT JOIN v_user_adlar ON loglar.user_id=v_user_adlar.USERID
		ORDER BY loglar.date ASC";
        break;
        case 'daxili_sened_termination_petition':
            $sql = "SELECT loglar.*,v_user_adlar.user_ad
		FROM (SELECT '$sened_id' AS id) AS prodoc_formlar_xitam_erizesi OUTER APPLY (
		SELECT elave_edib AS user_id,tarix as date,N'Sənəd qeydiyyatdan keçirilib' AS ne,'' AS [text] FROM tb_prodoc_formlar_xitam_erizesi WHERE id=prodoc_formlar_xitam_erizesi.id
		UNION ALL
		SELECT tb2.user_id,tb2.date AS date,CASE ne WHEN 1 THEN N'Dəyişdirilib' WHEN 3 THEN N'Təsdiqlənib' WHEN 4 THEN N'İmtina edilib' ELSE CAST(ne AS VARCHAR) END AS ne,qeyd FROM tb_prodoc_formlar_xitam_erizesi tb1
		INNER JOIN tb_prodoc_formlar_logs tb2 ON tb2.document_id=tb1.id
		WHERE tb1.id=prodoc_formlar_xitam_erizesi.id AND tb2.tip = 'prodoc_formlar_xitam_erizesi'  
		) loglar
		LEFT JOIN v_user_adlar ON loglar.user_id=v_user_adlar.USERID
		ORDER BY loglar.date ASC";
            break;
        case 'daxili_sened_employee_petition':
        case  'daxili_sened_employee_terminate_petition':
        case  'daxili_sened_other_petition':
            $sql = "SELECT loglar.*,v_user_adlar.user_ad
		FROM (SELECT '$sened_id' AS id) AS petition
		OUTER APPLY (
		SELECT who_registered AS user_id,petition_date AS date,N'Sənəd qeydiyyatdan keçirilib' AS ne,note AS [text] FROM tb_proid_employe_petition WHERE id=petition.id
		UNION ALL
		SELECT tb2.user_id,tb2.date AS date,CASE ne WHEN 1 THEN N'Dəyişdirilib' WHEN 3 THEN N'Təsdiqlənib' WHEN 4 THEN N'İmtina edilib' ELSE CAST(ne AS VARCHAR) END AS ne,qeyd FROM tb_proid_employe_petition tb1
		INNER JOIN tb_proid_employe_petition_logs tb2 ON tb2.petition_id=tb1.id
		WHERE tb1.id=petition.id
		) loglar
		LEFT JOIN v_user_adlar ON loglar.user_id=v_user_adlar.USERID
		ORDER BY loglar.date ASC";
            break;
        case 'daxili_sened_termination_contract':
            $sql = "SELECT loglar.*,v_user_adlar.user_ad
		FROM (SELECT '$sened_id' AS id) AS emrr
		OUTER APPLY (
		SELECT elave_edib AS user_id,elave_olunma_tarixi AS [date],N'Əmr əlavə edilib' AS ne,qeyd as text FROM tb_emrler WHERE id=emrr.id
		UNION ALL
		SELECT user_id,date AS [date],CASE ne WHEN 1 THEN N'Dəyişdirilib' WHEN 3 THEN N'Təsdiqlənib' WHEN 4 THEN N'İmtina edilib' ELSE CAST(ne AS VARCHAR) END AS ne,qeyd FROM tb_emrler_logs WHERE emr_id=emrr.id
		) loglar
		LEFT JOIN v_user_adlar ON loglar.user_id=v_user_adlar.USERID
		ORDER BY loglar.[date] ASC";
            break;
        case 'daxili_sened_vacation_compensation':
            $sql = "SELECT loglar.*,v_user_adlar.user_ad
		FROM (SELECT '$sened_id' AS id) AS emrr
		OUTER APPLY (
		SELECT elave_edib AS user_id,elave_olunma_tarixi AS [date],N'Əmr əlavə edilib' AS ne,qeyd as text FROM tb_emrler WHERE id=emrr.id
		UNION ALL
		SELECT user_id,date AS [date],CASE ne WHEN 1 THEN N'Dəyişdirilib' WHEN 3 THEN N'Təsdiqlənib' WHEN 4 THEN N'İmtina edilib' ELSE CAST(ne AS VARCHAR) END AS ne,qeyd FROM tb_emrler_logs WHERE emr_id=emrr.id
		) loglar
		LEFT JOIN v_user_adlar ON loglar.user_id=v_user_adlar.USERID
		ORDER BY loglar.[date] ASC";
            break;
        case 'daxili_sened_labor_contract':
            $sql = "SELECT loglar.*,v_user_adlar.user_ad
		FROM (SELECT '1' AS id) AS contract
		OUTER APPLY (
            SELECT who_registered AS user_id,contract_date AS [date],N'Sənəd qeydiyyatdan keçirilib' AS ne,note AS text FROM tb_proid_labor_contracts WHERE id=contract.id
		UNION ALL
		SELECT tb2.user_id,tb2.date AS [date],CASE ne WHEN 1 THEN N'Dəyişdirilib' WHEN 3 THEN N'Təsdiqlənib' WHEN 4 THEN N'İmtina edilib' ELSE CAST(ne AS VARCHAR) END AS ne,qeyd FROM tb_proid_labor_contracts tb1
		INNER JOIN tb_proid_labor_contracts_logs tb2 ON tb2.contract_id=tb1.id
		WHERE tb1.id=contract.id
		) loglar
		LEFT JOIN v_user_adlar ON loglar.user_id=v_user_adlar.USERID
		ORDER BY loglar.date ASC";
            break;
        case 'daxili_sened_ezamiyyet':
            $sql = "SELECT loglar.*,
                v_user_adlar.user_ad
              FROM (
                SELECT '$sened_id' AS id) AS ezamiyyet
                OUTER APPLY (
                  SELECT elave_eden_user AS user_id,
                    date AS [date],
                    '158sened_qeydiyyatdan_kechdi' AS ne,
                    about AS [text] 
                  FROM tb_ezamiyyetler 
                  WHERE id=ezamiyyet.id
                    UNION ALL
                  SELECT tb2.user_id,
                    tb2.date AS [date],
                    CASE ne 
                      WHEN 1 THEN '158changed' 
                      WHEN 3 THEN '158testiqlenib' 
                      WHEN 4 THEN '158imtinaedilib' 
                      WHEN 5 THEN '158birgunlukemekhaqqiyadeyishiklik' 
                      ELSE CAST(ne AS VARCHAR) 
                    END AS ne,
                    qeyd AS [text] 
                  FROM tb_ezamiyyetler tb1
                  LEFT JOIN tb_ezamiyyetler_logs tb2 ON tb2.ezamiyyet_id=tb1.id
                  WHERE tb1.id=ezamiyyet.id
                ) loglar
              LEFT JOIN v_user_adlar ON loglar.user_id=v_user_adlar.USERID
              ORDER BY loglar.date ASC";
            break;
        case 'daxili_sened_gorushler':
            $sql = "SELECT loglar.*,v_user_adlar.user_ad
		FROM (SELECT '$sened_id' AS id) AS gorush
		OUTER APPLY (
		SELECT gorushu_teyin_eden_user AS user_id,date AS [date],N'Sənəd qeydiyyatdan keçirilib' AS ne,about AS [text] FROM tb_gorushler WHERE id=gorush.id
		UNION ALL
		SELECT tb2.user_id,tb2.date AS [date],CASE ne WHEN 1 THEN N'Dəyişdirilib' WHEN 2 THEN N'Qəbul edilib' WHEN 3 THEN N'Təsdiqlənib' WHEN 4 THEN N'İmtina edilib' ELSE CAST(ne AS VARCHAR) END AS ne,qeyd AS text FROM tb_gorushler tb1
		LEFT JOIN tb_gorushler_logs tb2 ON tb2.gorush_id=tb1.id
		WHERE tb1.id=gorush.id
		) loglar
		LEFT JOIN v_user_adlar ON loglar.user_id=v_user_adlar.USERID
		ORDER BY loglar.date ASC";
            break;
        case 'daxili_sened_employment':
            $sql = "SELECT loglar.*,v_user_adlar.user_ad
		FROM (SELECT '$sened_id' AS id) AS emrr
		OUTER APPLY (
		SELECT elave_edib AS user_id,elave_olunma_tarixi AS [date],N'Əmr əlavə edilib' AS ne,qeyd as text FROM tb_emrler WHERE id=emrr.id
		UNION ALL
		SELECT user_id,date AS [date],CASE ne WHEN 1 THEN N'Dəyişdirilib' WHEN 3 THEN N'Təsdiqlənib' WHEN 4 THEN N'İmtina edilib' ELSE CAST(ne AS VARCHAR) END AS ne,qeyd FROM tb_emrler_logs WHERE emr_id=emrr.id
		) loglar
		LEFT JOIN v_user_adlar ON loglar.user_id=v_user_adlar.USERID
		ORDER BY loglar.[date] ASC";
            break;
        case 'daxili_sened_business_trip':
            $sql = "
            SELECT loglar.*,v_user_adlar.user_ad
		FROM (SELECT '30' AS id) AS business_trip
		OUTER APPLY (
            SELECT '' AS user_id,date AS [date],N'Sənəd qeydiyyatdan keçirilib' AS ne, base AS [text] FROM tb_prodoc_business_trip WHERE id=business_trip.id
		UNION ALL
		SELECT tb2.user_id,tb2.date AS [date],CASE ne WHEN 1 THEN N'Dəyişdirilib' WHEN 3 THEN N'Təsdiqlənib' WHEN 4 THEN N'İmtina edilib' WHEN 5 THEN N'1 günlük əməkhaqqıya dəyişiklik' ELSE CAST(ne AS VARCHAR) END AS ne,qeyd FROM tb_prodoc_business_trip tb1
		LEFT JOIN tb_prodoc_business_trip_logs tb2 ON tb2.trip_id=tb1.id
		WHERE tb1.id=business_trip.id
		) loglar
		LEFT JOIN v_user_adlar ON loglar.user_id=v_user_adlar.USERID
		ORDER BY loglar.[date] ASC";
		break;
        case 'daxili_sened_vacation_order':
            $sql = "SELECT loglar.*,v_user_adlar.user_ad
		FROM (SELECT '$sened_id' AS id) AS vacation_order
		OUTER APPLY (
            SELECT employe AS user_id,date AS [date],N'Sənəd qeydiyyatdan keçirilib' AS ne, '' AS text FROM tb_prodoc_vacation_orders WHERE id=vacation_order.id
		UNION ALL
		SELECT tb2.user_id,tb2.date AS [date],CASE ne WHEN 1 THEN N'Dəyişdirilib' WHEN 3 THEN N'Təsdiqlənib' WHEN 4 THEN N'İmtina edilib' WHEN 5 THEN N'1 günlük əməkhaqqıya dəyişiklik' ELSE CAST(ne AS VARCHAR) END AS ne,qeyd FROM tb_prodoc_vacation_orders tb1
		LEFT JOIN tb_prodoc_vacation_orders_logs tb2 ON tb2.order_id=tb1.id
		WHERE tb1.id=vacation_order.id
		) loglar
		LEFT JOIN v_user_adlar ON loglar.user_id=v_user_adlar.USERID
		ORDER BY loglar.[date] ASC";
            break;
        case 'daxili_sened_xestelik_vereqi':
            $sql = "SELECT loglar.*, 
                v_user_adlar.user_ad
              FROM (SELECT $sened_id AS id) AS gorush_v
                OUTER APPLY (
                  SELECT user_id,
                    tarix AS [date],
                    '158sened_qeydiyyatdan_kechdi' AS ne,
                    about AS [text] 
                  FROM tb_gorushler 
                  WHERE id=gorush_v.id
                    UNION ALL
                  SELECT tb2.user_id, tb2.date AS [date],
                    CASE ne 
                      WHEN 1 THEN '158changed' 
                      WHEN 3 THEN '158testiqlenib' 
                      WHEN 4 THEN '158imtinaedilib' 
                    ELSE CAST(ne AS VARCHAR) END AS ne, qeyd AS [text] 
                  FROM tb_gorushler tb1
                  LEFT JOIN tb_gorushler_logs tb2 ON tb2.gorush_id=tb1.id
                  WHERE tb1.id=gorush_v.id
                ) loglar
                LEFT JOIN v_user_adlar ON loglar.user_id=v_user_adlar.USERID
                ORDER BY loglar.date ASC";
            break;
        case 'daxili_sened_icazeler':
            $sql = "SELECT loglar.*,
                v_user_adlar.user_ad
              FROM (SELECT '$sened_id' AS id) AS icaze
                OUTER APPLY (
                  SELECT elave_eden_user AS user_id,
                  date AS [date],
                  '158sened_qeydiyyatdan_kechdi' AS ne,
                  about AS [text] 
                FROM tb_icazeler 
                WHERE id=icaze.id
                  UNION ALL
                SELECT tb2.user_id, tb2.date AS [date],
                  CASE ne 
                    WHEN 1 THEN '158changed' 
                    WHEN 3 THEN '158testiqlenib' 
                    WHEN 4 THEN '158imtinaedilib' 
                  ELSE CAST(ne AS VARCHAR) END AS ne, qeyd AS [text] 
                FROM tb_icazeler tb1
                LEFT JOIN tb_icazeler_logs tb2 ON tb2.icaze_id=tb1.id
                WHERE tb1.id=icaze.id
                ) loglar
                LEFT JOIN v_user_adlar ON loglar.user_id=v_user_adlar.USERID
                ORDER BY loglar.[date] ASC";
            break;
    }

    return $sql;
}