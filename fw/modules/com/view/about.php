<?php Kernel\View::join(Kernel\Module::pathToModule('com').'view/head') ?>

<div class="container white-text">

	<?php Kernel\View::join(Kernel\Module::pathToModule('com').'view/header') ?>
    
    <h1>Com</h1>
    <p>Module for fw framework <strong class="teal-text">Ver. 2.0</strong> for FW</p>
    <p><a href="/com">Go to dashboard</a></p>
    
</div>

<?php Kernel\View::join(Kernel\Module::pathToModule('com').'view/footer') ?>