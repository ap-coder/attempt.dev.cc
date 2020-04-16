<?php

namespace CODEQR;

class ProductController extends Controller{

	public function __construct(){

		parent::__construct();
	}
	
	public function updatePOST(){

		global $wpdb;
		
		$visible = isset($_POST['visible']) ? 1 : 0;


		// Going to put the Product data into the new table wp_codeqr_products
		
		$row_id = $wpdb->get_var("SELECT id as id FROM wp_codeqr_products WHERE `product_id` = {$_POST['product_id']}");

		if( $row_id ){
			
			$wpdb->update('wp_codeqr_products', array(
				'model_number' => $_POST['model_number'],
				'visible' => $visible
			),array(
				'product_id' => $_POST['product_id']
			));

		} else {

			$wpdb->insert("wp_codeqr_products", array(
				'product_id' => $_POST['product_id'], 
				'model_number' => $_POST['model_number'],				
				'visible' => $visible
			));
		}


		$this->uploadImage();

		$this->uploadPDF();

		wp_redirect( admin_url() . 'admin.php?page=codeqr-products&product_id='.$_POST['product_id'], $status = 302 ); exit;
	}

	public function featurePOST(){

		global $wpdb;

		$action = $_POST['action'];
		$feature_id = $_POST['feature_id'];
		$product_id = $_POST['product_id'];

		switch ($action) {

			case 'add':				
				$wpdb->insert("wp_codeqr_product_features", array('features' => $feature_id, 'product_id' => $product_id));
				break;

			case 'delete':
				$wpdb->query("DELETE FROM wp_codeqr_product_features WHERE features = '{$feature_id}' AND product_id = {$product_id}");
				break;
			
		}

		$this->response->success('success');
	}

	public function uploadImage(){

		if( isset($_FILES) && !empty($_FILES['file']['name']) ){
			$errors = $this->setVideoThumbnailAsFeaturedImage($_POST['product_id']);
		}
	}

	public function uploadPDF(){

		if( isset( $_FILES['pdf_cover'] ) && $_FILES['pdf_cover']['size'] ){

			$file = $_FILES['pdf_cover']['name'];

			$path_parts = pathinfo($file);

			$fext = $path_parts['extension'];

			$fname = $path_parts['filename']; // since PHP 5.2.0

			$fname = $fname.rand(1,9999).time().'.'.$fext;

			$destination =  dirname(QRROOT_PATH) . '/pdfs/uploaded/';

			$moved = move_uploaded_file($_FILES['pdf_cover']['tmp_name'], $destination . $fname);

			$pdf = new \setasign\Fpdi\Fpdi();

			// $pdf = new Fpdi();

			// echo '<pre>'.print_r($pdf, 1).'</pre>';die('fire');

		 	$pageCount = $pdf->setSourceFile($destination . $fname);

		 	if( !$pageCount ){
		 		$product_id = $_POST['product_id'];
				$status = array('status'=>'failed', 'message' => 'The PDF that you uploaded is a version greater than PDF 1.4. The Free FPDI only supports PDF v1.4 and below (See https://www.setasign.com/fpdi-pdf-parser for more details).');
				// ob_start(); include dirname(QRROOT_PATH) . '/partial/product.php'; $template = ob_get_clean();
				// echo $template; return;

				$this->response->redirect(get_bloginfo( 'url' ) . "/wp-admin/admin.php?page=codeqr-products&product_id=".$product_id, $status, false);
		 	}

			$pageId = $pdf->importPage($pageCount);
			$pdf->addPage();
			$pdf->useTemplate($pageId);

			$pageCount = $pdf->setSourceFile( dirname(QRROOT_PATH) . '/pdfs/templates/toc.pdf');
			$pageId = $pdf->importPage($pageCount);
			$pdf->addPage();
			$pdf->useTemplate($pageId);

			$pageCount = $pdf->setSourceFile( dirname(QRROOT_PATH) . '/pdfs/templates/grid.pdf');
			$pageId = $pdf->importPage($pageCount);
			$pdf->addPage();
			$pdf->useTemplate($pageId);

			@$pdf->output($destination . $fname, 'F');

			global $wpdb;

			//$wpdb->update('wp_codeqr_products', array('pdf_cover'=>$fname, 'pdf_nicename'=>$file), array('product_id'=>$_POST['product_id']));

			$wpdb->query("
				UPDATE `wp_codeqr_products` 
				SET `pdf_cover` = '{$fname}', `pdf_nicename` = '{$file}' 
				WHERE `product_id` = {$_POST['product_id']}
			");


		} else {

			if( !isset($_POST['pdf']) ){

				global $wpdb;

				$wpdb->update('wp_codeqr_products', 
					array(
						'pdf_cover'=>NULL, 
						'pdf_nicename'=>NULL
					), 
					array(
						'product_id'=>$_POST['product_id']
				));
			}

		}
	}

	public function uploadFeaturedImagePOST(){


		$this->setVideoThumbnailAsFeaturedImage($_POST['product_id']);
	}

	public function setVideoThumbnailAsFeaturedImage($post_id){
		
		$errors = $this->validate();

		if( !empty($errors)){
			return $errors;
		}

		if( isset($_FILES) && !empty($_FILES['file']['name']) ){
			
			$target_file = dirname(QRROOT_PATH).'/temp/'.$_FILES["file"]["name"];

			if( !move_uploaded_file($_FILES["file"]["tmp_name"], $target_file) ){
				$errors[] = 'Could not move file to temp folder upon upload.';
				return $errors;
			}



			// Create the new file in the wp uploads folder.
			// ----------------------------------------------------------------------
			
			$goal_image_file = wp_upload_bits( $_FILES["file"]["name"] , null, file_get_contents($target_file) );
			
			// Set post meta about this image. Need the comment ID and need the path.
			// ----------------------------------------------------------------------
			
			if( empty($goal_image_file['error']) ) {

				// Prepare an array of post data for the attachment.
				$attachment = array(
					'guid'           => $goal_image_file['url'], 
					'post_mime_type' => $goal_image_file['type'],
					'post_title'     => $_FILES["file"]["name"],
					'post_content'   => '',
					'post_status'    => 'inherit'
				);

				// Insert the attachment.
				$attach_id = wp_insert_attachment( $attachment, $goal_image_file['file'], $post_id );

				if (!is_wp_error($attach_id)) {

					require_once( ABSPATH . 'wp-admin/includes/image.php' );
					// Generate the metadata for the attachment, and update the database record.
					$attach_data = wp_generate_attachment_metadata( $attach_id, $goal_image_file['file'] );

					$wuam = wp_update_attachment_metadata( $attach_id, $attach_data );

					$spt = set_post_thumbnail( $post_id, $attach_id );

		    }


			} else {

				$this->response->json(array('status'=>'fail', 'message'=>'Image wasn\'t created'));
			}

		}
	}

	protected function validate(){

		$error = array();

		/*if( isset($_POST['name']) && strlen($_POST['name']) < 1 ){
			$error['fname'] = 'Provide a nickname.';
		}
		
		if( isset($_POST['sdesc']) && strlen($_POST['sdesc']) < 1 ){
			$error['sdesc'] = 'Provide a short description.';
		}

		if( isset($_POST['ldesc']) && strlen($_POST['ldesc']) < 1 ){
			$error['ldesc'] = 'Provide a long description.';
		}*/

		if( isset($_FILES) && !empty($_FILES['file']['name']) ){

			// Check if is an image
			// ------------------------------------------
			$check = getimagesize($_FILES["file"]["tmp_name"]);

			if($check === false) {
				$error['upload1'] = "File is not an image.";
			}

			$pi = pathinfo($_FILES["file"]["name"]);

			// Check file size
			if ($_FILES["file"]["size"] > 500000) {
				$error['upload2'] = "Sorry, your file is too large.";
			}

			// Allow certain file formats
			if(strtolower($pi['extension']) != "jpg" && strtolower($pi['extension']) != "png" && strtolower($pi['extension']) != "jpeg"
			&& strtolower($pi['extension']) != "gif" ) {
				$error['upload3'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
			}
		}
		
		/*if( isset($_POST['folder']) && strlen($_POST['folder']) < 1 ){
			$error['folder'] = 'Provide a folder.';
		}
		
		if( isset($_POST['filename']) && strlen($_POST['filename']) < 1 ){
			$error['filename'] = 'Provide a filename.';
		}
		
		if( isset($_POST['nicename']) && (strlen($_POST['nicename']) == 0 || strlen($_POST['nicename']) > 5) ){
			$error['nicename'] = 'Provide a human readable name.';
		}*/


		return $error;
	}
}