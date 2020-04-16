<?php 



function codeqr_menu_pages(){

	$admin_page_name = 'Code QR';
	add_menu_page( $admin_page_name, $admin_page_name, 'custom_menu_access', 'codeqr', 'codeqr_admin', plugin_dir_url( __DIR__ ).'/framework/public/assets/images/qr-code.png' );
	add_submenu_page( 'codeqr', 'Add QR Feature', 'Add Feature Category', 'custom_menu_access', 'codeqr-feat_cats', 'wp_codeqr_feat_cats_func' );
	add_submenu_page( 'codeqr', 'Add QR Feature', 'Add QR Feature', 'custom_menu_access', 'codeqr-add_feat', 'codeqr_add_feat_func' );
	add_submenu_page( 'codeqr', 'QR Features', 'QR Features', 'custom_menu_access', 'codeqr-list_feats', 'codeqr_list_feat_func' );
	add_submenu_page( 'codeqr', 'Products', 'Products', 'custom_menu_access', 'codeqr-products', 'wp_codeqr_products_func');
    remove_submenu_page('codeqr','codeqr'); // pay a attention

}



add_action('admin_menu', 'codeqr_menu_pages'); 



// function codeqr_scripts(){
// 	wp_enqueue_script( 'dropzone', plugin_dir_url( __DIR__ ). 'assets/js/dropzone.js', array('jquery'), false, false );
// 	wp_enqueue_style( 'admin-style', plugin_dir_url( __DIR__ ). 'assets/css/admin-style.css', array(), false, 'all' );
// 	wp_enqueue_script( 'sortable', plugin_dir_url(__DIR__) . 'assets/js/sortable.js', array('jquery'), false, true );
// }



function codeqr_admin(){}



function codeqr_frontend_scripts(){

	wp_enqueue_style( 'codeqr-front-end-css', plugin_dir_url(__DIR__).'assets/css/front-end.css', array(), false, 'all' );
	wp_enqueue_style( 'fancybox-css', plugin_dir_url(__DIR__).'assets/js/fb/source/jquery.fancybox.css', array(), false, 'all' );
	wp_enqueue_style( 'fancybox-buts-css', plugin_dir_url(__DIR__).'assets/js/fb/source/helpers/jquery.fancybox-buttons.css', array(), false, 'all' );
	wp_enqueue_script( 'fancybox-pack', plugin_dir_url(__DIR__) . 'assets/js/fb/source/jquery.fancybox.pack.js', array('jquery'), false, true );
	wp_enqueue_script( 'fancybox-buts', plugin_dir_url(__DIR__) . 'assets/js/fb/source/helpers/jquery.fancybox-buttons.js', array('jquery'), false, true );

}



// add_action( 'admin_enqueue_scripts', 'codeqr_scripts');

add_action( 'wp_enqueue_scripts', 'codeqr_frontend_scripts' );



function wp_codeqr_feat_cats_func(){



	if ( !current_user_can( 'custom_menu_access' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );	
	}



	$message = NULL;



	$options = get_option('codedocs');

	

	include dirname(__DIR__) .'/framework/source/v1/models/FeatcatsModel.php';



	if( isset($_POST) && isset($_POST['submit']) ){

		$Featcats = new \CODEQR\FeatcatsModel;
		$Featcats->updateFeatCats($_POST);

	}



	$Featcats = new \CODEQR\FeatcatsModel;

	$data['feature_categories'] = $Featcats->listFeatCats();



	ob_start(); include dirname(__DIR__) . '/partial/feat_cats.php'; $template = ob_get_clean();



	echo $template;

}



function validate_feature_post(){



		$error = array();



		if( isset($_POST['feature_name']) && strlen($_POST['feature_name']) < 1 ){

			$error['feature_name'] = 'Provide a Feature Name.';

		}

		

		if( isset($_POST['feature_code']) && strlen($_POST['feature_code']) < 1 ){

			$error['feature_code'] = 'Please Provide a Feature Code.';

		}



		if( isset($_POST['description']) && strlen($_POST['description']) < 1 ){

			$error['description'] = 'Provide a description.';

		}



		return $error;		

}



function codeqr_add_feat_func(){





	if ( !current_user_can( 'custom_menu_access' ) )  {

	

		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );	

	}



	$status = NULL;	

	

	$data = array();

	

	$options = get_option('codedocs');



	include dirname(__DIR__) .'/framework/source/v1/models/FeatcatsModel.php';

	include dirname(__DIR__) .'/framework/source/v1/models/FeatureModel.php';

	include dirname(__DIR__) .'/framework/source/v1/models/ProdFeatsModel.php';





	$Featcats = new \CODEQR\FeatcatsModel;

	$Features = new \CODEQR\FeatureModel;

	$ProdFeats = new \CODEQR\ProdFeatsModel; 



	if( isset($_POST) && !empty($_POST['action']) && $_POST['action'] == 'add-feature' ){



	//echo '<pre>'.print_r($_POST, 1).'</pre>';



		// Check for any validation issues.

		// -----------------------------------------------------------

		$errors = validate_feature_post($_POST);



		// If Errors than show fail message over the edit screen

		// -----------------------------------------------------------

		if( !empty($errors) ){



			$status = array('status'=>'failed', 'message'=>'Feature not saved successfully.', 'errors' => $errors);

			// ob_start(); include dirname(__DIR__) . '/partial/feature-edit.php'; $template = ob_get_clean();

			// echo $template;

			// Show the template with our compiled data.

			// -----------------------------------------------------------

			ob_start(); include dirname(__DIR__) . '/partial/feature-edit.php'; $template = ob_get_clean();

			echo $template;

			return;

		}



		// Save the Features & return the feature ID or error message

		// -----------------------------------------------------------

		$new_feature_id = $Features->save($_POST);



		if( isset( $_FILES['feature_qr_upload'] ) && strlen($_FILES['feature_qr_upload']['name']) ){



			$file = $_FILES['feature_qr_upload']['name'];



			$path_parts = pathinfo($file);



			$fext = $path_parts['extension'];

			$fname = $path_parts['filename']; // since PHP 5.2.0



			$fname = $fname.rand(1,9999).time().'.'.$fext;



			if( $_FILES['feature_qr_upload']['size'] > 5000000 ){

				$this->response->json(array('status'=>'failed', 'message'=>'Upload an image that is less than 5MB'));

			}



			$destination = rtrim(dirname(QRROOT_PATH), "/") . '/' . 'qr_codes';



			$moved = move_uploaded_file($_FILES['feature_qr_upload']['tmp_name'], rtrim($destination,"/") . '/'.  $fname);



			global $wpdb;



			$wpdb->update('wp_codeqr_features', array('feature_image'=>$fname), array('id'=>$new_feature_id));



		}



		// This is the New Feature ID

		// -----------------------------------------------------------

		if( is_numeric($new_feature_id) ){



			// Save the Categories that go along with the features.

			// -----------------------------------------------------------

			if( isset($_POST['feature_categories']) ){



				$Featcats->save($new_feature_id, $_POST['feature_categories']);

			}

			

			/*// Save the product association

			// -----------------------------------------------------------

			if( isset($_POST['products']) && !empty($_POST['products']) ){

				$ProdFeats->save($_POST['products'], $new_feature_id);

			}*/



			$status = array('status'=>'success', 'message'=>'Feature saved successfully.');



			ob_start(); include dirname(__DIR__) . '/partial/feature-edit.php'; $template = ob_get_clean();

			echo $template;

			return;



		// Error message

		// -----------------------------------------------------------

		} else {



			$status = array('status'=>'failed', 'message'=>'Unable to save feature');



			ob_start(); include dirname(__DIR__) . '/partial/feature-edit.php'; $template = ob_get_clean();

			echo $template;

			return;

			

		}

	}

	$Featcats = new \CODEQR\FeatcatsModel;

	$data['feature_categories'] = $Featcats->listFeatCats();



	// Show the template with our compiled data.

	// -----------------------------------------------------------

	ob_start(); include dirname(__DIR__) . '/partial/feature-edit.php'; $template = ob_get_clean();

	echo $template;

}



function codeqr_list_feat_func(){



	if ( !current_user_can( 'custom_menu_access' ) )  {

	

		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );	

	}



	global $wpdb;

	

	$message = NULL;



	$options = get_option('codedocs');



	if( isset($_GET['feature_id']) && strlen($_GET['feature_id']) ){



		$feature = $wpdb->get_row("SELECT id FROM wp_codeqr_features WHERE id = {$_GET['feature_id']}");



		if( !$feature ){

			

			$status = array('status'=>'failed', 'message'=>'The requested feature no longer exists or is an invalid feature index.');

			ob_start(); include dirname(__DIR__) . '/partial/feature-list.php'; $template = ob_get_clean();

			echo $template;

			return;

		}



		include dirname(__DIR__) .'/framework/source/v1/models/FeatcatsModel.php';

		include dirname(__DIR__) .'/framework/source/v1/models/FeatureModel.php';

		include dirname(__DIR__) .'/framework/source/v1/models/ProdFeatsModel.php';



		$Featcats = new \CODEQR\FeatcatsModel;

		$ProdFeats = new \CODEQR\ProdFeatsModel;

		$Features = new \CODEQR\FeatureModel;



		$errors  = getSessionErrors();

		$status  = getSessionStatus();



		if( isset($_POST) && !empty($_POST['action']) && $_POST['action'] == 'add-feature' ){



			if( isset( $_FILES['feature_qr_upload'] ) && strlen($_FILES['feature_qr_upload']['name']) ){



				$file = $_FILES['feature_qr_upload']['name'];



				$path_parts = pathinfo($file);



				$fext = $path_parts['extension'];

				$fname = $path_parts['filename']; // since PHP 5.2.0



				$fname = $fname.rand(1,9999).time().'.'.$fext;



				if( $_FILES['feature_qr_upload']['size'] > 5000000 ){

					$this->response->json(array('status'=>'failed', 'message'=>'Upload an image that is less than 5MB'));

				}



				$destination = ABSPATH . 'wp-content/plugins/codeqr/qr_codes/' . $fname;



				$moved = move_uploaded_file($_FILES['feature_qr_upload']['tmp_name'], $destination);



				global $wpdb;



				$wpdb->update('wp_codeqr_features', array('feature_image'=>$fname), array('id'=>$_GET['feature_id']));



			} else {



				if( !isset($_POST['image']) ){



					global $wpdb;



					$wpdb->update('wp_codeqr_features', array('feature_image'=>NULL), array('id'=>$_GET['feature_id']));

				}



			}



			$status = NULL;



			 

			// Save the Feature to the feature table

			$result = $Features->updateRow($_POST);



			// Save the relationship to the categories

			$Featcats->save($_GET['feature_id'], $_POST['feature_categories']);





			// Create a status

			if( is_numeric($result) ){



				$status = array('status'=>'success', 'message'=>'Feature saved successfully.');



			} else {



				$status = array('status'=>'failed', 'message'=>'Feature not saved successfully.');

			}



			// End if $_POST isset;

		}



		$data['feature_categories'] = $Featcats->listFeatCats();

		$data['selected_categories'] = explode(',', $Featcats->getCatsByFeature($_GET['feature_id']));



		// Rerun the query to the newly updated object.



		$results = $Features->get($_GET['feature_id']);



		ob_start(); include dirname(__DIR__) . '/partial/feature-edit.php'; $template = ob_get_clean();



		echo $template;



	} else {



		ob_start(); include dirname(__DIR__) . '/partial/feature-list.php'; $template = ob_get_clean();



		echo $template;

	}

}



function wp_codeqr_products_func(){





	if ( !current_user_can( 'custom_menu_access' ) )  {

	

		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );	

	}



	global $wpdb;



	$message = NULL;



	$options = array();



	$status = NULL;



	$errors = array();



	if( isset($_POST['submit']) ){



		$visible = isset($_POST['visible']) ? 1 : 0;



		global $wpdb;



		if( isset($_POST['features']) ){

			$features = implode(',', $_POST['features']);

		} else {

			$features = '';

		}

		$row_id = $_POST['row_id'];



		$wpdb->query("

			UPDATE wp_codeqr_products 

			SET model_number = '".$_POST['model_number']."', 

			visible = {$visible}, 

			features = '{$features}' 

			WHERE product_id={$_POST['product_id']}");



		if( isset( $_FILES['pdf_cover'] ) && strlen($_FILES['pdf_cover']['name']) ){



			$file = $_FILES['pdf_cover']['name'];



			$path_parts = pathinfo($file);



			$fext = $path_parts['extension'];



			$fname = $path_parts['filename']; // since PHP 5.2.0



			$fname = $fname.rand(1,9999).time().'.'.$fext;



			$destination = ABSPATH . 'wp-content/plugins/codeqr/pdfs/uploaded/' . $fname;



			$moved = move_uploaded_file($_FILES['pdf_cover']['tmp_name'], $destination);





			$pdf = new \FPDI();



			try {



			 	$pageCount = $pdf->setSourceFile($destination);

				$pageId = $pdf->importPage($pageCount);

				$pdf->addPage();

				$pdf->useTemplate($pageId);

					

			} catch (Exception $e) {		

				

				$status = array('status'=>'failed', 'message' => 'The PDF that you uploaded is a version greater than PDF 1.4. The Free FPDI only supports PDF v1.4 and below.', 'errors'=>$errors);

				ob_start(); include dirname(__DIR__) . '/partial/product.php'; $template = ob_get_clean();

				echo $template; return;

			}





			$pageCount = $pdf->setSourceFile( ABSPATH . 'wp-content/plugins/codeqr/pdfs/templates/toc.pdf');

			$pageId = $pdf->importPage($pageCount);

			$pdf->addPage();

			$pdf->useTemplate($pageId);



			$pageCount = $pdf->setSourceFile( ABSPATH . 'wp-content/plugins/codeqr/pdfs/templates/grid.pdf');

			$pageId = $pdf->importPage($pageCount);

			$pdf->addPage();

			$pdf->useTemplate($pageId);



			@$pdf->output($destination, 'F');



			global $wpdb;



			$wpdb->update('wp_codeqr_products', array('pdf_cover'=>$fname, 'pdf_nicename'=>$file), array('product_id'=>$_GET['product_id']));



		} else {



			if( isset($_POST['pdf']) && !strlen($_POST['pdf']) ){



				global $wpdb;



				$wpdb->update('wp_codeqr_products', array('pdf_cover'=>NULL, 'pdf_nicename'=>NULL), array('product_id'=>$_GET['product_id']));

			}



		}





		if( isset($_FILES) && !empty($_FILES['file']['name']) ){



			include dirname(__DIR__) . '/framework/source/v1/controllers/ProductController.php';

			$Products = new \CODEQR\ProductController;

			$errors = $Products->setVideoThumbnailAsFeaturedImage($_GET['product_id']);

		}



		if( $errors ){



			$status = array('status'=>'failed', 'message' => 'Unable to update product', 'errors'=>$errors);

			ob_start(); include dirname(__DIR__) . '/partial/product.php'; $template = ob_get_clean();

			echo $template; return;

		}



		if( !empty($wpdb->last_error) ) {



			$status = array('status'=>'failed', 'message' => $wpdb->last_error);

		} else {

			$status = array('status'=>'success', 'message' => 'Product successfully saved.');

		}

	}



	if( isset($_GET['product_id']) && strlen($_GET['product_id']) ){



		ob_start(); include dirname(__DIR__) . '/partial/product.php'; $template = ob_get_clean();



	} else {



		ob_start(); include dirname(__DIR__) . '/partial/products.php'; $template = ob_get_clean();

	}



	echo $template;

}



function codeqr_shortcode_func( $atts = array(), $content = '' ) {



	$atts = shortcode_atts( array(



		'product_id' => ''



	), $atts, 'shortcode-id' );



	global $wpdb;



	$string = '';



	$post_name = $wpdb->get_var("SELECT post_name FROM {$wpdb->prefix}posts WHERE ID = {$atts['product_id']}");



	$row = $wpdb->get_results("SELECT * FROM wp_codeqr_product_features WHERE product_id = {$atts['product_id']}");



	if( empty($row) ){



		echo 'There are no QR codes to display for this product.';



	} else {



		$features_array = wp_list_pluck( $row, 'features' );





		$rtc_included = array_search('rtc', $features_array);



		if( $rtc_included !== FALSE ){

			// exclude rtc from feature list

			unset($features_array[$rtc_included]);			

		}



		$features_array = array_filter($features_array);

		

		global $post;

		

		ob_start();

		include dirname(__DIR__).'/partial/sc-header.php';

		$string .= ob_get_clean();

		

		if( !empty($features_array) ) {



			$features_list = implode(',', $features_array);



			$features = $wpdb->get_results("SELECT * FROM wp_codeqr_features WHERE id IN ({$features_list})");

		}



		// show categories of features for this product

		if( !isset($_GET['feature_category']) ){





			$all_the_categories = array();



			if(!isset($_GET['list']) || (isset($_GET['list']) && $_GET['list'] == 'categories') ){



				foreach( $features as $feature ){



					// get the categories included with each feature

					$categories = $wpdb->get_var("SELECT categories FROM wp_codeqr_feat_cats WHERE feature_id = {$feature->id}");



					if( !is_null($categories) ){



						// explode the categories and include them 

						$categories = explode(',',$categories);



						// into an array to be flattened later

						$all_the_categories = array_merge($all_the_categories, $categories);

					}

				

				}



				$all_the_categories = array_unique($all_the_categories);



				$all_the_categories2 = array();



				foreach($all_the_categories as $k => $atc){

				

					if( !in_array($atc, $all_the_categories2) ){

				

						$all_the_categories2[] = $atc;

					}

				}



				$all_the_categories2 = $wpdb->get_results("SELECT * FROM wp_codeqr_categories WHERE slug IN ('".implode("','", $all_the_categories2)."')");



				ob_start();

			

				include dirname(__DIR__).'/partial/sc-categories.php';

			

				$string .= ob_get_clean();



			} elseif( isset($_GET['list']) && $_GET['list'] == 'features' ) {



				

				$features_array = array_filter($features_array);

				

				$features = implode(',', $features_array);



				$all_the_features = $wpdb->get_results("SELECT * FROM wp_codeqr_features WHERE id IN (".$features.")");



				$features = $all_the_features;



				ob_start();

			

				include dirname(__DIR__).'/partial/sc-features.php';

			

				$string .= ob_get_clean();



			}



		} else {



			if( $_GET['feature_category'] == 'clock_configurator' ){



				$category_name = 'Clock Configurator';



			} elseif($_GET['feature_category'] == 'configurator') {

				

				$category_name = 'CR5000AV Configurator';



			} else {



				$category_name = $wpdb->get_var("SELECT name FROM wp_codeqr_categories WHERE slug = '".$_GET['feature_category']."'");



			}



			ob_start();

			

			include dirname(__DIR__).'/partial/sc-features.php';

			

			$string .= ob_get_clean();



		}			



	}



	return $string;

}



add_shortcode( 'codeqr', 'codeqr_shortcode_func' );



add_action ( 'wp_head', 'codeqr_js_variables' );



function codeqr_js_variables(){ 

?>

  <script type="text/javascript">

    var wphome_url = <?php echo json_encode( get_bloginfo( 'url' ) ); ?>; 

    var qrroute =  <?php echo json_encode( get_bloginfo( 'url' ) .'/'. QRPATHNAME .'/' ); ?>;

    var qrpath = <?php echo json_encode( plugin_dir_url( __DIR__ ) )?>

  </script><?php

}





function productHasRTC($product_id){



	global $wpdb;

	

	// To reduce confusion, query the database again for the RTC feature

	$_features = $wpdb->get_var("SELECT features FROM wp_codeqr_product_features WHERE product_id = {$product_id}");



	// Make sure we are always using an array

	if( is_null($_features) ) $_features = array();



	// Sometimes there's only one feature

	$_features = explode(',', $_features);



	// Make sure there's associated features and get rid of rtc

	if(empty($_features)) return false;



	// RTC is not a feature of this product, then show it

	if( in_array('rtc', $_features) ) return true;

}