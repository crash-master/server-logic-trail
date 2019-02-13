<?php vjoin(auth_path('view/layouts/head')); ?>
	<div class="container">
		<div class="row">
			<div class="col-6 offset-3">
				<div class="card" style="width: 18rem; margin: auto; margin-top: 15%">
				  <div class="card-body">
				    <h5 class="card-title">Sign In</h5>
				    <form action="/auth/form/signin" method="post">
				    	<input type="hidden" name="auth-signin">
					    <p class="card-text">
					    	<div class="form-group">
					    		<label for="nickname">Your Nickname</label>
					    		<input type="text" id="nickname" name="nickname" class="form-control" placeholder="Your Nickname">
					    	</div>
					    	<div class="form-group">
					    		<label for="password">Your Password</label>
					    		<input type="password" id="password" name="password" class="form-control" placeholder="Your Password">
					    	</div>
					    </p>
					    <button class="btn btn-primary card-link">Sign In</button>
					    <a href="/auth/signup-page" class="btn card-link">Sign Up</a>
					</form>
				  </div>
				</div>
			</div>
		</div>
	</div>
<?php vjoin(auth_path('view/layouts/footer')); ?>