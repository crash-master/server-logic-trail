<?php if(count($events)): ?>
	<ul class="collection with-header">
		<li class="collection-header"><h4 class="red-text text-darken-2" title="Method name">Events</h4></li>
	<?php foreach($events as $name => $count): ?>
		<li class="collection-item">
    		<strong>
    			<?= $name ?>
    		</strong> -
    		<strong class="teal-text darken-3">
				(<?= $count ?>)
			</strong>
		</li>
	<?php endforeach; ?>
	</ul>
<?php endif; ?>