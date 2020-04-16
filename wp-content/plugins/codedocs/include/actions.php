<?php 

function codedocs_menu_pages(){
	$admin_page_name = 'Code Docs';
	add_menu_page( $admin_page_name, $admin_page_name, 'manage_options', 'codedocs', 'codedocs_admin_func', 'dashicons-media-code' );
	add_submenu_page( 'codedocs', 'Languages', 'Languages', 'manage_options', 'codedocs-settings', 'codedocs_admin_func' );
	add_submenu_page( 'codedocs', 'Add Document', 'Add Document', 'manage_options', 'codedocs-doc-add', 'codedocs_add_doc_func' );
	add_submenu_page( 'codedocs', 'Documents', 'Documents', 'manage_options', 'codedocs-docs', 'codedocs_docsets_func' );
	add_submenu_page( 'codedocs', 'Products', 'Products', 'manage_options', 'codedocs-products', 'codedocs_products_func');
	add_submenu_page( 'codedocs', 'Industries', 'Industries', 'manage_options', 'codedocs-industries', 'codedocs_industry_func');
  remove_submenu_page('codedocs','codedocs'); // pay a attention
}

add_action('admin_menu', 'codedocs_menu_pages'); 

function codedocs_scripts(){
	wp_enqueue_script( 'dropzone', plugin_dir_url( __DIR__ ). 'assets/js/dropzone.js', array('jquery'), false, false );
	wp_enqueue_style( 'admin-style', plugin_dir_url( __DIR__ ). 'assets/css/admin-style.css', array(), false, 'all' );
	wp_enqueue_script( 'sortable', plugin_dir_url(__DIR__) . 'assets/js/sortable.js', array('jquery'), false, true );
}

add_action( 'admin_enqueue_scripts', 'codedocs_scripts');

function codedocs_admin_func(){

	$message = NULL;

	$options = array();

	if ( !current_user_can( 'manage_options' ) )  {
	
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );	
	}

	if( isset( $_POST['publish'] ) ){

		update_option( 'codedocs', $_POST );
	}

	$options = get_option('codedocs');

	ob_start(); include dirname(__DIR__) . '/partial/admin.php'; $template = ob_get_clean();

	echo $template;
}

function codedocs_docsets_func(){

	if ( !current_user_can( 'manage_options' ) )  {
	
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );	
	}

	global $wpdb;
	
	$message = NULL;

	$options = get_option('codedocs');

	if(isset($_GET['doc_id']) && strlen($_GET['doc_id']) ){

		if( isset($_POST['submit']) ){

			include dirname(__DIR__) . '/framework/source/v1/models/FileModel.php';
			include dirname(__DIR__) . '/framework/source/v1/controllers/FileController.php';
			$FileController = new \WPMVC\FileController;
			$FileController->upload2();

			$updated = $wpdb->update("{$wpdb->prefix}files", array(
				'nicename' => $_POST['name'], 
				'industry' => implode(',', array_unique($_POST['industry'])), 
				'product' => implode(',', array_unique($_POST['products']))
			), array('id' => $_GET['doc_id']), array('%s', '%s', '%s'), array('%d'));

			if( empty($wpdb->last_error) ){
				$status = 'Updated Successfully';
			} else {
				$status = $wpdb->last_error;
			}
		}		

		ob_start(); include dirname(__DIR__) . '/partial/docset.php'; $template = ob_get_clean();

		echo $template;

	} else {

		ob_start(); include dirname(__DIR__) . '/partial/docsets.php'; $template = ob_get_clean();

		echo $template;
	}
}

function codedocs_add_doc_func(){

	if ( !current_user_can( 'manage_options' ) )  {
	
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );	
	}

	$message = NULL;
 
	$options = get_option('codedocs');

	ob_start(); include dirname(__DIR__) . '/partial/new-docset.php'; $template = ob_get_clean();

	echo $template;
}

function codedocs_products_func(){

	$message = NULL;

	$options = array();

	if ( !current_user_can( 'manage_options' ) )  {
	
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );	
	}

	if( isset($_POST['submit']) ){

		// If there is documents in the array
		if( isset($_POST['documents']) && !empty($_POST['documents']) ){

			$_POST['documents'] = array_unique($_POST['documents']);

			global $wpdb;

			$row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}sort WHERE `sort_type`='product' AND `type_id` = ".$_GET['product_id']);

			if( !empty($wpdb->last_result) ){

				$wpdb->update($wpdb->prefix.'sort', array('doc_array'=>implode(',',$_POST['documents'])), array('id'=>$row->id));

			} else {

				$wpdb->insert($wpdb->prefix.'sort', array('sort_type'=>'product', 'type_id'=>$_GET['product_id'], 'doc_array'=>implode(',',$_POST['documents'])));
			}

			$results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}files WHERE product LIKE '%".$_GET['product_id']."%' AND parent_id IS NULL");

			foreach($results as $res){

				if( !in_array($res->id, $_POST['documents'])){

					$missing = $_GET['product_id'];

					$products = explode(',', $res->product);
					
					if ( ($key = array_search($missing, $products)) !== false ) {

					  unset($products[$key]);
					}

					$products = array_values($products);

					$wpdb->update("{$wpdb->prefix}files", array('product'=>$products), array('id'=>$res->id));

				}

			}			

			$documents = implode(',', $_POST['documents']);

			$results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}files WHERE product NOT LIKE '%".$_GET['product_id']."%' AND parent_id IS NULL AND id IN ({$documents})");

			if( count($results) ){

				foreach( $results as $res ){

					$missing = $_GET['product_id'];
					
					$products = explode(',', $res->product);
										
					array_push($products, $_GET['product_id']);

					$products = array_filter($products);

					$products = array_values($products);

					$products = implode(',', $products);

					$wpdb->update("{$wpdb->prefix}files", array('product'=>$products), array('id'=>$res->id));

				}
			}

		} else {


			global $wpdb;

			// File all the documents that have the product_id in the set. Since $_POST['document'] is empty
			$results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}files WHERE FIND_IN_SET({$_GET['product_id']},product) > 0");

			
			foreach( $results as $result ){
				$products = explode(',',$result->product);
				if( ($key = array_search($_GET['product_id'], $products)) !== FALSE ){
					unset($products[$key]);
				}

				$products = implode(',',$products);
				$wpdb->query("UPDATE {$wpdb->prefix}files SET `product` = '{$products}' WHERE id = {$result->id}");
			}


			// remove the doc_array from the product row of the sort table
			$row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}sort WHERE `sort_type`='product' AND `type_id` = ".$_GET['product_id']);

			if( !empty($wpdb->last_result) ){

				$wpdb->update($wpdb->prefix.'sort', array('doc_array'=>''), array('id'=>$row->id));

			}
		}

	}

	// SHOW THE FRONT END
	// ----------------------------------------------------------------
	// ----------------------------------------------------------------
	if( isset($_GET['product_id']) && strlen($_GET['product_id']) ){

		ob_start(); include dirname(__DIR__) . '/partial/product.php'; $template = ob_get_clean();
	} else {

		ob_start(); include dirname(__DIR__) . '/partial/products.php'; $template = ob_get_clean();
	}


	echo $template;
}

function codedocs_industry_func(){

	$message = NULL;

	$options = array();

	if ( !current_user_can( 'manage_options' ) )  {
	
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );	
	}

	if( isset($_POST['submit']) ){


		if( isset($_POST['documents']) && !empty($_POST['documents']) ){

			$_POST['documents'] = array_unique($_POST['documents']);

			global $wpdb;

			$row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}sort WHERE `sort_type`='industry' AND `type_id` = ".$_GET['industry_id']);

			if( !empty($wpdb->last_result) ){

				$wpdb->update($wpdb->prefix.'sort', array('doc_array'=>implode(',',$_POST['documents'])), array('id'=>$row->id));

			} else {

				$wpdb->insert($wpdb->prefix.'sort', array('sort_type'=>'industry', 'type_id'=>$_GET['industry_id'], 'doc_array'=>implode(',',$_POST['documents'])));
			}

			$results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}files WHERE industry LIKE '%".$_GET['industry_id']."%' AND parent_id IS NULL");

			foreach($results as $res){

				if( !in_array($res->id, $_POST['documents'])){

					$missing = $_GET['industry_id'];

					$industries = explode(',', $res->industry);
					
					if (($key = array_search($missing, $industries)) !== false) {
					  unset($industries[$key]);
					}

					$industries = array_values($industries);

					$industries = implode(',', $industries);

					$wpdb->update("{$wpdb->prefix}files", array('industry'=>$industries), array('id'=>$res->id));

				}
			}

			$documents = implode(',', $_POST['documents']);

			$results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}files WHERE industry NOT LIKE '%".$_GET['industry_id']."%' AND parent_id IS NULL AND id IN ({$documents})");

			if( count($results) ){

				foreach( $results as $res ){

						$missing = $_GET['industry_id'];
						
						$industries = explode(',', $res->industry);
											
						array_push($industries, $_GET['industry_id']);

						$industries = array_filter($industries);

						$industries = array_values($industries);

						$industries = implode(',', $industries);

						$wpdb->update("{$wpdb->prefix}files", array('industry'=>$industries), array('id'=>$res->id));

				}
			}
		} else {

			global $wpdb;

			// File all the documents that have the product_id in the set. Since $_POST['document'] is empty
			$results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}files WHERE FIND_IN_SET({$_GET['industry_id']},industry) > 0");

			foreach( $results as $result ){

				$industries = explode(',',$result->industry);
				
				if( ($key = array_search($_GET['industry_id'], $industries)) !== FALSE ){
					unset($industries[$key]);
				}

				$industries = implode(',',$industries);
				$wpdb->query("UPDATE {$wpdb->prefix}files SET `industry` = '{$industries}' WHERE id = {$result->id}");
			}


			// remove the doc_array from the product row of the sort table
			$row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}sort WHERE `sort_type`='industry' AND `type_id` = ".$_GET['industry_id']);

			if( !empty($wpdb->last_result) ){

				$wpdb->update($wpdb->prefix.'sort', array('doc_array'=>''), array('id'=>$row->id));

			}

		}


	}

	if( isset($_GET['industry_id']) && strlen($_GET['industry_id']) ){

		ob_start(); include dirname(__DIR__) . '/partial/industry.php'; $template = ob_get_clean();

	} else {

		ob_start(); include dirname(__DIR__) . '/partial/industries.php'; $template = ob_get_clean();
	}


	echo $template;
}

function docset_shortcode_func( $atts = array(), $content = '' ) {

	$atts = shortcode_atts( array(
		'product_id' => '',
		'industry_id' => '',
		'doc_id' => '',
	), $atts, 'shortcode-id' );

	global $wpdb;


	if( isset($atts['product_id']) && strlen($atts['product_id']) > 0 ){

		$row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}sort WHERE `sort_type`='product' AND `type_id` = ".$atts['product_id']);
		if( $row ){ 

			$results = $wpdb->get_results("SELECT id FROM {$wpdb->prefix}files WHERE  FIND_IN_SET({$atts['product_id']},product) > 0 "); 
			$results = wp_list_pluck( $results, 'id' );
			$doc_array = explode(',', $row->doc_array);
			$results = array_merge($doc_array,$results );
			$results = array_unique($results);
			$results = implode(',', $results);
			$results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}files WHERE id IN (".$results.") ORDER BY FIELD(id, ".$results.")"); 


		} else { 
			$results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}files WHERE  FIND_IN_SET({$atts['product_id']},product) > 0 "); 
		}

		$langs = $wpdb->get_results("SELECT `language` FROM `{$wpdb->prefix}files` WHERE FIND_IN_SET({$atts['product_id']},product) > 0 GROUP BY `language`");
		
	}

	elseif( isset($atts['industry_id']) && strlen($atts['industry_id']) > 0 ){
		
		$row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}sort WHERE `sort_type`='industry' AND `type_id` = ".$atts['industry_id']);
		if( $row ){ $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}files WHERE id IN (".$row->doc_array.") ORDER BY FIELD(id, ".$row->doc_array.")"); 

			$results = wp_list_pluck( $results, 'id' );
			$doc_array = explode(',', $row->doc_array);
			$results = array_merge($doc_array,$results );
			$results = array_unique($results);
			$results = implode(',', $results);
			$results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}files WHERE id IN (".$results.") ORDER BY FIELD(id, ".$results.")"); 
		} 
		else { $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}files WHERE parent_id IS NULL AND FIND_IN_SET({$atts['industry_id']},industry) > 0 "); }
		$langs = $wpdb->get_results("SELECT `language` FROM `{$wpdb->prefix}files` WHERE FIND_IN_SET({$atts['industry_id']},industry) > 0 GROUP BY `language`");
	}

	elseif( isset($atts['doc_id']) && strlen($atts['doc_id']) > 0 ){

		$results = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}files` WHERE `id` = {$atts['doc_id']}");
		$langs = $wpdb->get_results("SELECT `language` FROM `{$wpdb->prefix}files` WHERE `id` = {$atts['doc_id']} GROUP BY `language`");
	}


	if( !empty($results) ){

		$langs = wp_list_pluck( $langs, 'language' );

		$string = '<p style="text-align: right;"><select name="language" id="language" style="width: 120px;height: 29px;">';
		foreach( $langs as $lang):
			$string .= '<option value="'.$lang.'">'.$lang.'</option>';
		endforeach;
		$string .= '</select></p>';

		$string .=  '<div id="documents">';
		foreach($results as $result):
			if( is_null($result->parent_id) ){
				$string .= '<div class="docset"><a href="'.get_bloginfo('url').'/portal/file/download/'.$result->id.'">'.$result->nicename.' <span style="font-size: 0.9em">['.$result->language.']</span></a></div>';
			}
		endforeach;
		$string .=  '</div>';

	} else {

		$string =  '<div id="documents">';
		$string .= '<div class="docset">There are no associated documentation for this product.</div>';
		$string .=  '</div>';
	}


	ob_start();
	include dirname(__DIR__).'/partial/shortcode-script.php';
	$string .= ob_get_clean();

	return $string;
}

add_shortcode( 'docset', 'docset_shortcode_func' );