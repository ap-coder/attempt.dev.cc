<?php
	global $wpdb;
	$model_number = $wpdb->get_var("
	SELECT model_number FROM wp_codeqr_product_features 
	WHERE product_id = {$post->ID} AND model_number IS NOT NULL
	"); ?>

<h1><?php echo $post->post_title; ?></h1>
<p>Below are a list of configuration options available for your product organized by category. 
Each configuration option will generate a QR code that your scanner can read that will allow you 
configure and update settings in your product. You can view the QR code individually by click on it’s 
name and a pop-up will appear, or you can click on the checkbox next to each option and click on 
“Generate PDF” button to create a PDF that will contain a collection of QR codes that can be saved, 
emailed, or printed.</p><p>&nbsp;</p>
<div class="thumbnail"><?php the_post_thumbnail(array(100,100)); ?></div>
<div class="title"><p><strong><?php echo $model_number; ?></strong></p></div>

<form action="<?php echo bloginfo( 'url' ); ?>/codeqr/print/printpdf" method="POST">
	<input type="hidden" name="product" value="<?php echo $model_number; ?>">
<?php

	// Have a button above "Generate PDF"

	// List of categories
	
	global $wpdb;
	$categories = $wpdb->get_results("SELECT * FROM wp_codeqr_categories");
	echo '<ul id="QRCategories">';
	foreach($categories as $k => $cat):


					global $wpdb;
					$result = $wpdb->get_results("SELECT * FROM wp_codeqr_product_features WHERE product_id = {$post->ID}");


					$features = array();
					foreach($result as $res){
						$feats = explode(',',$res->features);
						foreach($feats as $f){
							if( strlen($f) > 0 )
							$features[] = $f;
						}
					}

					$features = array_unique($features);

					$features_csv = implode(',',$features);

					$features_csv = trim($features_csv, ',');

					$features = $wpdb->get_results("
						SELECT 
							FIND_IN_SET('{$cat->slug}',categories) as matched,
							cfc.*, 
							cf.description as feature_description, 
							cf.feature_code as feature_code, 
							cf.feature_name as feature_name,
							cf.template as template
						FROM wp_codeqr_feat_cats as cfc 
							JOIN wp_codeqr_features as cf ON cf.id = cfc.feature_id
						WHERE feature_id IN ({$features_csv })");

					foreach($features as $k => $feature): 
						
						if( ! $feature->matched ) continue;

						$template = unserialize($feature->template);

						if( $k == 0 ){

						echo '<li style=" cursor: pointer;" data-slug="'.$cat->slug.'">

										<i class="fa fa-chevron-right"></i><span class="trigger">'.$cat->name.'</span>

										<div class="list" style="display: none;">';
						}
					?>
											<div class="feature">
												<div class="left"><input type="checkbox" class="check-all"></div>
												<div class="none"><strong>Select All</strong></div> 
											</div>

											<hr>

											<div class="feature">
												<div class="left"><input type="checkbox" name="feature[<?php echo $feature->feature_id; ?>]" value=""></div>
												<div class="none">
													<p><strong><a href="#" class="open-modal"><span class="qr-name"><?php echo stripcslashes($feature->feature_name); ?></span></a></strong><br>
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

											<hr>

											<div class="feature">
												<div class="left"><input type="checkbox" class="check-all"></div>
												<div class="none"><strong>Select All</strong></div> 
											</div>

					<?php endforeach;
					
						echo '</div></li>'; 

	endforeach;
	echo '</ul>';

	// Have a button above "Generate PDF"

	echo '<button>Generate PDF</button>';
?>
</form>

<div class="modal" id="my-modal">
	<div class="modal-close"><i class="fa fa-remove"></i></div>
	<div class="modal-qr-name"></div>
	<div class="modal-qr-container">
		<img src="<?php echo get_stylesheet_directory_uri(). '/image/qrcode.png'?>" alt="">
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
			var name = $(this).closest('div.none').find('.qr-name').text();
			var desc = $(this).closest('div.none').find('.qr-description').text();
			$('#my-modal').find('.modal-qr-name').html(name);
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