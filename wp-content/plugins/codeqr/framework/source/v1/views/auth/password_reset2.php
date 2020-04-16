<?php get_header('members'); ?>

<div id="main">
	<div class="page-content">
	<div class="col-sm-12">
		
		<form action="<?php echo secure_route('auth/reset');?>" id="payment-form" method="POST">				
			
			<h1 style="text-align: center">Login</h1>
			<hr>
			
			<?php echo isset($data['error']) && !is_array($data['error']) ? '<p class="error">'.$data['error'].'</p>' : ''; ?>
			
			<p>Email <br />
				<input type="email" name="email" value="<?php echo $data['email'];?>">
			</p>
			
			<p>Password <br />
				<input type="password" name="password">
			</p>
			<?php echo isset($data['error']['password']) ? '<p class="error">'.$data['error']['password'].'</p>' : ''; ?>
			
			<p>Confirm Password <br />
				<input type="password" name="password2">
			</p>

			<input type="hidden" name="action" value="reset">
			<input type="hidden" name="token" value="<?php echo $data['token'];?>">
			<button class="inactive" id="login-submit">RESET</span></button>
		</form>

		<?php echo isset($data['error']['message']) ? '<p class="error">'.$data['error']['message'].'</p>' : ''; ?>

		<div class="row" style="padding-bottom: 80px;height: 0"></div>
	</div>
	</div>
	</div>
<?php get_footer(); ?>