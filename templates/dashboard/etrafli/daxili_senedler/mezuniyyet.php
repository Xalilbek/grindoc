<div>
    <div class='page-header'>
        <p><small>Məzuniyyət alacaq şəxs</small><br>&nbsp;<?php cap($senedler['mezuniyyet_alacaq_shexs']) ?></p>
        <p><small>İlk tarix</small><br>&nbsp;<?php tarixCapEt($senedler['start_date'], 'd M Y') ?></p>
        <p><small>Məzuniyyət müddəti</small><br>&nbsp;<?php cap($senedler['number_of_days']) ?></p>
        <p><small>Son tarix</small><br>&nbsp;<?php tarixCapEt($senedler['end_date'], 'd M Y') ?></p>
        <p><small>Təyinat</small><br>&nbsp;<?php cap($senedler['teyinat_ad']) ?></p>
        <p><small>Qoşma</small><br>&nbsp;</p>
        <p><small>Məlumat</small><br>&nbsp;<?php cap($senedler['about']) ?></p>
        <p><small>Status</small><br>&nbsp;<?php cap($senedler['status_ad']) ?></p>
    </div>
</div>