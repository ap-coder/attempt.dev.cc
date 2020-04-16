<?php get_header('members'); ?>

<?php include('partial/sidebar.php'); ?>

<div id="main" class="none">
	
	<div class="page-content">

		<?php if( 'false' == get_user_meta( $data['user_id'], 'confirmed', true ) ):?>
			<div class="col-sm-12">
				<div class="alert alert-warning">A confirmation email was sent to <strong><?php echo $data['current_user']->user_email; ?></strong> account to confirm your email address. Please check your email and click the confirmation link.</div>
			</div>
		<?php endif;?>

		<?php if(!isset($data['plan']) && !current_user_can( 'manage_options' )):?>
			<div class="col-sm-12">
				<div class="alert alert-info">Non Support Plan customers may still post tickets for a support request. All requests will be estimated &amp; invoiced at full development rate.</div>
			</div>
		<?php endif;?>

		<div class="col-sm-12">

			<div class="row">
				<div class="col-sm-8">
					<h4>Subscription</h4>			
					<?php if(!isset($data['plan'])):?>
						<div class="row"><div class="col-sm-12"><p>You are not enrolled in any Support Plans at this time. <a href="<?php echo site_url( '/pricing' )?>">Choose a Plan.</a></p><br /></div></div>
					<?php else: ?>
						<div class="current-subscription well">
							<div class="col-sm-12">
									<div class="row">
										<div>
											<h3><?php echo $data['plan']['name'];?> : $<?php echo (int)$data['plan']['amount'] * 0.01?> / <?php echo $data['plan']['interval']?></h3>								
										</div>
										<div class="row">
											<div class="col-sm-4">
												<ul>
													<li><strong><?php
													if( isset($data['plan']) ):
														if( strrpos($data['plan']['id'], 'basic')) echo '2';
														if( strrpos($data['plan']['id'], 'pro')) echo '6';
														if( strrpos($data['plan']['id'], 'enter')) echo '12';
													endif;
													?> Hours Dev Time</strong></li>
													<li>Discounted Hourly Rate  </li>
												</ul>
											</div>
											<div class="col-sm-4">
												<ul>
													<li>Free Wordpress Plugin Updates  </li>
													<li>Unlimited Wordpress Training  </li>
												</ul>
											</div>																
											<div class="col-sm-4">
												<ul>
													<li>Remote Desktop Support  </li>
													<li>Free Staging/Sandbox Site</li>
												</ul>
											</div>
											<div class="col-sm-12">
												You may <a href="<?php echo site_url('/pricing')?>">change your plan</a> at any time
											</div>
										</div>
									</div>
							</div>
						</div>
					<?php endif; ?>
				</div>

				<div id="account-stats" class="col-sm-4">
					<h4>Account Stats</h4>
					<div class="well dashboard-widget">

						<div class="row">
							
							<div class="col-sm-6">
										
								<p>
								<span class="large">
									<?php echo $data['hours_used'];?>
								</span><br />
								Hours Used</p>
							</div>
							<div class="col-sm-6">
								<p>
								<span class="large">
									<?php
									// get the amount of dev hours per plan
									if( isset($data['plan']) ):
										if( strrpos($data['plan']['id'], 'basic')) $included_hours =  2;
										if( strrpos($data['plan']['id'], 'pro')) $included_hours =  6;
										if( strrpos($data['plan']['id'], 'enter')) $included_hours =  12;
										$available_hours = $included_hours - floatval($data['hours_used']);
										echo $available_hours;
									endif;
									?>
								</span><br />
								Hours Available</p></div>
						</div>
					</div>
				</div>
			</div>


			<div class="row">
				<div class="col-md-4">	
					<h4>Tickets</h4>	
					<div class="well dashboard-widget">
						<div class="left" style="padding-right: 15px;">
							<div class="number-token">
								<?php echo $data['ticket_total'];?>
							</div>
						</div>
						<div class="none" style="display: inline-block;">	
								<?php if( count($data['ticket_counts'])):?>
									<h5>Ticket Breakdown</h5>			
									<ul style="list-style: none; padding: 0; margin: 0">		
									<?php foreach( $data['ticket_counts'] as $count): ?>
										<li style="border-bottom: 1px solid #ccc; padding: 0 15px 5px 0; margin-bottom: 5px; font-size:0.9em;">
											<?php echo $count->status_count; ?> - <?php echo $count->status; ?></li>
									<?php endforeach; ?>
									</ul>
								<?php else: ?>
									<p>No tickets at this time. <a href="<?php echo route('account/tickets');?>">Create Ticket.</a></p>
								<?php endif;?>
						</div>
					</div>
				</div>
				<div class="col-md-4">	
					<h4>Messages</h4>	
					<div class="well dashboard-widget">
						<div>
							<ul style="list-style: none; padding: 0; margin: 0">						
								<?php if( count($data['messages']) ) :?>
									<?php foreach( $data['messages'] as $message): ?>
										<li style="border-bottom: 1px solid #ccc; padding: 0 0 5px 0; margin-bottom: 5px; font-size:0.9em;">
											<a href="<?php echo route('account/ticket/'.$message->ticket_id.'/view')?>">
										<h5><?php echo $message->project_name; ?> <br /><span class="small">From: <?php echo $message->first_name . ' ' . $message->last_name; ?></span></h5>
											<?php echo stripslashes($message->message); ?><span class="right"><?php echo date('n/j/y', strtotime($message->created_at))?></span></a></li>
									<?php endforeach; ?>
								<?php else: ?>
									<p>No Messages yet. Soon :)</p>
								<?php endif;?>
							</ul>
						</div>
					</div>
				</div>
				<div class="col-md-4">	
					<h4>Projects</h4>	
					<div class="well dashboard-widget">
						<div>
							<ul style="list-style: none; padding: 0; margin: 0">						
								<?php if( count( $data['projects'] )):?>
									<?php foreach( $data['projects'] as $project): ?>
										<li style="border-bottom: 1px solid #ccc; padding: 0 0 5px 0; margin-bottom: 5px; font-size:0.9em;"><a href="<?php echo route('account/project/'.$project->id.'/view'); ?>"><?php echo $project->name; ?></a></li>
									<?php endforeach; ?>
								<?php else: ?>
									<p>No Projects yet. <a href="<?php echo route('account/projects/add')?>">Create Project.</a></p>
								<?php endif;?>
							</ul>
						</div>
					</div>
				</div>
			</div>

		</div>

	</div>

</div>

<?php get_footer(); ?>