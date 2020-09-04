<?php

require_once '../../../prodoc/ajax/dashboard/tarixce/history_operation.php';

$operation_names = array_merge($relatedKeyAndOperationTextMap, $operationTextMap);


foreach ($senedler as $sened) {

    $rowCount++;


        ?>

        <tr class='clickable-row' data-tip="<?php print $sened['tip']; ?>" sened-id='<?php print $sened['id']; ?>' ">
        <td class="number" style=" width: 88px;"><?php print $rowCount ?></td>
        <td class="document_number" style="width: 177px"><?php print escape($sened['document_number']) ?>
        </td>
        <td style="width: 122px"><?php print escape($sened['nov']) ?></td>
        <td style="text-align:  left; width: 265px">
            <?php foreach ($sened['created_at'] as $created_at): ?>
                <?php tarixCapEt($created_at) ?>,<br>
            <?php endforeach; ?>
        </td>
        <td style="width: 156px">
            <div>
                <?php foreach ($sened['operation'] as $operation): ?>
                   <span data-toggle="tooltip" title="<?= $operation_names[$operation] ?>"> <?php print escape(substr($operation_names[$operation],0,13-strlen($operation_names[$operation]))).'...' ?></span><br>

                <?php endforeach; ?>
            </div>
        </td>
        </tr>

        <?php

}
