<?php

function updateFileId ($moduleEntryId,$fileId,$moduelName)
{
    if(count($fileId)>0){
        DB::query("Update tb_files SET module_entry_id = ".$moduleEntryId." WHERE created_by =".(int)$_SESSION['erpuserid']." AND id IN  (".implode(',',$fileId).") AND module_name = '".$moduelName."' AND is_deleted = 0   ");
    }
}
