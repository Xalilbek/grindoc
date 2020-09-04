<?php
session_start();
include_once '../../../class/class.functions.php';
require_once DIRNAME_INDEX . 'prodoc/includes/internal_document.php';
$user = new User();

if(!$user->get_session())
{
    header("Location: login.php");
    exit;
}
$TenantId = $user->getActiveTenantId();

$queryFilter = $user->getQueryTenantFilter('insertCheck',$TenantId);

function qeyriIshGunYuoxla( $employe , $whlDate )
{
    if (isProdocMode()) {
        return false;
    }

    global $user,$queryFilter;
    $whlDate = date("m/d/Y" , $whlDate );

    $data = pdof()->query(" SELECT qeyri_ish_gunu FROM tb_ish_rejimi_gundelik WHERE user_id = '$employe' AND tarix = '$whlDate' AND IsEmployeeWorkDate = 1 AND $queryFilter")->fetch();
    if($data === false) $user->error_msg( "Əməkdaşın həmin tarixdə iş rejimi yoxdur" );

    return $data['qeyri_ish_gunu'] > 0 ? true : false;
}

function checkIsWorkDate($employe, $date1, $date2)
{
    global $TenantId;
    $chk = pdof()->query(" SELECT StartDate,EndDate FROM tb_proid_users_period_salary WHERE user_id = '$employe' AND TenantId = '$TenantId' ORDER BY StartDate DESC")->fetch();

    return ( strtotime($chk['StartDate']) <= strtotime($date1) && ( $chk['EndDate'] == "" || strtotime($chk['EndDate']) >= strtotime($date2) ) );
}

const DOCUMENT_KEY = 'mezuniyet_erizesi';

if(isset($_POST['gid']) && is_numeric($_POST['gid']) && $_POST['gid']>=0
    && isset($_POST['date1']) && is_string($_POST['date1']) && $_POST['date1']!=""
    && isset($_POST['vacation_day_count']) && is_numeric($_POST['vacation_day_count']) && $_POST['vacation_day_count']>0
    && isset($_POST['about']) && is_string($_POST['about'])
    && isset($_POST['employe']) && is_numeric($_POST['employe']) && $_POST['employe']>0
    && isset($_POST['vacation_type']) && is_numeric($_POST['vacation_type']) && $_POST['vacation_type']>0
)
{
    try {
        DB::beginTransaction();

        $userId = (int)$_SESSION['erpuserid'];
        $vacationId = (int)$_POST['gid'];
        $privilegia = (int)$user->checkPrivilegia("mezuniyyetler");
        $legal_illegal = $user->getActiveSystemType();

        $employe = (int)$_POST['employe'];
        $strTarix1 = strtotime($_POST['date1']);

        $date1 = date("m/d/Y", $strTarix1);
        $tarix1Ay = date("m", $strTarix1);
        $tarix1Il = date("Y", $strTarix1);

        $vacationDay = (int)$_POST['vacation_day_count'];

        $about = $user->tmzle($_POST['about']);
        $vacation_type = (int)$_POST['vacation_type'];
        $vacationTypeMSK = pdof()->query("SELECT (CASE WHEN [check]=1 THEN 1 ELSE 2 END) AS type , dovr FROM tb_teyinatlar WHERE id='$vacation_type'")->fetch();
        $vacationPeriod = (int)$vacationTypeMSK['dovr'];
        $vacationTypeMSK = (int)$vacationTypeMSK['type'];

        $vacationInf = false;
        if ($vacationId > 0) {
            $vacationInf = pdof()->query("SELECT * FROM tb_mezuniyyetler WHERE id='$vacationId'")->fetch();
            if (!$vacationInf) {
                $user->error_msg(dil::soz("26err1"));
            }
            if ($vacationInf['elave_eden_user'] != $userId) {
                $user->error_msg(dil::soz("26err2"));
            }
            $hasOrder = pdof()->query("SELECT * FROM tb_prodoc_vacation_orders WHERE vacation_id='$vacationId'")->fetch();
            if ($hasOrder) {
                $user->error_msg(dil::soz("26err3"));
            }

            require_once DIRNAME_INDEX . 'prodoc/model/InternalDocument.php';
            $intDoc = new InternalDocument($vacationInf['document_id']);
        }

        // Mezuniyyetin hesablanma dovru VSQ daxil olma tarixinnen goturmelidi
        //$employeInf = pdof()->query("SELECT *, DATEDIFF(MONTH, ishe_qebul_tarixi, GETDATE()) AS worked_months FROM v_users WHERE USERID='$employe' AND $queryFilter")->fetch();
        $employeInf = pdof()->query("SELECT *, DATEDIFF(MONTH, vsq_entree_date, GETDATE()) AS worked_months FROM tb_users WHERE USERID='$employe'")->fetch();

        $tabechilik = $user->tabechilikChixart();
        $tabechiler2 = array();
        $tabechiler = array();
        if (isset($tabechilik[$userId])) {
            $tabechiler2 = $tabechilik[$userId];
            $tabechiler = array_keys($tabechiler2);
        }


        if (/*isset($tabechiler[$employe]) ||*/
            (int)$privilegia == 2) {

            $whlDate = strtotime($date1);
            $whlEndDate = strtotime("+" . ($vacationDay - 1) . " day", $whlDate);
            while ($whlDate <= $whlEndDate) {
                if ($legal_illegal == 'legal' && $user->qeyriIshGunuYoxla($employe, $whlDate) !== false) {
                    $whlEndDate = strtotime("+1 day", $whlEndDate);
                }
                $whlDate = strtotime("+1 day", $whlDate);
            }

            $firstWorkDay = date("m/d/Y", strtotime("+1 day", $whlEndDate));
            $date2 = date("m/d/Y", $whlEndDate);

            if (isProIDMode()) {
                if (!checkIsWorkDate($employe, $date1, $date2)) {
                    $user->error_msg("Seçilən tarix əməkdaşın işlədiyi günlərə düşmür");
                }
            }

            // qeyri resmi hisse
            if ($legal_illegal === 'illegal') {
                $user->vacation()->mezuniyyetGunleri(false, $employe, $TenantId);

                $limitChixart = pdof()->query("SELECT * FROM tb_mezuniyyet_gunleri_illik WHERE user_id='$employe' AND tarix1<GETDATE() AND tarix2 IS NULL AND $queryFilter")->fetch();
                $vacationDays = (int)$user->vacation()->getDailyVacationDays($employe, $TenantId, true);
                $vacationDays += (int)$limitChixart['elave_gun_sayi'];
                $usedDays = (int)$limitChixart['used_days'];
                $deletedDays = (int)$limitChixart['silinen_gunler'];
                $thisYearId = (int)$limitChixart['id'];
                $otenIllerdenQalanGunler = pdof()->query("SELECT SUM(haqq_etdiyi_gun_sayi+elave_gun_sayi-used_days-silinen_gunler) AS un_used_days FROM tb_mezuniyyet_gunleri_illik WHERE user_id='$employe' AND id<>'$thisYearId' AND $queryFilter")->fetch();
                $cariIlinqalanGunleri = $vacationDays - $usedDays - $deletedDays;
                $otenIllerdenQalanGunler = (int)$otenIllerdenQalanGunler['un_used_days'];
                $unUsedDays = $cariIlinqalanGunleri + $otenIllerdenQalanGunler;

                if ($unUsedDays < $vacationDay && $vacationPeriod == 1) {
                    // $user->error_msg("Məzuniyyətə Seçdiyiniz gün sayı haqq etdiyiniz günlərdən çoxdur..."); // muveqqeti olaraq goturulub
                }
            } else {
                // resmi hisse
                $user->mezuniyyetGunleri(false, $employe);

                $limitChixart = pdof()->query("SELECT * FROM tb_mezuniyyet_gunleri_illik WHERE user_id='$employe' AND tarix1<GETDATE() AND tarix2>GETDATE() AND $queryFilter")->fetch();
                $vacationDays = (int)$limitChixart['gun_sayi'];
                $vacationDays += (int)$limitChixart['elave_gun_sayi'];
                $usedDays = (int)$limitChixart['used_days'];
                $deletedDays = (int)$limitChixart['silinen_gunler'];
                $thisYearId = (int)$limitChixart['id'];
                $otenIllerdenQalanGunler = pdof()->query("SELECT SUM(gun_sayi+elave_gun_sayi-used_days-silinen_gunler) AS un_used_days FROM tb_mezuniyyet_gunleri_illik WHERE user_id='$employe' AND id<>'$thisYearId' AND $queryFilter")->fetch();
                $cariIlinqalanGunleri = $vacationDays - $usedDays - $deletedDays;
                $otenIllerdenQalanGunler = (int)$otenIllerdenQalanGunler['un_used_days'];
                $unUsedDays = $cariIlinqalanGunleri + $otenIllerdenQalanGunler;
            }

            $lastDayy = date("m/t/Y", strtotime("+" . ($vacationDay - 1) . " day", strtotime($date1)));

            $chkMezuniyyet = pdof()->query("SELECT * FROM tb_ezamiyyetler WHERE status!=3 AND ((start_date<='$date1' AND end_date>='$date1') OR (start_date<='$date2' AND end_date>='$date2') OR (start_date>='$date1' AND end_date<='$date2')) AND user_id='$employe' AND $queryFilter")->fetch();


            if ($chkMezuniyyet && isProIDMode()) {
                $user->error_msg(sprintf(dil::soz("26err10") . "%s №" . (int)$chkMezuniyyet['id'] . ")",
                    dil::soz("47ezamiyyet")));
            }

            if ($vacationPeriod == 1) {
                $month = (int)pdof()->query("SELECT mezuniyyet_huququnun_verilmesi FROM tb_teyinatlar WHERE id='$vacation_type'")->fetchColumn();

                // $month = $month ? $month : 6;

                if (($employeInf['worked_months'] >= $month) == false) {
                    $user->error_msg(sprintf(dil::soz("26err9"), $month) . date("d-m-Y",
                            strtotime((string)$employeInf['ishe_qebul_tarixi'])) . ").");
                }

                $remainderDays = array();
                $selectDaysString = ($legal_illegal === 'illegal') ? "(haqq_etdiyi_gun_sayi - used_days - silinen_gunler)" : "(gun_sayi - used_days - silinen_gunler)";
                $getRemainderDays = pdof()->query("SELECT YEAR(tarix1) AS t_year , $selectDaysString AS days FROM tb_mezuniyyet_gunleri_illik WHERE user_id='$employe' AND $queryFilter ORDER BY tarix1 ASC")->fetchAll();
                foreach ($getRemainderDays as $remainderDay) {
                    $remainderDays[(int)$remainderDay['t_year']] = (int)$remainderDay['days'];
                }

                $workYearsVac = pdof()->query("
			SELECT tb2.d_year, COUNT(0) AS days
			FROM dbo.dateRange('$date1', '$date2') a
			OUTER APPLY (SELECT USERID, (CASE WHEN MONTH(ishe_qebul_tarixi)*31+DAY(ishe_qebul_tarixi)>MONTH(a.date)*31+DAY(a.date) THEN YEAR(a.date)-1 ELSE YEAR(a.date) END) AS d_year FROM v_users WHERE USERID='$employe' AND $queryFilter) tb2
			WHERE (CASE WHEN (SELECT COUNT(0) FROM tb_qeyri_ish_gunleri WHERE tarix1<=a.date AND tarix2>=a.date AND deleted=0 AND day_off=1 AND timesheet_id=(SELECT type FROM tb_proid_time_sheets WHERE id=(SELECT TOP 1 tid FROM tb_proid_time_sheets_users WHERE user_id=tb2.USERID AND $queryFilter)))=0 THEN 0 ELSE 1 END)=0
			GROUP BY tb2.d_year
			ORDER BY tb2.d_year")->fetchAll(PDO::FETCH_ASSOC);

                $yearsHtml = '';
                $yearsSub = array();
                foreach ($workYearsVac AS $kk => $wyVac) {
                    $mYear = $wyVac['d_year'];
                    foreach ($remainderDays AS $rYear => $rDay) {
                        if ($mYear >= $rYear) {
                            $daySub = $rDay > $workYearsVac[$kk]['days'] ? $workYearsVac[$kk]['days'] : $rDay;
                            $remainderDays[$rYear] -= $daySub;
                            $workYearsVac[$kk]['days'] -= $daySub;
                            $yearsSub[$rYear] = (isset($yearsSub[$rYear]) ? $yearsSub[$rYear] : 0) + $daySub;
                        } else {
                            continue;
                        }
                        if ($workYearsVac[$kk]['days'] == 0) {
                            unset($workYearsVac[$kk]);
                            break;
                        }
                    }
                }

                if (count($workYearsVac) > 0 && $vacationId == 0) {
                    // $user->error_msg(dil::soz("26err5"));
                }
            }

            $vaxtUyqunluqunuYoxla = pdof()->query("SELECT * FROM v_userler_meshqulluq_vaxtlari tb1 WHERE user_id='$employe' AND $queryFilter AND ((CAST(ilk_tarix AS DATE)<=CAST('" . $date1 . "' AS DATE) AND CAST(son_tarix AS DATE)>CAST('" . $date1 . "' AS DATE)) OR (CAST(ilk_tarix AS DATE)<CAST('" . $date2 . "' AS DATE) AND CAST(son_tarix AS DATE)>=CAST('" . $date2 . "' AS DATE)) OR (CAST(ilk_tarix AS DATE)>=CAST('" . $date1 . "' AS DATE) AND CAST(son_tarix AS DATE)<=CAST('" . $date2 . "' AS DATE))) AND (ne<>'mezuniyyet' OR (ne='mezuniyyet' AND ne_id<>'$vacationId'))")->fetch();
            if ($vaxtUyqunluqunuYoxla && isProIDMode()) {
                $busy_t = array(
                    "gorush" => dil::soz("47gorush"),
                    "icaze" => dil::soz("47icaze"),
                    "mezuniyyet" => dil::soz("47mezuniyyet"),
                    "ezamiyyet" => dil::soz("47ezamiyyet"),
                    "xestelik_vereqi" => dil::soz("sm21")
                );

                $user->error_msg(dil::soz("26err10") . (isset($busy_t[$vaxtUyqunluqunuYoxla['ne']]) ? ($busy_t[$vaxtUyqunluqunuYoxla['ne']] . " №" . (int)$vaxtUyqunluqunuYoxla['ne_id']) : "") . ").");

            } else {
                    if ($vacationTypeMSK == 1) {
                        $vacAmount = $user->proid()->vacationAmountCalc($date1, $date2, $vacationDay, $employe, $TenantId);
                        if ($legal_illegal == 'legal') {
                            $vacAmount = $vacAmount['max'];
                        } else {
                            $vacAmount = $vacAmount['amount3'];
                        }
                    } else {
                        $vacAmount = 0;
                    }

                    if ($vacationId > 0) {
                        pdof()->query("UPDATE tb_mezuniyyetler SET current_year_vacation_days='$cariIlinqalanGunleri',previous_year_vacation_days='$otenIllerdenQalanGunler',about=N'$about', start_date=CAST('$date1' AS DATE), end_date=CAST('$date2' AS DATE), user_id='$employe',  elave_eden_user='$userId',vacation_type='$vacation_type',mezuniyyet_tip='$vacationTypeMSK',gunluk_emek_haqqi='$vacAmount',mezuniyyet_gunleri_limit='$vacationDays',istifade_olunan_mezuniyyet_gunleri='$usedDays' ,number_of_days='$vacationDay',first_work_day_after_vac='$firstWorkDay' WHERE id='$vacationId'");


                        $getmezuniyyet_id[0] = $vacationId;
                        $user->proid()->regime()->ReFixEmployeesWorkingHours([$employe],
                            date("m/d/Y", strtotime($vacationInf['start_date'])),
                            date("m/d/Y", strtotime($vacationInf['end_date'])));
                        $documentId = $vacationInf['document_id'];

                        $user->editInternalDocument(
                            $intDoc,
                            DOCUMENT_KEY,
                            false
                        );
                        $id=$vacationId;

                    } else {
                        $id = pdof()->query("
                                    INSERT INTO tb_mezuniyyetler 
                                    (
                                        TenantId,
                                        about, 
                                        start_date, 
                                        end_date, 
                                        user_id, 
                                        elave_eden_user,
                                        vacation_type,
                                        mezuniyyet_tip,
                                        gunluk_emek_haqqi,
                                        mezuniyyet_gunleri_limit,
                                        istifade_olunan_mezuniyyet_gunleri,
                                        number_of_days,
                                        first_work_day_after_vac,
                                        order_status,
                                        current_year_vacation_days,
                                        previous_year_vacation_days
                                    ) 
                                    OUTPUT  INSERTED.id
                                    VALUES 
                                    (
                                        '$TenantId',
                                        N'$about', 
                                        CAST('$date1' AS DATE), 
                                        CAST('$date2' AS DATE), 
                                        '$employe', 
                                        '$userId', 
                                        '$vacation_type',
                                        '$vacationTypeMSK',
                                        '$vacAmount',
                                        '$vacationDays',
                                        '$usedDays',
                                        '$vacationDay',
                                        '$firstWorkDay',
                                        NULL  ,
                                        '$cariIlinqalanGunleri',
                                        '$otenIllerdenQalanGunler'
                                    )")->fetchColumn();


                        $documentId = $user->createInternalDocumentNumber(
                            $id,
                            DOCUMENT_KEY,
                            false
                        );

                        $getmezuniyyet_id = pdof()->query("SELECT TOP 1 id,date,gunluk_emek_haqqi, document_id FROM tb_mezuniyyetler ORDER BY id DESC")->fetch();
                        $documentId=$getmezuniyyet_id['document_id'];
                        $vacationId = (int)$getmezuniyyet_id['id'];
                    }

                    createRelation($documentId);

                   $status = daxiliSenedinTestiqlemesiniElaveEt($id, DOCUMENT_KEY, $documentId,'',null,true);


                if ((int)$status == 1) {
                        pdof()->query("DELETE FROM tb_notifications WHERE bolme='izahatlar' AND kid IN (SELECT id FROM tb_izahatlar WHERE ir_id IN (SELECT id FROM tb_ish_rejimi_gundelik WHERE tarix>=CAST('$date1' AS DATE) AND tarix<=CAST('$date2' AS DATE) AND user_id='$employe' AND $queryFilter))");
                        pdof()->query("DELETE FROM tb_izahatlar WHERE ((menbe='ishe_gecikme' AND ir_id IN (SELECT id FROM tb_proid_late_for_work WHERE date>=CAST('$date1' AS DATE) AND date<=CAST('$date2' AS DATE) AND user_id='$employe' AND $queryFilter))
                    OR (menbe='ishden_tez_chixma' AND ir_id IN (SELECT id FROM tb_proid_early_leaves_work WHERE date>=CAST('$date1' AS DATE) AND date<=CAST('$date2' AS DATE) AND user_id='$employe'  AND $queryFilter))
                    OR (menbe='yubanma' AND ir_id IN (SELECT id FROM tb_proid_delays WHERE date>=CAST('$date1' AS DATE) AND date<=CAST('$date2' AS DATE) AND user_id='$employe'  AND $queryFilter))
                    OR (menbe='work_exits_over_limit' AND ir_id IN (SELECT id FROM tb_proid_work_exits WHERE date>=CAST('$date1' AS DATE) AND date<=CAST('$date2' AS DATE) AND user_id='$employe'  AND $queryFilter)))");
                        pdof()->query("UPDATE tb_proid_late_for_work SET delay=0,penalty=0 WHERE date>=CAST('$date1' AS DATE) AND date<=CAST('$date2' AS DATE) AND user_id='$employe'  AND $queryFilter");
                        pdof()->query("UPDATE tb_proid_early_leaves_work SET time=0,penalty=0 WHERE date>=CAST('$date1' AS DATE) AND date<=CAST('$date2' AS DATE) AND user_id='$employe'  AND $queryFilter");
                        pdof()->query("UPDATE tb_proid_delays SET delay=0,penalty=0 WHERE date>=CAST('$date1' AS DATE) AND date<=CAST('$date2' AS DATE) AND user_id='$employe'  AND $queryFilter");
                        pdof()->query("DELETE FROM tb_proid_work_exits WHERE date>=CAST('$date1' AS DATE) AND date<=CAST('$date2' AS DATE) AND user_id='$employe'  AND $queryFilter");
                    }

                    $user->proid()->regime()->ReFixEmployeesWorkingHours([$employe], $date1, $date2);


                }

            DB::commit();
            print json_encode(array("status" => "ok",'id'=>$documentId));

            }


        } catch (Exception $exception) {
            sameRelationError($exception);
            DB::rollBack();
        }
    }
    else
    {
        $user->error_msg(dil::soz("26err8"));
    }
