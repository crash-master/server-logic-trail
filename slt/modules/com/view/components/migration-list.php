<?php if(count($migrations)): ?>
	<ul class="collection with-header">
		<li class="collection-header">
			<h4 class="red-text text-darken-2" title="Package name">Migrations</h4>
		</li>
		<li class="collection-header">
			<label for="">Migration Up <i class="material-icons">cloud_upload</i></label>
			<input data-request="/com/migrations/up/{name}" type="text" placeholder="Input migration name">
			<br>

			<label for="">Migration Down <i class="material-icons">cloud_download</i></label>
			<input data-request="/com/migrations/down/{name}" type="text" placeholder="Input migration name">

			<label for="">Migration Refresh <i class="material-icons">refresh</i></label>
			<input data-request="/com/migrations/refresh/{name}" type="text" placeholder="Input migration name">
			<div class="row">
				<div class="col s12">
					<h6>All</h6>
					<a href="/com/migrations/up" class="btn orange darken-1 confirm"><i class="large material-icons ">cloud_upload</i></a>
					<a href="/com/migrations/down" class="btn orange darken-1 confirm"><i class="large material-icons ">cloud_download</i> </a>
					<a href="/com/migrations/refresh" class="btn orange darken-1 confirm"><i class="large material-icons ">refresh</i></a>
				</div>
			</div>
		</li>
	<?php foreach($migrations as $i => $migration): ?>
		<li class="collection-item">
        		<strong title="Migration name"><?= $migration['name'] ?></strong>
        		<span class="teal-text darken-3" title="Action">		
				        <p><?= str_replace('//', '/', $migration['path']) ?></p>
				</span>
		</li>
	<?php endforeach; ?>
	</ul>
<?php endif; ?>