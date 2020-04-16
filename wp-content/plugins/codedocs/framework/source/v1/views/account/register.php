<?php get_header('members'); if( session_id() ) session_destroy();?>

<div id="main">
	<div class="page-content">
		<div class="col-sm-12 col-md-6 col-md-offset-3">

			<form action="<?php echo secure_route('auth/register'); ?>" id="payment-form" method="POST" class="ajax-save" data-validate="<?php echo route('auth/validate')?>">		
				<input type="hidden" name="action" value="register">
				<h1 style="text-align: center">Register</h1>
				<div style="height: 30px"></div>
										
				<div class="row">
					<div class="col-sm-12">Account Holder's Name*</div>
					<div class="col-sm-6"><p><input type="text" name="fname" value=""></p></div>
					<div class="col-sm-6"><p><input type="text" name="lname" value=""></p></div>

					<div class=" col-sm-12">
						<p>Company* <br />
							<input type="text" name="company" value="">
						</p>
					</div>

					<div class=" col-sm-12 col-md-4">
						<p>City* <br />
							<input type="text" name="address_city" value="">
						</p>
					</div>

					<div class=" col-sm-12 col-md-4">
						<p>State* <br />
							<select name="address_state" id="address_state" class="form-control">
								<option value="">-- SELECT STATE -- </option>
								<?php foreach(fifty_states() as $abbr => $state):?>
								<option value="<?php echo $state;?>"><?php echo $abbr; ?></option>
								<?php endforeach;?>
							</select>
						</p>
					</div>

					<div class=" col-sm-12 col-md-4">
						<p>Zip* <br />
							<input type="text" name="address_zip" value="">
						</p>
					</div>
					<div class="col-sm-12"><p>Email <br /> <input type="email" name="email"></p></div>
					<div class="col-sm-12"><p>Password <br /><input type="password" name="password" value=""></p></div>
					<div class="col-sm-12"><p>Password Again <br /><input type="password" name="password2" value=""></p></div>
					<div class="col-sm-12"><br /><input type="submit" value="Submit" class="right"></div>
				</div>

			</form>

		<div class="row" style="padding-bottom: 80px;height: 0"></div>
	</div>
	</div>
</div>

<?php get_footer(); ?>