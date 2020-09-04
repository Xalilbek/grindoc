<?php
session_start();
include_once '../../../class/class.functions.php';
include_once DIRNAME_INDEX . 'modules/module_builder/model.php';
include_once DIRNAME_INDEX . 'prodoc/component/Form.php';
$user = new User();

if(!$user->get_session())
{
    print "daxil_olmayib";
    exit();
}

$tip = get('tip');

$pr = ($tip == 'protask' ? (int)$user->checkPrivilegia("msk:msk_protask_istehsalat_teqvimi") : (int)$user->checkPrivilegia("msk:msk_general_istehsalat_teqvimi"));
if ($pr !== 2)
{
    print json_encode(array("status"=>"hazir","template"=>htmlspecialchars('<div>Olmaz!</div>',ENT_QUOTES)));
    exit();
}

$nm    = get('nezaret_muddeti');
$son_icra_tarixi = get('son_icra_tarixi');
$activ = get('activ');
$vacib_sahe = get('vacib_sahe');
$activ_son_tarixi    = get('activ_son_tarix');
$nezaret_muddeti_gun = get('nezaret_muddeti_gun');
$son_icra_tarixi_gun = get('son_icra_tarixi_gun');

if(isset($nm) && !empty($nm)) {
    update($nm, getTO($tip));
}
else if(isset($activ))
{
    update($activ, getActiv($tip));
}

if(isset($son_icra_tarixi) && !empty($son_icra_tarixi))
{
    update($son_icra_tarixi, getSonIcra($tip));
}
else if(isset($activ_son_tarixi))
{
    update($activ_son_tarixi, getActivSonIcra($tip));
}

if(isset($nezaret_muddeti_gun))
{
    update($nezaret_muddeti_gun, getNezaretGun($tip));
}

if(isset($son_icra_tarixi_gun))
{
    update($son_icra_tarixi_gun, getSonIcraGun($tip));
}

if(isset($vacib_sahe))
{
    update($vacib_sahe, getVacibSahe($tip));
}

function update($val, $name)
{
    global $user;
    DB::query("UPDATE tb_options SET value = '$val' WHERE option_name = '$name'");
    $user->success_msg();
}

function getActiv($tip)
{
    $activ = '';

    switch ($tip)
    {
        case 'huquqi':
            $activ = 'activ_nezaret_muddeti';
            break;
        case 'protask':
            $activ = 'activ_nezaret_muddeti_protask';
            break;
        case 'fiziki':
            $activ = 'activ_nezaret_muddeti_fiziki';
            break;
        case 'daxili':
            $activ = 'activ_nezaret_muddeti_daxili';
            break;
    }

    return $activ;
}

function getActivSonIcra($tip)
{
    $activ = '';

    switch ($tip)
    {
        case 'huquqi':
            $activ = 'activ_son_tarix';
            break;
        case 'fiziki':
            $activ = 'activ_son_tarix_fiziki';
            break;
        case 'protask':
            $activ = 'activ_son_tarix_protask';
            break;
        case 'daxili':
            $activ = 'activ_son_tarix_daxili';
            break;
    }

    return $activ;
}

function getTO($tip)
{
    $activ = '';

    switch ($tip)
    {
        case 'huquqi':
            $activ = 'nezaret_muddeti';
            break;
        case 'fiziki':
            $activ = 'nezaret_muddeti_fiziki';
            break;
        case 'protask':
            $activ = 'nezaret_muddeti_protask';
            break;
        case 'daxili':
            $activ = 'nezaret_muddeti_daxili';
            break;
    }

    return $activ;
}

function getSonIcra($tip)
{
    $activ = '';

    switch ($tip)
    {
        case 'huquqi':
            $activ = 'son_icra_tarixi';
            break;
        case 'fiziki':
            $activ = 'son_icra_tarixi_fiziki';
            break;
        case 'protask':
            $activ = 'son_icra_tarixi_protask';
            break;
        case 'daxili':
            $activ = 'son_icra_tarixi_daxili';
            break;
    }

    return $activ;
}

function getNezaretGun($tip)
{
    $activ = '';

    switch ($tip)
    {
        case 'huquqi':
            $activ = 'nezaret_muddeti_gun';
            break;
        case 'fiziki':
            $activ = 'nezaret_muddeti_gun_fiziki';
            break;
        case 'protask':
            $activ = 'nezaret_muddeti_gun_protask';
            break;
        case 'daxili':
            $activ = 'nezaret_muddeti_gun_daxili';
            break;
    }

    return $activ;
}

function getSonIcraGun($tip)
{
    $activ = '';

    switch ($tip)
    {
        case 'huquqi':
            $activ = 'son_icra_tarix_gun';
            break;
        case 'fiziki':
            $activ = 'son_icra_tarix_gun_fiziki';
            break;
        case 'protask':
            $activ = 'son_icra_tarix_gun_protask';
            break;
        case 'daxili':
            $activ = 'son_icra_tarix_gun_daxili';
            break;
    }

    return $activ;
}

function getVacibSahe($tip)
{
    $activ = '';

    switch ($tip)
    {
        case 'huquqi':
            $activ = 'nezaret_muddeti_vacib_sahe';
            break;
        case 'fiziki':
            $activ = 'nezaret_muddeti_vacib_sahe_fiziki';
            break;
        case 'daxili':
            $activ = 'nezaret_muddeti_vacib_sahe_daxili';
            break;
    }

    return $activ;
}