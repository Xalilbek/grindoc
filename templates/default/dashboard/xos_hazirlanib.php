<?php if ($fromParent): ?>
<div data-parent-li="<?php printf('%s', $derkenar_id); ?>" style="padding-left: 25px ;background-color:  #E8E8E8;">
    <?php else:  ?>
<div>
<?php endif; ?>
<div class="mt-element-list" id="derkenarlar">
    <div class="mt-list-container list-news" style="border-left: none; border-right: none">
        <ul>
            <?php foreach ($outgoingDocuments as $outgoingDocument): ?>
                <li class="mt-list-item" >
                    <div class="list-datetime bold uppercase font-green"> <?php print tarixeCevir($outgoingDocument['created_at']) ?> </div>
                    <div class="list-item-content">
                        <h3 class="uppercase bold" style="display: flex;align-items: center">
                            <a href="index.php?module=prodoc_new&id=<?= $outgoingDocument['id'] ?>&bolme=kurator"><?php print $outgoingDocument['document_number'] ?></a>
                        </h3>
                        <br>
                        <?php if($outgoingDocument['tip'] === 'sened_hazirla'): ?>
                            <span class="text-muted">
                                <i class="fa fa-user"></i> <?= dsAlt('2616xos_hara_gonderilib', "Hara göndərilib"); ?>: <?php print $outgoingDocument['teyinat_ad']; ?>
                            </span>
                            <br>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>




