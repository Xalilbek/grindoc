<?php
$sira=0;

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
    $muddet = isset($sened['icra_muddeti']) ? $sened['icra_muddeti'] : '';

    if(!empty($muddet))
    {
        $color = colorSened($muddet);
    }

    $icra_tarixi=explode(' ',$sened['icra_tarixi']);

    if($sened['icra_tarixi']!='-')
    $sened['icra_tarixi'] = $icra_tarixi[1].' '.$icra_tarixi[0].' '.$icra_tarixi[2];
    ?>
    <tr class='clickable-row <?= $color ?>' sened-id='<?php print $sened['id']; ?>' data-tip="<?php print $sened['tip']; ?>" >
        <td class="number" ><?php print $sira ?></td>
        <td class="document_number" style="width: 20%"><?php print escape($sened['nomre']).' / '; print tarixCapEt($sened['created_at']); ?></td>
        <td style=" text-align:  center;"><?php print escape($sened['muraciet_eden_ad']) ?></td>
        <td style=" text-align:  center;"><?php print escape($sened['gonderen_teshkilat_ad']) ?></td>
        <td style=" text-align:  center;"><?php print escape($sened['created_by']) ?></td>
        <td style=" text-align:  center;"><?php print escape($sened['executors']) ?></td>
        <td style=" text-align:  center;"><?php print escape($sened['qisa_mezmun']) ?></td>
        <td style=" text-align:  center;"><?php print $sened['icra_tarixi'] ?></td>
        <td style=" text-align:  center;"><?php print tarixCapEt($sened['baglanma_tarixi']) ?></td>

    </tr>
    <?php
}

function warningBtn(Document $incomingDocument, $nezaret)
{
    if (!$incomingDocument->senedNezaretEdeBiler()) {
        return;
    }

    return '
             <i class="fa fa-exclamation-triangle sened_nezaret_et" id="warningIcon" style="color:  '.$nezaret.'; font-size:  24px; margin-top:  7px; margin-left: 25px; cursor:pointer" ></i>
                ';
}

function colorSened($muddet)
{
    $muddet = ceil($muddet);
    $color  = '';

    if($muddet <= 3)
    {
        $color = 'danger';
    }
    else if($muddet <= 10 AND $muddet >= 4)
    {
        $color = 'warning';
    }

    return $color;
}
?>
