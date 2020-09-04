<?php

include_once '../../class/class.functions.php';
$user = new User();
function dataForType($document_id){
    global $userAdSQLFormat;

    $properties = array(
        "elave_razilashdirma"=>array(
            "query"=> "
       SELECT
           v_daxil_olan_senedler.id as sened_id,
            ( CASE tb2.sened_tip WHEN 1 THEN N'Məlumat üçün' WHEN 2 THEN N'İcra üçün' ELSE N'Qeyd olunmayıb' END ) AS senedin_tipi,
            ( SELECT name FROM tb_prodoc_nazalogiya WHERE id = tb2.qisa_mezmun ) AS qisa_mezmun,
            rey_muellifi_ad,
            ttt.user_name,
            ttt.imza as emekdash_image,
            ttt.vezife as vezifeler,
            v.ad AS emeqhaqqina_elave_valyuta_ad,
            tb2.*,
             FORMAT(tb2.senedin_tarixi, 'dd-MM-yyyy') as sened_tarix,
            CONCAT(tb2.emeqhaqqina_elave, ' ', v.ad) as emeqhaqqina_elave_reqem,
             v_daxil_olan_senedler.created_at AS date,
             v.ad as valyuta_ad,
             v_daxil_olan_senedler.document_number
        FROM
            v_daxil_olan_senedler
            LEFT JOIN tb_prodoc_elave_razilashdirma as tb2 ON v_daxil_olan_senedler.id= tb2.document_id 
            LEFT JOIN v_users AS ttt ON
             ttt.USERID = tb2.emekdash
             LEFT JOIN tb_valyuta As v
             ON v.id = tb2.emeqhaqqina_elave_valyuta
        WHERE tb2.document_id = $document_id
    "
        ),
    "arayish"=>array(
            "query"=> "SELECT
                                        tb1.*,
                                        tb3.Soyadi surName,
                                        tb3.Adi as name,
                                        tb3.AtaAdi as dadName,
                                        tb1.date as created_at,
                                        tb4.name as sent_company,
                                        CONVERT(date, tb1.[date]) AS certificate_date,
                                        tb3.vezife as position,
                                        tb5.document_number as  document_number
                                    FROM
                                        tb_prodoc_certificate tb1
                                        LEFT JOIN v_users tb3 ON USERID = employe
                                        LEFT JOIN tb_prodoc_certificate_organizations tb4 ON organization_id = tb4.id
                                        LEFT JOIN v_daxil_olan_senedler_corrected tb5 ON tb1.document_id = tb5.id 
                                    WHERE
                                        tb1.document_id='$document_id'  "
        ),
        "employe_petition"=>array(
            "query"=> "SELECT
                                        tb1.*,
                                        tb3.Soyadi surName,
                                        tb3.Adi as name,
                                        tb3.AtaAdi as dadName,
                                        tb1.date as created_at,
                                        tb4.name as sent_company,
                                        CONVERT(date, tb1.[date]) AS certificate_date,
                                        tb3.vezife as position_name,
                                        tb5.document_number as  document_number
                                    FROM
                                        tb_prodoc_certificate tb1
                                        LEFT JOIN v_users tb3 ON USERID = employe
                                        LEFT JOIN tb_prodoc_certificate_organizations tb4 ON organization_id = tb4.id
                                        LEFT JOIN v_daxil_olan_senedler_corrected tb5 ON tb1.document_id = tb5.id 
                                    WHERE
                                        tb1.document_id='$document_id'  "
        ),
        "hesabat_yarat"=>array(
            "query"=> "
                     SELECT
                        tb_prodoc_partlamamish_tek_sursat.*,
                        tb_general_cities.ad AS sheher_ad,
                        tb_general_regions.ad AS rayon_ad,
                        CONCAT ((
                            CASE
                                
                                WHEN dkps = 1 THEN
                                'DGPS' ELSE '' 
                            END 
                                ),
                                ' ',
                        ( CASE WHEN kps = 1 THEN 'GPS' ELSE '' END )) AS koordinat_goturulub,
                        CONCAT ((
                            CASE
                                
                                WHEN veziyyet_zererleshdirilmish = 1 THEN
                                N'Zərərsizləşdirilmiş' ELSE '' 
                            END 
                                ),
                                ' ',
                                (
                            CASE
                                
                                WHEN veziyyet_isharelenmish = 1 THEN
                                N'İşarənləmiş' ELSE '' 
                            END 
                                ),
                                ' ',
                                    (
                            CASE
                                
                                WHEN veziyyet_aparilmish = 1 THEN
                                N'Aparılmış' ELSE '' 
                            END 
                                )) AS veziyyet,
                            document_number 
                        FROM
                            tb_prodoc_partlamamish_tek_sursat
                            LEFT JOIN tb_general_cities ON tb_general_cities.id= tb_prodoc_partlamamish_tek_sursat.sheher_id
                            LEFT JOIN tb_general_regions ON rayon_id = tb_general_regions.id
                            LEFT JOIN tb_prodoc_document_number ON tb_prodoc_document_number.id= document_id 
                    WHERE
                    tb_prodoc_partlamamish_tek_sursat.document_id = $document_id
                                            "
        ),
        "create_act"=>array(
            "query"=> " SELECT act.*,
               CONVERT(VARCHAR(10), act.created_at, 110) as akt_tarix,
               city.ad as sheher,
               region.ad as kend,
               CONCAT([user].Adi,' ',[user].Soyadi) as tehvil_veren_ad
        FROM tb_prodoc_aktlar act
               LEFT JOIN tb_general_cities city ON city.id = act.sheher_id
               LEFT JOIN tb_general_regions region ON region.id = act.kend_id
               LEFT JOIN v_users [user] ON [user].USERID = act.tehvil_veren
       WHERE act.document_id= ".$document_id
        ),
        "task_command"=>array(
                "query"=> "
                         SELECT
               v_daxil_olan_senedler.id as sened_id,
                ( CASE v_daxil_olan_senedler.sened_tip WHEN 1 THEN N'Məlumat üçün' WHEN 2 THEN N'İcra üçün' ELSE N'Qeyd olunmayıb' END ) AS senedin_tipi,
                ttt.user_name as rey_muellifi,
                ttt.vezife,
                tb2.*,
                 v_daxil_olan_senedler.created_at AS date,
                convert(varchar,tb2.document_date ,105) as senedin_tarixi,
                 v_daxil_olan_senedler.document_number
            FROM
                v_daxil_olan_senedler
                LEFT JOIN tb_prodoc_task_command as tb2 ON v_daxil_olan_senedler.id= tb2.document_id 
                LEFT JOIN v_users AS ttt ON
                 ttt.USERID = tb2.rey_muellifi
            WHERE tb2.document_id =  $document_id
                                                "
            ),
        "satin_alma"=>array(
            "query"=> "
                         SELECT
                ttt.user_name as sifarisci_adi,
                ttt.struktur_bolmesi as sifarisci_sobesi,
                sifaris.name as sifaris_tipi_adi,
                tb2.*,
                 v_daxil_olan_senedler.created_at AS date,
                 v_daxil_olan_senedler.document_number
            FROM
                v_daxil_olan_senedler
                LEFT JOIN tb_prodoc_satinalma_sifaris as tb2 ON v_daxil_olan_senedler.id= tb2.document_id 
                LEFT JOIN tb_prodoc_sifaris_tipi sifaris ON sifaris.id = tb2.sifaris_tipi
                LEFT JOIN v_users AS ttt ON
                 ttt.USERID = tb2.sifarisci
            WHERE tb2.document_id =  $document_id
                                                "
        ),
    "teqdimat"=>array(
                "query"=> "
                         SELECT
                            v_daxil_olan_senedler.id AS sened_id,
                            ( CASE v_daxil_olan_senedler.sened_tip WHEN 1 THEN N'Məlumat üçün' WHEN 2 THEN N'İcra üçün' ELSE N'Qeyd olunmayıb' END ) AS senedin_tipi,
                            rey_muellifi_ad,
                            ( SELECT $userAdSQLFormat FROM tb_users WHERE USERID = tb2.kim ) AS kim_adi,
                            ( SELECT  vezife FROM v_users WHERE USERID = tb2.kim ) vezife,
                            ( SELECT $userAdSQLFormat FROM tb_users WHERE USERID = tb2.kime ) AS kime_adi,
                            tb2.*,
                            convert(varchar,v_daxil_olan_senedler.created_at,105)  AS DATE,
                            convert(varchar,v_daxil_olan_senedler.senedin_tarixi,105) as senedin_tarixi,
                            v_daxil_olan_senedler.document_number 
                        FROM
                            v_daxil_olan_senedler
                            LEFT JOIN tb_prodoc_teqdimat AS tb2 ON v_daxil_olan_senedler.id= tb2.document_id
                        WHERE
                            tb2.document_id = $document_id
                                                "
            )

    );

    return $properties;
}


function hasOrders($sened_id,$tip, $document_id){
    $orderOfElements=array();
    if("create_act"==$tip){
        $sql =" SELECT  CONCAT(Adi, ' ', Soyadi) as user_name
        FROM tb_prodoc_akt_imzalayan_shexsler imzalayan_shexsler
               LEFT JOIN v_users ON v_users.USERID = imzalayan_shexsler.user_id
        WHERE document_id = ".$document_id;

        $imzalayan_shexsler = DB::fetchAll($sql);
        $orderOfElements = array();
        foreach ($imzalayan_shexsler as $key => $imzalayan_shexs) {
            $orderOfElements[] = [
                'order_counter' => $key + 1,
                'order_row' => $key + 1,

                'imzalayan' => $imzalayan_shexs['user_name'],
            ];
        }
    }
    return $orderOfElements;
}


function hasTable($sened_id,$tip,$document_id){
    $devices=array();
    if ($tip=="hesabat_yarat") {
        $sql = "
        SELECT
            qurgunun_novu,
            modeli,
            miqdari,
            ( CASE WHEN surprizli = 1 THEN N'Hə' ELSE N'Yox' END ) AS surprizli,
            ( CASE WHEN tele_meftili = 1 THEN N'Hə' ELSE N'Yox' END ) AS tele_meftili 
        FROM
            [dbo].[tb_prodoc_partlamamish_tek_sursat_qurgular] 
        WHERE
            sened_id = $sened_id
                ";
        $qurgular = DB::fetchAll($sql);

        $devices = array();
        foreach ($qurgular as $key => $qurgu) {
            $devices[] = [
                'table_counter' => $key + 1,
                'table_row' => $key + 1,
                'qurgunun_novu' => $qurgu['qurgunun_novu'],
                'modeli' => $qurgu['modeli'],
                'miqdari' => $qurgu['miqdari'],
                'surprizli' => $qurgu['surprizli'],
                'tele_meftili' => $qurgu['tele_meftili'],
            ];
        }
    }
    else if ($tip=="create_act") {
        $sql = "
          SELECT *
        FROM tb_prodoc_akt_tipleri WHERE internal_document_id = $sened_id
                ";
        $qurgular = DB::fetchAll($sql);

        $devices = array();
        foreach ($qurgular as $key => $qurgu) {
            $devices[] = [
                'table_counter' => $key + 1,
                'tipi' => $qurgu['tipi'],
                'chapi' => $qurgu['chapi'],
                'novu' => $qurgu['novu'],
                'miqdari' => $qurgu['miqdari'],
                'qeydi' => $qurgu['qeydi'],
            ];
        }
    }

    else if ($tip=="task_command"){
        $sql="SELECT
                v_user_adlar.user_ad,
                derkenar_metn,
                ( CASE WHEN son_icra_tarixi IS NOT NULL THEN CONVERT ( VARCHAR, FORMAT ( son_icra_tarixi, 'dd-MM-yyyy' )) ELSE N'Müraciət olunduqda' END ) AS son_icra_tarixi 
            FROM
                tb_prodoc_task_command_hesabat_verenler AS tb2
                LEFT JOIN v_user_adlar ON v_user_adlar.USERID = tb2.kime 
            WHERE
                tb2.task_command_id = $sened_id";

        $tasks=DB::fetchAll(sprintf($sql));

        $devices=array();
        foreach ($tasks as $key=>$task) {
            $devices[] = [
                'table_counter' => $key + 1,
                'kime' => $task['user_ad'],
                'tapshiriq' => $task['derkenar_metn'],
                'muddet' => $task['son_icra_tarixi'],
            ];
        }
    }
    else if ($tip=="satin_alma"){
        $sql="SELECT
                tb2.*,
                tb2.miqdar * tb2.mebleq as meblegin_cemi,
                olcu_vahidi.name as olcu_vahidi_adi
            FROM
                tb_prodoc_satinalma_sifaris_xidmet AS tb2
                LEFT JOIN tb_prodoc_olcu_vahidi olcu_vahidi ON olcu_vahidi.id = tb2.olcu_vahidi 
            WHERE
                tb2.parent_id = $sened_id";

        $tasks=DB::fetchAll(sprintf($sql));

        $devices=array();
        foreach ($tasks as $key=>$task) {
            $devices[] = [
                'table_counter'   => $key + 1,
                'malin_kodu'      => $task['malin_kodu'],
                'table_row'       => $task['mal_adi'],
                'olcu_vahidi_adi' => $task['olcu_vahidi_adi'],
                'netice_miqdar'   => $task['miqdar'],
                'mebleq'          => $task['mebleq'],
                'meblegin_cemi'   => $task['meblegin_cemi'],
                'icra_muddeti'    => $task['gun'],
                'serh'            => $task['sebeb_qeyd_et']
            ];
        }
    }

return $devices;

}

function otherComponents($sened_id,$tip,$document_id){
    $otherComponents=array();
    if ($tip=="task_command"){
        $melumat = [];
        $sql = "SELECT
					v_user_adlar.user_ad
				FROM
					tb_prodoc_testiqleyecek_shexs as tb2
				LEFT JOIN v_user_adlar ON v_user_adlar.USERID = tb2.user_id AND tb2.tip = 'tanish_ol'
				WHERE
					tb2.related_record_id = $document_id";

        $melumat = DB::fetchColumnArray(sprintf($sql));


        $kime = [];
        $sql = "SELECT
					v_user_adlar.user_ad
				FROM
					tb_prodoc_task_command_hesabat_verenler as tb2
				LEFT JOIN v_user_adlar ON v_user_adlar.USERID = tb2.kime
				WHERE
					tb2.task_command_id = $sened_id";
        $kime = DB::fetchColumnArray(sprintf($sql));
        $otherComponents['kime']           = implode(",", $kime);
        $otherComponents['melumat']        = trim(implode(",", $melumat), ",");
    }
    return $otherComponents;

}