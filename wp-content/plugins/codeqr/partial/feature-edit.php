<style>
	<?php  include 'styles.php'; ?>
</style>

<div class="wrap">

	<?php
	if( isset($status) && !is_null($status) && !empty($status) ) if( $status['status'] == 'success' ):?>
		<div class="notice notice-success is-dismissible">
	    <p><?php echo $status['message']; ?></p>
		</div>
	<?php else  : ?>
		<div class="notice notice-error is-dismissible">
	    <p><?php echo $status['message']; ?></p>
		</div>
	<?php endif; ?>

	<?php if( isset($_GET) && isset($_GET['feature_id'])): ?>
		<h2><?php esc_html_e( 'Edit QR Feature' ); ?></h2>
	<?php else : ?>
		<h2><?php esc_html_e( 'Add QR Feature' ); ?></h2>
	<?php endif; ?>
	<hr>
	<div class="card" style="max-width: 800px">
		<?php if( isset($results) && isset($results[0]->feature_name) ):?>
		<h3><?php echo stripslashes($results[0]->feature_name); ?></h3>
		<?php endif; ?>
		<!-- id="qr-feature-form" -->
		<form action="" method="POST" id="feature-list-form" enctype="multipart/form-data">
			<?php 
			if( isset($_GET['page']) && ( $_GET['page'] == 'codeqr-list_feats' || $_GET['page'] == 'codeqr-add_feat' ) ){
				echo '<input type="hidden" name="action" value="add-feature">';
				if( $_GET['page'] == 'codeqr-list_feats' ){

					echo '<input type="hidden" name="feature_id" value="'.$_GET['feature_id'].'">';
				}
			}
			?>
			<table class="form-table">
				<tbody>
					<tr>
						<th>
							<label for="filename">QR Feature Name</label>
						</th>
						<td>
							<input type="text" name="feature_name" id="feature_name" value="<?php echo isset($results) && isset($results[0]->feature_name) ? stripslashes($results[0]->feature_name) : '';?>">
						</td>
						<td></td>
					</tr>
					<tr>
						<th>
							<label for="filename">QR Feature Code</label>
						</th>
						<td>
							<input type="text" name="feature_code" id="feature_code" value="<?php echo isset($results) && isset($results[0]->feature_code) ? stripslashes($results[0]->feature_code) : '';?>">
						</td>
						<td></td>
					</tr>
					<tr>
						<th>
							<label for="filename">QR Feature Description</label> 
						</th>
						<td>
							<textarea name="description" id="description" ><?php echo isset($results) && isset($results[0]->description) ? stripslashes($results[0]->description) : '';?></textarea>
						</td>
						<td></td>
					</tr>
					<tr>
						<th>
							<label for="filename">QR Image</label>
						</th>
						<td>
							<?php 
								if(isset($results) && !empty($results[0]) && !is_null($results[0]->feature_image)){
									echo '<div class="feature_image"><input type="hidden" value="true" name="image">';
									echo '<img style="max-width: 150px; height: auto;" src="'.plugin_dir_url(__DIR__).'/qr_codes/'.$results[0]->feature_image.'" alt="">
										<div class="remove_image"><span style="color: red;" class="dashicons dashicons-no-alt"></span></div>
									</div>';
								}
							?>
							<input type="file" name="feature_qr_upload" id="feature_qr_upload" value="<?php echo isset($results) && isset($results[0]->feature_qr_upload) ? $results[0]->feature_qr_upload : '';?>">
						</td>
						<td></td>
					</tr>
					</tbody>
					<tr>
						<th>
							<label for="filename">QR Option Category</label>
						</th>
						<td>
							<?php if( isset($data) && isset($data['feature_categories']) ) : 
							foreach( $data['feature_categories'] as $key => $feat_cat ): ?>
							<p>
								<input type="checkbox" 
									<?php 
										echo isset($data) && isset($data['selected_categories']) && !empty($data['selected_categories']) && in_array(str_replace(' ', '-', strtolower($feat_cat->name)), $data['selected_categories'])
										? 'checked="checked"' 
										: ''; ?> 
									name="feature_categories[<?php echo $key; ?>]" 
									value="<?php echo strtolower($feat_cat->slug); ?>"> <?php echo $feat_cat->name; ?>
							</p>
							<?php endforeach;
							endif;?>
						</td>
						<td></td>
					</tr>
					<tr>
						<th>
							<label for="filename">&nbsp;</label>
						</th>
						<td>
							
								<input type="submit" value="Submit" name="Submit" style="float: right;">
						</td>
						<td></td>
					</tr>
				</tbody>
			</table>
		</form>
	</div>


	<?php
	/*<div class="card" style="max-width:800px">
		
		<table>
			<tbody>
					<tr>
						<th>
							&nbsp;
						</th>
						<th colspan="2">
							<label for="filename" style="width: 100%; text-align: left; display: block;">Search Available Products</label>
							<input id="documents" placeholder="Search Products">
						</td>
					</tr>

					<?php 
						ob_start();
						include 'feature-assoc.php';
						echo ob_get_clean();
					?>
			</tbody>
		</table>
	</div>*/
	?>

	<div id="feature-list-template" style="display: none">
		<?php include 'feature-item.php';?>
	</div>

	<?php include 'feature-script.php'; ?>


</div><!-- .wrap -->
