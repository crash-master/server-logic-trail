<?php vjoin(auth_path('view/layouts/head')); ?>
	<div class="container">
		<div class="row">
			<div class="col-6 offset-3">
				<div class="card" style="width: 18rem; margin: auto; margin-top: 15%">
				  <div class="card-body">
				    <h5 class="card-title"><?= $text['title'] ?></h5>
				    <p class="catd-text"><?= $text['description'] ?></p>
				    <form action="/auth/form/signup" method="post">
				    	<input type="hidden" name="auth-signup">
					    <p class="card-text">
					    	<div class="form-group">
					    		<label for="email"><?= $text['fields_titles']['email'] ?></label>
					    		<input type="text" id="email" name="email" class="form-control" placeholder="<?= $text['fields_titles']['email'] ?>">
					    	</div>
					    	<div class="form-group">
					    		<label for="nickname"><?= $text['fields_titles']['nickname'] ?></label>
					    		<input type="text" id="nickname" name="nickname" class="form-control" placeholder="<?= $text['fields_titles']['nickname'] ?>">
					    	</div>
					    	<hr>
					    	<div class="form-group">
					    		<label for="password"><?= $text['fields_titles']['password'] ?></label>
					    		<input type="password" id="password" name="password" class="form-control" placeholder="<?= $text['fields_titles']['password'] ?>">
					    	</div>
					    	<div class="form-group">
					    		<label for="password_again"><?= $text['fields_titles']['password_again'] ?></label>
					    		<input type="password" id="password_again" name="password_again" class="form-control" placeholder="<?= $text['fields_titles']['password_again'] ?>">
					    	</div>
					    </p>
					    <button class="btn btn-primary card-link"><?= $text['signup_btn'] ?></button>
					    <a href="/auth/signin-page" class="btn card-link"><?= $text['signin_btn'] ?></a>
					</form>
				  </div>
				</div>
			</div>
		</div>
	</div>
<?php vjoin(auth_path('view/layouts/footer')); ?>
