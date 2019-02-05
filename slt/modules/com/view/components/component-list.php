<?php if(count($components)): ?>
		<ul class="collection with-header">
    		<li class="collection-header"><h4 class="red-text text-darken-2" title="Component name">Components</h4></li>
		<?php foreach($components as $name => $component): ?>
		<?php foreach($component as $view => $action): ?>
				<li class="collection-item">
	        		<strong><?= $name ?></strong><br>
	        		<span title="Path to view"><?= $view ?></span><br>
	        		<span class="teal-text darken-3" title="Action">
						<?php if(!is_array($action)): ?>		
					        <p><?= $action ?></p>
						<?php else: ?>
							<?php foreach($action as $i => $data): ?>
								<p><?= $data ?></p>
							<?php endforeach; ?>
						<?php endif; ?>
					</span>
				</li>
		<?php endforeach; ?>
		<?php endforeach; ?>
		</ul>
	<?php else: ?>
		<p class="card-panel red lighten-3">Components was not found</p>
	<?php endif; ?>
