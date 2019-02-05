<ul class="collection with-header">
	<li class="collection-header">
		<h4 class="red-text text-darken-2">Create new</h4>
	</li>
	<li class="collection-item">
		<label for="">Model</label>
		<input type="text" data-request="/com/create/model/{name}" placeholder="Input model name">
		<br>
		<label for="">Controller</label>
		<input type="text" data-request="/com/create/controller/{name}" placeholder="Input controller name">
		<br>
		<label for="">Migration</label>
		<input type="text" data-request="/com/create/migration/{name}" placeholder="Input migration name">
		<br>
		<label for="">Set</label>
		<input type="text" data-request="/com/create/set/{name}" placeholder="Input set name">
	</li>
</ul>