<?php get_header('members'); ?>

<div id="main">
	<div class="page-content">
<div class="col-md-5 middle-align">

	<h1 style="text-align: center">Password Reset</h1>
	<hr>
	<p class="success">You password was reset successfully. You will receive a confirmation email shortly. <br /><a href="<?php echo site_url().'/r/account/login';?>">Login Now</a></p>

	<?php echo isset($data['error']['message']) ? '<p class="error">'.$data['error']['message'].'</p>' : ''; ?>

	<div class="row" style="padding-bottom: 80px;height: 0"></div>

</div>
</div>
</div>

<?php get_footer(); ?>