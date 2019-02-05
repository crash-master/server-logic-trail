<?php Kernel\View::join(Kernel\Module::pathToModule('com').'view/head') ?>

<div class="container">	
	<?php Kernel\View::join(Kernel\Module::pathToModule('com').'view/header') ?>
	<div class="row">
		<div class="col s6">
			<!-- Create -->
			<?php vjoin(Kernel\Module::pathToModule('com').'view/components/create-form') ?>
			<!-- Components -->
			<?php vjoin(Kernel\Module::pathToModule('com').'view/components/component-list') ?>
			<!-- Events -->
			<?php vjoin(Kernel\Module::pathToModule('com').'view/components/event-list') ?>
		</div>
		<div class="col s6">
			<!-- Migrations -->
			<?php vjoin(Kernel\Module::pathToModule('com').'view/components/migration-list') ?>
			<!-- Routes -->
			<?php vjoin(Kernel\Module::pathToModule('com').'view/components/route-list') ?>
		</div>
	</div>

	
</div>

<?php Kernel\View::join(Kernel\Module::pathToModule('com').'view/footer') ?>