<?php

function getDocumentColorRelatedStatus($sened){

    $color = '';
    if($sened['sened_novu'] == 'xos'){
        if ((int)$sened['status'] == 1){
            $color = 'green';
        }elseif ((int)$sened['status'] == OutgoingDocument::STATUS_LEGV_OLUNUB){
            $color = 'black';
        }
        else{
            $color = 'blue';
        }
    }else{
        if ((int)$sened['status'] == Document::STATUS_ACIQ) {
            $color = 'blue';
        } elseif ((int)$sened['status'] == Document::STATUS_BAGLI) {
            $color = 'green';
        } elseif ((int)$sened['state'] == Document::STATE_IN_TRASH) {
            $color = 'black';
        }
    }

    return $color;
}