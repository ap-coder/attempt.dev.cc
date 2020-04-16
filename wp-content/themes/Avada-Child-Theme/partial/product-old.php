<?php
	global $wpdb;

	$model_number = $wpdb->get_row("
	SELECT model_number, product_id FROM wp_codeqr_products 
	WHERE product_id = {$_GET['product']} AND model_number IS NOT NULL"); 
	
	if( ! $model_number ){

		$model_number = $wpdb->get_row("
		SELECT model_number, product_id FROM wp_codeqr_products
		WHERE product_id = {$_GET['product']} AND model_number IS NOT NULL"); 
	}

		echo '<a href="'.get_permalink( $post->ID ).'">← Return to Configuration Guide Generator</a>'; 

?>
<h1><?php echo $pst->post_title; ?></h1>
<p>Below are a list of configuration options available for your product organized by category. 
Each configuration option will generate a QR code that your scanner can read that will allow you 
configure and update settings in your product. You can view the QR code individually by click on it’s 
name and a pop-up will appear, or you can click on the checkbox next to each option and click on 
“Generate PDF” button to create a PDF that will contain a collection of QR codes that can be saved, 
emailed, or printed.</p><p>&nbsp;</p>
<div class="thumbnail"><?php 

echo get_the_post_thumbnail( $pst->ID, array(100,100)); 

?></div>
<div class="title"><p><strong><?php echo $model_number->model_number; ?></strong></p></div>

<form action="<?php echo bloginfo( 'url' ); ?>/codeqr/print/printpdf" method="POST">
	<input type="hidden" name="product" value="<?php echo $model_number->product_id; ?>">
<?php

	// Have a button above "Generate PDF"

	// List of categories
	
	global $wpdb;



	$features_list = $wpdb->get_results("SELECT features FROM wp_codeqr_product_features WHERE product_id = {$_GET['product']}");

	$features_list = wp_list_pluck( $features_list, 'features' );

	$fla_imploded = implode("','",$features_list);
	$results = $wpdb->get_results("SELECT * FROM wp_codeqr_feat_cats WHERE feature_id IN ('".$fla_imploded."')");
	$results = wp_list_pluck( $results, 'categories' );

	$array = array();
	foreach($results as $re){
		$array[] = trim($re,',');
	}
	$array = implode(',',$array);
	$array = explode(',', $array);
	$array = array_unique($array);

	foreach($array as $k=> $ar){
		$array[$k] = sanitize_title( $ar);
	}

	$categories = $wpdb->get_results("SELECT * FROM wp_codeqr_categories WHERE slug IN ('".implode("','",$array)."')");
	

	echo '<ul id="QRCategories">';

	foreach($categories as $k => $cat): ?>

		<li style=" cursor: pointer;" data-slug="<?php echo $cat->slug; ?>">

			<i class="fa fa-chevron-right"></i><span class="trigger"><?php echo $cat->name; ?></span>

			<div class="list" style="display: none;">

				<?php

					$features = $wpdb->get_results("
						SELECT 
							FIND_IN_SET('{$cat->slug}',categories) as matched,
							cfc.*, 
							cf.description as feature_description, 
							cf.feature_code as feature_code, 
							cf.feature_name as feature_name,
							cf.template as template,
							cf.feature_image
						FROM wp_codeqr_feat_cats as cfc 
							JOIN wp_codeqr_features as cf ON cf.id = cfc.feature_id
						WHERE feature_id IN ('{$fla_imploded}')"); 


				?>


					<div class="list" style="display: none;">

						<div class="feature">
							<div class="left"><input type="checkbox" class="check-all"></div>
							<div class="none"><strong>Select All</strong></div> 
						</div>

						<hr>

						<?php foreach($features as $k => $feature): 
							
							if( ! $feature->matched ) continue;

							//unset($features_list_array[$feature->feature_id]);

							$template = unserialize($feature->template); ?>
					
							<div class="feature">
								
								<div class="left"><input type="checkbox" name="cat[<?php echo $cat->slug; ?>][]" value="<?php echo $feature->feature_id; ?>"></div>
								
								<div class="none">
									
									<p><strong><a href="#" class="open-modal" data-image="<?php echo $feature->feature_image; ?>"><span class="qr-name"><?php echo stripcslashes($feature->feature_name); ?></span></a></strong><br>
									
									<span class="qr-description"><?php echo stripcslashes($feature->feature_description); ?></span></p>

									<div>
										
											<?php
												if( !empty($template) && count($template)){

													foreach( $template as $t ){

														if( !empty($t) ){

															if ( ( isset($t->option_name) && !empty($t->option_name) )
																&& ( isset($t->option_value) && !empty($t->option_value) )
																&& ( isset($t->option_behavior) && !empty($t->option_behavior) )
																&& ( isset($t->option_type) && !empty($t->option_type) )
															){

																switch ($t->option_type) {

																	case 'text':
																		echo '<p><strong>'.$t->option_behavior.'</strong> <br />
																		<input type="text" name="feature['.$feature->feature_id.']['.$t->option_name.']" value="'.$t->option_value.'"></p>';
																		break;
																	
																	case 'number':
																		echo '<p><strong>'.$t->option_behavior.'</strong> <br />
																		<input type="number" name="feature['.$feature->feature_id.']['.$t->option_name.']" value="'.$t->option_value.'"></p>';
																		break;
																	
																	case 'email':
																		echo '<p><strong>'.$t->option_behavior.'</strong> <br />
																		<input type="email" name="feature['.$feature->feature_id.']['.$t->option_name.']" value="'.$t->option_value.'"></p>';
																		break;
																}
															}
															
														}
													}
												}
											?>

									</div>

								</div>

							</div>

						<?php endforeach; ?>

						<hr>

						<div class="feature">
							<div class="left"><input type="checkbox" class="check-all"></div>
							<div class="none"><strong>Select All</strong></div> 
						</div>

					</div>

		</li>

	<?php endforeach;
	echo '</ul>';

	// Have a button above "Generate PDF"

	echo '<button>Generate PDF</button>';


	//echo '<pre>'.print_r($features_list_array, 1).'</pre>';

?>
</form>

<div class="modal" id="my-modal">
	<div class="modal-close"><i class="fa fa-remove"></i></div>
	<div class="modal-qr-name"></div>
	<div class="modal-qr-container">
		<img src="" alt="">
	</div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />
<script>
	
	jQuery(document).ready(function($){

		$('form').on('submit', function(e){
			e.preventDefault();
		 	if( $(this).find('input:checked').length < 1 ){
		 		alert('Please check at least one feature.');
		 	} else {
		 		$(this)[0].submit();
		 	}
		})

		$('a.open-modal').on('click', function(e){
			e.preventDefault();
			var image = $(this).data('image');
			var name = $(this).closest('div.none').find('.qr-name').text();
			var desc = $(this).closest('div.none').find('.qr-description').text();
			$('#my-modal').find('.modal-qr-name').html(name);
			$('#my-modal').find('img').attr('src', '<?php echo get_bloginfo('url'). '/wp-content/plugins/codeqr/qr_codes/'?>'+image);
			$('#my-modal').modal();
			return false;
		});

		$('.modal-close').on('click', function(){

			$('#my-modal').trigger('click');
		})

		$('#QRCategories li, #QRCategories li').on('click', '.trigger,  .fa-chevron-right', function(e){
			if($(this).closest('li').hasClass('active')){
				$(this).closest('li').removeClass('active');
				$(this).closest('li').find('.list').slideUp(750);
			} else {
				$(this).closest('li').addClass('active');
				$(this).closest('li').find('.list').slideDown(750);
			}
		});

		$('#QRCategories li .list').on('click', '.check-all', function(){
			if( $(this).prop('checked') ){
				$(this).closest('.list').find('input').each(function(i, t){
					$(t).prop('checked', 'checked');
				});
			} else {
				$(this).closest('.list').find('input').each(function(i, t){
					$(t).prop('checked', false);
				});
			}
		});

	});
</script>