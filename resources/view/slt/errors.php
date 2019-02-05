<style>
	.slt-err-block{
		width: 100%;
		height: auto;
		padding: 20px;
		background-color: #444;
		color: #eee;
		font-family: Courier !important;
		font-size: 15px;
	}

	.slt-err-block .line{
		display: block;
		padding: 10px;
		background: #eee;
		font-weight: bold;
	}

	.slt-err-block .line.error{
		background-color: #ccc;
		color: #B71C1C;
	}

	.slt-err-block code{
		padding: 10px;
		color: #00695C;
	}

	.slt-err-block code .line b{
		padding-right: 10px;
		border-right: 2px solid #ccc;
		display: inline-block;
		margin-right: 10px;
		font-weight: normal;
	}
</style>
<div class="slt-err">
	<!-- <h1 class="slt-err-title">ERRORS (count <?= count($errs) ?>)</h1>-->
	<?php foreach ($errs as $err): ?>
		<?= $err ?>
	<?php endforeach ?>
</div>
