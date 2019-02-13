<?php vjoin(auth_path('view/layouts/head')); ?>
	<div class="container">
		<div class="row">
			<div class="col-6 offset-3">
				<div class="card" style="width: 18rem; margin: auto; margin-top: 15%">
				  <div class="card-body">
				    <h5 class="card-title">Sign Up</h5>
				    <form action="/auth/form/signup" method="post">
				    	<input type="hidden" name="auth-signup">
					    <p class="card-text">
					    	<div class="form-group">
					    		<label for="email">Your Email</label>
					    		<input type="text" id="email" name="email" class="form-control" placeholder="Your Email">
					    	</div>
					    	<div class="form-group">
					    		<label for="nickname">Your Nickname</label>
					    		<input type="text" id="nickname" name="nickname" class="form-control" placeholder="Your Nickname">
					    	</div>
					    	<hr>
					    	<div class="form-group">
					    		<label for="password">Your Password</label>
					    		<input type="password" id="password" name="password" class="form-control" placeholder="Your Password">
					    	</div>
					    	<div class="form-group">
					    		<label for="password2">Your Password Again</label>
					    		<input type="password" id="password2" name="password2" class="form-control" placeholder="Your Password Again">
					    	</div>
					    </p>
					    <button class="btn btn-primary card-link">Sign Up</button>
					    <a href="/auth/signin-page" class="btn card-link">Sign In</a>
					</form>
				  </div>
				</div>
			</div>
		</div>
	</div>
<?php vjoin(auth_path('view/layouts/footer')); ?>
