<?php get_header('members'); ?>

<div id="main">
	<div class="page-content">
		<div class="col-sm-12 col-md-4 col-md-offset-4">

			<form action="<?php echo secure_route('auth/login');?>" method="POST" class="ajax-save" data-validate="<?php echo route('auth/validate')?>">				
				
				<h1 style="text-align: center">Login</h1>
				
				<?php if( isset($data['error']) ):?>
					<div class="alert alert-warning" role="alert"><?php echo $data['error']?></div>
				<?php endif;?>

				<hr>
				
				<?php echo isset($data['error']) && !is_array($data['error']) ? '<p class="error">'.$data['error'].'</p>' : ''; ?>
				
				<p>Email <br />

					<input type="email" name="email">

				</p>
				
				<p>Password <br />

					<input type="password" name="password">

				</p>
				
				<div>
					
				<p>
					<a class="right" href="<?php echo route('auth/reset')?>" style="font-size: 14px;">Forgot your password?</a>
					<input type="checkbox" name="remember" value="true"> Remember Me

				</p>
				</div>

				
				<?php if(isset($_GET['ref'])):?><input type="hidden" name="ref" value="<?php echo $_GET['ref']?>"><?php endif; ?>
				<input type="hidden" name="action" value="authenticate">
				
				<button class="inactive" id="login-submit">LOGIN</button>
				
				<p style="text-align: center">&nbsp;<br />
				
				<a href="<?php echo route('account/register')?>" style="font-size: 18px;">Don't have an Account? Sign Up!</a>
			</p>

			</form>

		</div>
	</div>
</div>

<?php get_footer(); ?>