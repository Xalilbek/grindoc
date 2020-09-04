<?php
require_once DIRNAME_INDEX . 'prodoc/functions/documentStatusColors.php';
    $sira = $rowCount;

    $priv = new Privilegiya();
    $butunSenedlerUzre = $priv->getByExtraId('butun_senedler_uzre');
    $emeliyyatHuququUzre = $priv->getByExtraId('emeliyyat_huququ_uzre');
    $reyYazmaqHuququ = $priv->getByExtraId('rey_yazmaq_huququ');

    use PowerOfAttorney\PowerOfAttorney;

    foreach ($senedler as $sened){
        $nezeratdedir="";
        if ($sened['sened_novu']=="dos"){
        	$documentData = [
        		'rey_muellifi' => $sened['rey_muellifi']
			];

            $incomingDocument = new Document($sened['id'], ['data' => $documentData]);

            $nezaret = ($sened['mektub_nezaretdedir'] == 1 ? "red" : "gray");
            $nezeratdedir = warningBtn($incomingDocument,$nezaret);
        }
        $sira++;
        $color  = '';
        $colorLeftBorder = '';
        $muddet = isset($sened['icra_muddeti']) ? $sened['icra_muddeti'] : '';
        $sonIcraTarixi = isset($sened['son_icra_tarix']) ? $sened['son_icra_tarix'] : '';

        if(!empty($muddet)) //  && (int)$sened['status'] == Document::STATUS_ACIQ
        {
            $color = colorSened($muddet);
        }

        if(array_key_exists('status', $sened)){
            if (!empty($sonIcraTarixi) && $sonIcraTarixi !== '-' && $sened['status'] == Document::STATUS_ACIQ){
                $colorLeftBorder = colorSened($sonIcraTarixi, 'dashboard');
            }else {
                $colorLeftBorder = getDocumentColorRelatedStatus($sened);
            }
        }

        ?>
        <tr style='border-left: 9px solid <?= empty($colorLeftBorder) ? "transparent" : $colorLeftBorder ?> !important' class='clickable-row <?= $color ?> ' sened-id='<?php print $sened['id']; ?>' data-tip="<?php print $sened['tip']; ?>" sened-novu="<?php print $sened['sened_novu']; ?>" executor="<?php print $sened['mesul_shexs'] ?>">
                <td class="number" style=" width:  5%; padding-left: 10px; "><?php print $sira ?></td>
<!--        <td><input type='checkbox'></td>-->
        <?php if ($nezaret_sehifesi): ?>
            <td class="document_number" style="width: 17%"><a href="index.php?module=prodoc_new&id=<?= $sened['id']; ?>&bolme=prodoc_sened_qeydiyyatdan_kecib"><?php print escape($sened['nomre']) ?></a></td>
        <?php else: ?>
            <td class="document_number" style="width: 17%"><?php print escape($sened['nomre']) ?> <?php print (isset($sened['tekrar_eyni']) && !empty($sened['tekrar_eyni']) && $sened['tekrar_eyni'] != '-') ? '-' . $sened['tekrar_eyni'] : ''; ?>
                <?php if(isset($sened['vekaletname'])&&$sened['vekaletname']!=""): ?>
                    <strong style="color:red"><?php print $sened['vekaletname']; ?></strong>
                <?php endif; ?>
            </td>
        <?php endif; ?>
        <td style="width: 40%; text-align:  left;    padding: 0px 10% !important;"><?php tarixCapEt($sened['created_at']) ?></td>
        <?php if(getProjectName()===TS&&isset($isDashboard)): ?>
            <td style="text-indent: 25px;"><?php print $sened['son_icra_tarix']=="-" ? "-" :tarixCapEt($sened['son_icra_tarix'],'d-m-Y') ?></td>
        <?php else: ?>
            <td ><?php print escape($sened['nov']) ?></td>
        <?php endif; ?>
        <?php if (getProjectName() === TS): ?>
            <td style="display: none; word-break: break-all; text-align: center"><?php print escape($sened['qeyd']) ?></td>
        <?php endif; ?>
        <?php if ($nezaret_sehifesi): ?>
            <td>
                <?php
                    tarixCapEt($sened['tarix'], 'd M Y');
                    print escape(' (gün '. ceil($sened['icra_muddeti'])) .')';
                ?>
            </td>
        <?php endif; ?>
        <?php if (!$nezaret_sehifesi && $reyYazmaqHuququ == 1): ?>
            <th>
                <?php

                if ($sened['tip'] == 'daxil_olan_sened'){
                    $document = new Document($sened['id']);
                    $documentParticipants = $document->getParticipants('tip');
                    $documentInfo = $document->getData();

                    $role_users = columnArrayUsers($documentParticipants,'USERID');
                    $role_users[] = $documentInfo['rey_muellifi'];
                    $role_users[] = $documentInfo['created_by'];
                    $role_users[] = $documentInfo['yoxlayan_shexs'];

                    $powerOfAttorney = new PowerOfAttorney(
                        $document,
                        $user->getId(),
                        new User()
                    );

                    $poas = $powerOfAttorney->getPowerOfAttorneysAsDirectPrincipal(array_filter($role_users));
                    $poa_users = [];
                    foreach ($poas as $poa){
                        $poa_users[]=$poa['to_user_id'];
                    }

                }else{
                    $outGoingDocument = new OutgoingDocument($sened['id']);

                    $role_users = $outGoingDocument->getParticipantsOutgoingDocument($sened['id']);

                    $role_users = columnArrayUsers($role_users,'user_id');

                    $powerOfAttorney = new PowerOfAttorney(
                        $outGoingDocument,
                        $user->getId(),
                        new User()
                    );

                    $poas = $powerOfAttorney->getPowerOfAttorneysAsDirectPrincipal();
                    $poa_users = [];
                    foreach ($poas as $poa){
                        $poa_users[]=$poa['to_user_id'];
                    }
                }

                $session_role_user = in_array($_SESSION['erpuserid'], $role_users);
                $poa_role_user = in_array($_SESSION['erpuserid'], $poa_users);

                if ($butunSenedlerUzre == 1 && $emeliyyatHuququUzre != 1){
                    ?>
                    <i style="color:#36c6d3;font-size: 24px;" class="fa fa-comments comment_btn"></i>
                    <?php
                } else if ($butunSenedlerUzre != 1 && $emeliyyatHuququUzre == 1 && ($session_role_user == true || $poa_role_user == true)) {
                    ?>
                    <i style="color:#36c6d3;font-size: 24px;" class="fa fa-comments comment_btn"></i>
                    <?php
                } elseif ($butunSenedlerUzre == 1 && $emeliyyatHuququUzre == 1){
                    ?>
                    <i style="color:#36c6d3;font-size: 24px;" class="fa fa-comments comment_btn"></i>
                    <?php
                }else{
                    ?>
                    <i style="color:#808080;font-size: 24px;" class="fa fa-comments comment_btn deactive"></i>
                    <?php
                }
                ?>
            </th>
        <?php endif; ?>

            <td>
                 <?php print $nezeratdedir; ?>
            </td>
        </tr>
    <?php
    }

function columnArrayUsers($other_executors = array(),$specificValue){
    $role_users = [];
    foreach ($other_executors as $participantInfo) {
        foreach ($participantInfo as $participant) {
            if (isset($participant[$specificValue])) {
                $role_users[] = $participant[$specificValue];
            }
        }
    }
    return $role_users;
}

function warningBtn(Document $incomingDocument, $nezaret)
{
    if (!$incomingDocument->senedNezaretEdeBiler()) {
        return '
             <i class="fa fa-exclamation-triangle sened_nezaret_et_notification" id="warningIcon" style="color:  '.$nezaret.'; font-size:  24px; margin-top:  7px; margin-left: 25px; cursor:pointer" ></i>
                ';
    }

    return '
             <i class="fa fa-exclamation-triangle sened_nezaret_et" id="warningIcon" style="color:  '.$nezaret.'; font-size:  24px; margin-top:  7px; margin-left: 25px; cursor:pointer" ></i>
                ';
}

function colorSened($muddet = null, $location = null)
{
    $color  = '';
    if($location !== null){
        if($muddet == '-' || $muddet == null){
            $color = '';
        }else{
            $currentTimeInUnix = time();
            $enSonIcraTarixi = strtotime($muddet);
            $diff = floor(($enSonIcraTarixi - $currentTimeInUnix)/60/60/24);

            if($diff > 10){
                $color = 'grey';
            }else if ($diff >= 4 && $diff <= 10){
                $color = 'yellow';
            }else if ($diff < 4){
                $color = 'red';
            }
        }
    }
    else{
        $muddet = ceil($muddet);
        $tab = isset($_POST['filt_vaxt']) && is_string($_POST['filt_vaxt']) ? $_POST['filt_vaxt'] : 'vaxti_kecib';

        if($tab == "vaxti_kecib" || $muddet <= 3)
        {
            $color = 'danger';
        }
        else if($muddet <= 10 AND $muddet >= 4)
        {
            $color = 'warning';
        }
    }


    return $color;
}

?>
