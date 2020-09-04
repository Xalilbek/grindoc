<?php
session_start();
include_once '../../../class/class.functions.php';
$user = new User();

if(!$user->get_session())
{
	header("Location: login.php");
	exit;
}

function qeyriIshGunuYoxla( $employe , $whlDate )
{
    if (!isProIDMode()) {
        return false;
    }

    global $user,$queryFilter;
    $whlDate = date("m/d/Y" , $whlDate );

    $data = pdof()->query(" SELECT qeyri_ish_gunu FROM tb_ish_rejimi_gundelik WHERE user_id = '$employe' AND tarix = '$whlDate' AND IsEmployeeWorkDate = 1 AND $queryFilter")->fetch();

    if($data === false) $user->error_msg( "Əməkdaşın həmin tarixdə iş rejimi yoxdur" );

	return $data['qeyri_ish_gunu'] > 0 ? true : false;
}


if(isset($_POST['vacation_day_count']) && is_numeric($_POST['vacation_day_count']) && $_POST['vacation_day_count']>0 &&
	isset($_POST['employe']) && is_numeric($_POST['employe']) && $_POST['employe']>=0 &&
	isset($_POST['tarix1']) && is_string($_POST['tarix1']) && $user->is_date($_POST['tarix1']) && 
	isset($_POST['type']) && is_string($_POST['type']) && in_array($_POST['type'],array("days","enddate"))
  )
{
	$TenantId = $user->getActiveTenantId();
	$queryFilter = $user->getQueryTenantFilter('insertCheck',$TenantId);

	$vacation_day_count = (int)$_POST['vacation_day_count'];
	$employe            = (int)$_POST['employe'];
	$type               = $_POST['type'];
	$t1Str              = strtotime($_POST['tarix1']);
	$tarix1             = date("m/d/Y", $t1Str);
    $legal_illegal      = $user->getActiveSystemType();

	
	if($type=="enddate")
	{
		$whlDate = strtotime($tarix1);
		$whlEndDate = strtotime("+".($vacation_day_count-1)." day" , $whlDate);
		while($whlDate<=$whlEndDate)
		{
			if($legal_illegal == 'legal' && qeyriIshGunuYoxla( $employe , $whlDate )!==false)
			{
				$whlEndDate = strtotime("+1 day", $whlEndDate);
			}
			$whlDate = strtotime("+1 day", $whlDate);
		}

		$date2 = date("d-m-Y", $whlEndDate);

		$HolidayDays = floor((strtotime($date2) - strtotime($tarix1)) / 3600 / 24)+1-$vacation_day_count;
		
		print json_encode(array("status"=>"hazir","start_date"=>$_POST['tarix1'],"end_date"=>$date2,"vacation_day_count"=>$vacation_day_count,"holidayCount"=>$HolidayDays,"DaysCount"=>$vacation_day_count));
		exit();
	}
}
else
{
	$user->error_msg(dil::soz("26_mez_olmaz"));
}
