<?php
$TenantId = $user->getActiveTenantId();

?>

<?php print $intDoc->getDetailedInformationHTML() ?>

<p>
    <small>Sənəd tarixi:</small><br>
    <span><?php print htmlspecialchars($poa['petition_date']); ?></span>
</p>
<p>
    <small>Əməkdaş:</small><br>
    <span><?php print htmlspecialchars($poa['user_name']); ?></span>
</p>
<p>
    <small>Vəzifə:</small><br>
    <span><?php print htmlspecialchars($poa['vezife']); ?></span>
</p>
<p>
    <small>Qeyd:</small><br>
    <span style="word-wrap: break-word;" ><?php print htmlspecialchars($poa['note']); ?></span>
</p>

<?php print $intDoc->getRelatedInternalDocumentsHTMLTree(); ?>
<script>

</script>
