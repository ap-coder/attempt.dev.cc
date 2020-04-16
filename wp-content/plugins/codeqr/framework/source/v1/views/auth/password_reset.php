<?php get_header('members'); ?>
<div id="main">
	<div class="page-content">
	
	<div class="col-md-5 middle-align">

		<form action="<?php echo secure_route('auth/reset');?>" id="payment-form" method="GET">				
			
			<h1 style="text-align: center">Password Reset</h1>
			<hr>
			<p>Enter your email and we'll send you a reset link.</p>
			
			<div class="row">
				<div class="">
					<div class="col-sm-8"><input type="email" name="email"></div>
					<div class="col-sm-4"><button>RESET</button></div>
					<div class="col-sm-12">
						<br>
						<?php
							if(isset($data['error'])) echo '<div class="alert alert-danger">'.$data['error'].'</div>';
							if(isset($data['success'])) echo '<div class="alert alert-success">'.$data['success'].'</div>';
						?>
					</div>
				</div>
			</div>

			<input type="hidden" name="action" value="authenticate">
		</form>

		<div class="row" style="padding-bottom: 80px;height: 0"></div>
	</div>

</div>
</div>
<?php get_footer(); ?>