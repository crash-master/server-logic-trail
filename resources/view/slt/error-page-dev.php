<div class="slt-err-block">
	<div class="slt-err-block-head">
		<h1><?= $err_type ?> <small>code(<?= $errno ?>)</small></h1>
		<p><strong>Error text:</strong> <?= $errstr ?></p>
	</div>
	<div class="slt-err-block-body">
		<p><strong>In file:</strong> <?= $errfile ?> <strong>on line</strong> <?= $errline ?></p>
		<code>
			<?php foreach ($code as $inx => $line): ?>
				<?php if ($inx == $errline): ?>
					<span class="line error"><b><?= $inx ?></b> <?= $line ?></span>
				<?php else: ?>
					<span class="line"><b><?= $inx ?></b> <?= $line ?></span>
				<?php endif; ?>
			<?php endforeach ?>
		</code>
	</div>
</div>
