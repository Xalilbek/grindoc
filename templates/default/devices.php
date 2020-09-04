<link rel="stylesheet" type="text/css" href="assets/plugins/jquery-ui/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="assets/plugins/bootstrap-datetimepicker/css/datetimepicker.css"/>
<link rel="stylesheet" type="text/css" href="assets/plugins/select2/select2_metro.css"/>
<link rel="stylesheet" type="text/css" href="asset/global/plugins/uniform/css/uniform.default.css?v1"/>

<div class="modal-body">
    <table class="table table-striped table-bordered table-advance table-hover">
        <thead>
        <tr>
            <th>№</th>
            <th>Qurğunun növü</th>
            <th>Modeli</th>
            <th>Miqdarı</th>
            <th>Surprizli</th>
            <th>Tələ məftili</th>
        </tr>
        </thead>
        <tbody>
        <?php $i = 0; foreach ($docs as $doc): $i++; ?>
            <tr>
                <td><?php print $i; ?></td>
                <td><?php print htmlspecialchars($doc['qurgunun_novu']); ?></td>
                <td><?php print htmlspecialchars($doc['modeli']); ?></td>
                <td><?php print htmlspecialchars($doc['miqdari']); ?></td>
                <td><?php print htmlspecialchars($doc['surprizli_ad']); ?></td>
                <td><?php print htmlspecialchars($doc['tele_meftili_ad']); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="modal-footer" style="border: 0;">
    <div style="float: right;">
        <button type="button" data-dismiss="modal" class="btn default btn-circle">
            Bağla
        </button>
    </div>
</div>

<script type="text/javascript">

    $(function() {

    });

</script>