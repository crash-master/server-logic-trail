<?php vjoin(auth_path('view/layouts/head')); ?>
	<div class="container">
		<div class="row">
			<div class="col-6 offset-3">
				<div class="card" style="width: 18rem; margin: auto; margin-top: 15%">
				  <div class="card-body">
					<h5 class="card-title"><?= $text['title'] ?></h5>
					<p class="card-text"><?= $text['description'] ?></p>
					<form action="/auth/form/signin" method="post">
						<input type="hidden" name="auth-signin">
						<p class="card-text">
							<div class="form-group">
								<label for="<?= $signin_field ?>"><?= $text['fields_titles'][$signin_field] ?></label>
								<input type="text" id="<?= $signin_field ?>" name="<?= $signin_field ?>" class="form-control" placeholder="<?= $text['fields_titles'][$signin_field] ?>">
							</div>
							<div class="form-group">
								<label for="password"><?= $text['fields_titles']['password'] ?></label>
								<input type="password" id="password" name="password" class="form-control" placeholder="<?= $text['fields_titles']['password'] ?>">
							</div>
						</p>
						<button class="btn btn-primary card-link"><?= $text['signin_btn'] ?></button>
						<a href="/auth/signup-page" class="btn card-link"><?= $text['signup_btn'] ?></a>
					</form>
				  </div>
				</div>
			</div>
		</div>
	</div>
<?php vjoin(auth_path('view/layouts/footer')); ?>