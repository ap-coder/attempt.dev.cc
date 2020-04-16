
	<div id="sidebar" class="left">
		
		<div class="sidebar-heading">
			<?php	$name = get_full_name();?>
			<strong>Welcome, <?php echo "{$name->firstname} {$name->lastname}";?>!</strong>
		</div>

		<div class="sidebar-widget nav">
			<ul>
				<?php if( current_user_can( 'manage_options' )):?>
				<li><a href="<?php echo route('admin/dashboard')?>">Dashboard</a></li>
				<li><a href="<?php echo route('admin/projects')?>" >Projects</a></li>
				<li><a href="<?php echo route('admin/tickets')?>">Tickets</a></li>
				<li><a href="<?php echo route('admin/invoicing')?>">Invoicing</a></li>
				<?php else: ?>
				<li><a href="<?php echo route('account/dashboard')?>" >Dashboard</a></li>
				<li><a href="<?php echo route('account/projects')?>" >Projects</a></li>
				<li><a href="<?php echo route('account/tickets')?>" >Tickets</a></li>
				<li><a href="<?php echo route('account/settings')?>" >Account</a></li>
				<?php endif;?>
			</ul>
		</div>

	</div>