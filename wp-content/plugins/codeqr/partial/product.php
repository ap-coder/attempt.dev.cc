<style>
	<?php  include 'styles.php'; ?>
</style>

<?php 
	global $wpdb;


	// This is for features that are available globally
	$features_list = array();

	// These is the container for features that product owns
	$assoc_features = array();

	$features_list = $wpdb->get_results("SELECT * FROM wp_codeqr_features");

	// Check if product has features
	if( !isset($product) || !is_null($product_id) || (isset($product_id) && is_null($product_id))){
		$product_id = $_GET['product_id'];
	}

	$assoc_features_arr = $wpdb->get_results("
		SELECT features 
		FROM wp_codeqr_product_features 
		WHERE product_id = {$product_id}");
	$assoc_features_arr = wp_list_pluck( $assoc_features_arr, 'features' );
	$rtc_index = NULL;

	// Make sure there's associated features and get rid of rtc
	if( !empty( $assoc_features_arr ) ){

		// Get rid of RTC
		if( in_array('rtc',$assoc_features_arr) ){			
			$rtc_index = array_search('rtc', $assoc_features_arr);
			unset($assoc_features_arr[$rtc_index]);
		}

		// If RTC was the only feature then the array is empty
		// Only run this query if the array is not empty.
		if(!empty($assoc_features_arr)){
			$assoc_features = $wpdb->get_results("
				SELECT * FROM wp_codeqr_features 
				WHERE id IN (".implode(',', $assoc_features_arr).") 
				ORDER BY FIELD(id, ".implode(',', $assoc_features_arr).")");
		}
	}


	$product = $wpdb->get_row("
		SELECT p.post_title, p.ID, cp.*
		FROM `wp_codeqr_products` as cp
		JOIN {$wpdb->prefix}posts as p ON cp.product_id = p.ID 
		WHERE cp.product_id = {$product_id}");

	if( !$product ){

		// Get Product Data
		$product = $wpdb->get_row("
			SELECT p.*, 
			`cpf`.`model_number` as `model_number`, 
			`cpf`.`visible` as `visible`,
			`cpf`.`pdf_nicename` as `pdf_nicename`,
			`cpf`.`pdf_cover` as `pdf_cover`
			FROM {$wpdb->prefix}posts as p
			JOIN wp_codeqr_product_features as cpf ON cpf.product_id = p.ID
			WHERE p.ID={$product_id}");
	}


	if( is_null($product) ){
		$product = $wpdb->get_row("
		SELECT p.*
		FROM {$wpdb->prefix}posts as p
		WHERE p.ID = {$product_id}");
	}

	// Get the Thumbnail from Wordpress post attachment
	$thumbnail = get_the_post_thumbnail($product->ID, array(150,150));


?>

<div class="wrap">
	<?php

		if( isset($_SESSION['status']) ){
			$status = $_SESSION;
			session_destroy();
		}
	?>
	<?php if( isset($status) && !is_null($status) && !empty($status) ) if( $status['status'] == 'success' ):?>
		<div class="notice notice-success is-dismissible">
	    <p><?php echo $status['message']; ?></p>
		</div>
	<?php else  : ?>
		<div class="notice notice-error is-dismissible">
	    <p><?php echo $status['message']; ?><br />
	    	<?php if(isset($errors) && !empty($errors)) foreach($errors as $err): ?>
	    	<?php echo $err . '<br />'?>
	    	<?php endforeach; ?>
	    </p>
		</div>
	<?php endif; ?>

	<h2><?php esc_html_e( 'Manage Product' ); ?></h2>
	<hr>

	<form 
		method="POST" 
		action="<?php echo qrroute('product/update'); ?>" 
		enctype="multipart/form-data">
			<?php 
				$row = $wpdb->get_row("SELECT * FROM wp_codeqr_product_features WHERE product_id = {$product_id}");
				if( !is_null($row) ){

					echo '<input type="hidden" name="row_id" value="'.$row->id.'">';
				}
				echo '<input type="hidden" name="product_id" value="'.$product_id.'">';
		?>
		<div class="card" style="max-width: 800px">
			<table class="form-table">
				<tbody>

					<tr>
						<th colspan="2">
							<h3>Product: <?php echo $product->post_title; ?></h3>
						</th>
					</tr>

					<tr>
						<th>
							Model #
						</th>
						<td><input type="text" name="model_number" value="<?php echo isset($product->model_number) ? $product->model_number : ''; ?>"></td>
					</tr>

					<tr>
						<th>
							Product Thumbnail
						</th>
						<td><?php echo $thumbnail ? $thumbnail : ''; ?><br />
							<input type="file" name="file"></td>
					</tr>

					<tr>
						<th>
							PDF Cover Sheet
						</th>
						<td>
							<?php if( isset($product->pdf_nicename) && strlen($product->pdf_nicename) ) : ?>
							<div class="client-file">
								<div class="filename" style="width: 100%;">
									<div class="flex-row" style="flex-direction: row; min-width: 100%; position: relative;">
										<?php echo '<input type="hidden" name="pdf" value="'.$product->pdf_cover.'"><img style="width: 30px; height: 30px; margin-right: 8px;" src="'.plugin_dir_url( __DIR__ ).'/assets/img/pdf.png" alt="">
										<span class="name">' .$product->pdf_nicename.'</span>
										<span class="delete"><span class="dashicons dashicons-no"></span></span>'?>										
									</div>
								</div>
							</div>
							<br />
							<?php endif; ?>
							<input type="file" name="pdf_cover"></td>
					</tr>

					<tr>
						<th>
							Make Public?
						</th>
						<td><input type="checkbox" name="visible" <?php echo isset($product->visible) ? 'checked="checked"' : ''; ?>> </td>
					</tr>

					<tr>
						<th>
						</th>
						<th colspan="2">
							<label for="filename" style="width: 100%; text-align: left; display: block;">Search Available QR Features</label>
							<input id="documents" placeholder="Search QR Features">
						</th>
					</tr>

					<tr>

						<th> &nbsp; </th>

						<td valign="top" style="width: 50%; background-color: #f9f9f9; box-shadow: 0px 0px 3px 0px inset #ddd;vertical-align: top">

							<h5>Available Features</h5>
							
							<ul id="product-list" style=" min-height: 100%; overflow-y: scroll; vertical-align:top; padding-right: 10px;">
								
							  <li <?php echo !is_null($rtc_index) ? 'style="display: none;"' : '';?> class="available" data-feature_id="rtc">
						    	<div class="right btn btn-select"><i class="fa fa-plus"></i></div>
						    	<span class="none">Set Real Time Clock (RTC)</span> 
						    </li>

								<?php foreach($features_list as $doc): ?>

						    <li <?php echo in_array($doc->id, $assoc_features_arr) ? 'style="display: none;"' : '';?> class="available" data-feature_id="<?php echo $doc->id; ?>">
						    	<div class="right btn btn-select"><i class="fa fa-plus"></i></div>
						    	<span class="none"><?php echo stripslashes( $doc->feature_name); ?></span> 
						    </li>

							  <?php endforeach; ?>

							</ul>
						</td>

						<td style="width: 50%; background-color: #f9f9f9; box-shadow: 0px 0px 3px 0px inset #ddd;vertical-align:top">
							<h5>Associated Features</h5>

							<ul id="selected-products" class="simple_with_drop">

								<?php if( $features_list && is_array($assoc_features_arr) ){ 
									if( !is_null($rtc_index) ) : ?>
										<li>
											<div class="left" style="padding-right: 5px;">
												<span><i class="fa fa-bars"></i></span>
											</div>
											<div class="none">
												<span class="btn btn-unselect"><i class="fa fa-minus"></i></span>
												Set Real Time Clock (RTC)
												<input type="hidden" name="features[]" value="rtc">
											</div>
										</li>
								<?php endif; // end if rtc ?>

								<?php if(!empty($assoc_features_arr)) foreach( $assoc_features as $feature ): ?>
									<li data-feature_id="<?php echo $feature->id; ?>">
										<div class="left" style="padding-right: 5px;"><span><i class="fa fa-bars"></i></span></div>
										<div class="none">
											<div class="right btn btn-unselect"><i class="fa fa-minus"></i></div>
											<span class="none"><?php echo stripslashes($feature->feature_name); ?></span>
											<input type="hidden" name="features[]" value="<?php echo $feature->id; ?>" />
										</div>
									</li>
								<?php endforeach; } ?>

							</ul>
						</td>
					</tr>

					<tr>
						<th>
						</th>
						<td style="text-align: right;">
							<input type="submit" name="submit" value="Save">
						</td>
					</tr>

				</tbody>
			</table>
		</div>

	</form>

</div><!-- .wrap -->


<script>
	jQuery(document).ready(function($){

		$(".simple_with_drop").sortable({
  		handle: 'i.fa-bars'
  	});
		var $products = $('#product-list li');

		$('#documents').keyup(function() {
		  var re = new RegExp($(this).val(), "i"); // "i" means it's case-insensitive
		  $products.show().filter(function() {
		      return !re.test($(this).text());
		  }).hide();
		});

		$('.client-file .delete').click(function(){
			$(this).closest('.client-file').remove();
		})

		function send_update_feature(feature_id, action){

			$.ajax({
				url : '<?php echo qrroute('product/feature')?>',
				type: 'POST',
				dataType: 'json',
				data : {
					feature_id : feature_id,
					action : action,
					product_id : <?php echo $_GET['product_id']; ?>,

				},
				beforeSend: function(){

				},
				success: function(d){

					if( d.status == 'success' ){
						$('#association_message').text('Saved succssfully!');
					} else {
						$('#association_message').text('Unable to update.');
					}


					$('#association_message').fadeIn('fast');
					setTimeout(function(){
						$('#association_message').fadeOut('fast');
					}, 2000)
				},
				complete: function(){

				}
			})

		}


		// TODO :YOU NEED TO EDIT ALL BELOW

		$('.btn.btn-select').on('click', function(){
			var label = $(this).closest('li').find('span').text();
			var feature_id = $(this).closest('li').data('feature_id');
			$('#selected-products').append('<li>\
				<div class="left" style="padding-right: 5px;">\
					<span><i class="fa fa-bars"></i></span>\
				</div>\
				<div class="none">\
					<span class="btn btn-unselect"><i class="fa fa-minus"></i></span>\
					'+label+'<input type="hidden" name="features[]" value="'+feature_id+'" />\
				</div>\
			</li>');

			$(this).closest('li').hide();


			send_update_feature(feature_id, 'add');

		});

		$('#selected-products').on('click', '.btn-unselect', function(){
			var feature_id = $(this).closest('div.none').find('input').val();

			$('.available[data-feature_id='+feature_id+']').show();

			$(this).closest('li').remove();

			send_update_feature(feature_id, 'delete');
		});

	});
</script>
