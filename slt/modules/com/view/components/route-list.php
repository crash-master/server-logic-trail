<?php if(count($routes)): ?>
<?php foreach($routes as $method => $routeList): ?>
	<?php if(count($routeList)): ?>
	<ul class="collection with-header">
		<li class="collection-header"><h4 class="red-text text-darken-2" title="Method name">Routes from <?= $method ?></h4></li>
	<?php foreach($routeList as $route => $action): ?>
		<li class="collection-item">
    		<strong <?php if($method == 'get'): ?> title="Route" <?php else: ?> title="Post row" <?php endif; ?>>
    			Route: <?= $route == '' ? '/' : $route ?>
    		</strong><br>
    		<span class="teal-text darken-3">
				<?php if($method == 'get'): ?>
			    	<span title="Action"><?= is_string($action) ? $action : 'anonymous function(){}' ?></span>
				<?php else: ?>
					<?php if($action['route']): ?>
			    		<span title="Route"><?= $action['route'] ?></span> : <span title="Action"><?= $action['action'] ?></span>
			    	<?php else: ?>
			    		<span title="Action"><?= $action['action'] ?></span>
			    	<?php endif; ?>
				<?php endif; ?>
			</span>
		</li>
	<?php endforeach; ?>
	</ul>
	<?php endif; ?>
<?php endforeach; ?>
<?php endif; ?>