<link rel="stylesheet" type="text/css" href="assets/plugins/jquery-ui/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="assets/plugins/bootstrap-datetimepicker/css/datetimepicker.css"/>
<link rel="stylesheet" type="text/css" href="assets/plugins/select2/select2_metro.css"/>
<link rel="stylesheet" type="text/css" href="asset/global/plugins/uniform/css/uniform.default.css?v1"/>

<div class="modal-body">
    <table class="table table-striped table-bordered table-advance table-hover">
        <thead>
        <tr>
            <th>№</th>
            <th>Tipi</th>
            <th>Çapı(mm)</th>
            <th>Növü</th>
            <th>Miqdarı(ədəd)</th>
            <th>Qeyd</th>
        </tr>
        </thead>
        <tbody>
        <?php $i = 0; foreach ($docs as $doc): $i++; ?>
            <tr>
                <td><?php print $i; ?></td>
                <td><?php print htmlspecialchars($doc['tipi']); ?></td>
                <td><?php print htmlspecialchars($doc['chapi']); ?></td>
                <td><?php print htmlspecialchars($doc['novu']); ?></td>
                <td><?php print htmlspecialchars($doc['miqdari']); ?></td>
                <td><?php print htmlspecialchars($doc['qeydi']); ?></td>
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