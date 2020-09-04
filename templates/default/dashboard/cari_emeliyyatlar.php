<div class="mt-element-list">
    <div class="mt-list-container list-news" style="border-left: none; border-right: none">
        <ul>
            <?php foreach ($cari_emeliyyatlar as $cari_emeliyyat): ?>
                <li class="mt-list-item" style="font-size: 15px;">
                    <span class="text-muted">
                        <?= $cari_emeliyyat['emeliyyat']; ?>: <?= $cari_emeliyyat['userName'] ?>
                        <?php foreach ($cari_emeliyyat['emeliyyatBt'] as $emeliyyat): ?>
                            <span style="font-weight: bold;
                                        font-size: 16px;
                                        color: #36c6d3;">
                            <?= $emeliyyat ?></span>
                        <?php endforeach; ?>
                        <?php if(isset($cari_emeliyyat['number']) && !empty($cari_emeliyyat['number'])): ?>
                            <span style="font-weight: bold;
                                        font-size: 16px;
                                        color: #36c6d3;">
                            <?= $cari_emeliyyat['number'] ?></span>
                        <?php endif; ?>
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>


