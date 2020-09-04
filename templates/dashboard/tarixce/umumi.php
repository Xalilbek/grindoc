<li class="mt-list-item">
	<div class="list-datetime bold uppercase font-green">
        <?php tarixCapEt($tarixce['date']) ?>
	</div>

	<div class="list-item-content">
		<h3 class="uppercase">
			<a href="javascript:;"><?php cap($tarixce_ne) ?></a>
		</h3>

		<span class="text-muted">
			<i class="fa fa-user"></i> Əməliyyatçı: <?php cap($tarixce['user_ad']) ?>
		</span>

		<br>
		<span class="text-muted">
			<i class="fa fa-pencil"></i> Qeyd: - <?php \Util\View::altPrint(htmlspecialchars($tarixce['text']), '<i>Yoxdur</i>'); ?>
		</span>
	</div>
</li>