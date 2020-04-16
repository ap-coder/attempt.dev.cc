<?php

namespace CODEQR;

class FeatureController extends Controller{

	public function __construct(){

		parent::__construct();
	}

	public function testGET(){
		
		die('fire');
	}

	public function bulkactionPOST(){
		
		switch($_POST['action']){
			case 'delete':
				global $wpdb;
				$wpdb->query("DELETE FROM wp_codeqr_features WHERE id IN (".implode(',', $_POST['feature_id']).")");
			break;
		}

		if( !empty($wpdb->last_error) ){
			$_SESSION['errors'] = $wpdb->last_error;
			return false;
		} else {
			return true;
		}
	}

	public function saveNamePOST(){

		global $wpdb;

		$updated = $wpdb->update('wp_codeqr_categories', 
			array(
				'name'=>$_POST['name'], 
				'slug'=>sanitize_title($_POST['name'])
			), 
			array('id'=>$_POST['feat_cat_id']), array('%s','%s'), array('%d'));




		if( !empty($wpdb->last_error) ){
			$this->response->fail($wpdb->last_error);
		} else {
			$this->response->success('updated');
		}
	}

	public function savePOST(){

		$options = get_option('codedocs');

		$Featcats = new \CODEQR\FeatcatsModel;
		$Features = new \CODEQR\FeatureModel;
		$ProdFeats = new \CODEQR\ProdFeatsModel;

		$data['feature_categories'] = $Featcats->listFeatCats($_POST);

		if( isset($_POST) && !empty($_POST['action']) && $_POST['action'] == 'add-feature' ){

			// Check for any validation issues.
			// -----------------------------------------------------------
			$errors = validate_feature_post($_POST);

			// If Errors than show fail message over the edit screen
			// -----------------------------------------------------------
			if( !empty($errors) ){

				$status = array('status'=>'failed', 'message'=>'Feature not saved successfully.', 'errors' => $errors);
				// ob_start(); include dirname(__DIR__) . '/partial/feature-edit.php'; $template = ob_get_clean();
				// echo $template;
				$this->response->redirect(admin_url().'admin.php?page=codeqr-add_feat', array('errors' => $errors, 'message'=>'Unable to save feature', 'status'=>'failed'));
				return;
			}

			// If there are custom input settings posted, then encode them
			// -----------------------------------------------------------
			if( !empty($_POST['template_part']) ){

				foreach( $_POST['template_part'] as $key => $temp_part ){

					$_POST['template_part'][$key] = json_decode( stripslashes($_POST['template_part'][$key]) );

				}
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
				if( isset($_POST['feat_cats']) ){

					$Featcats->save($new_feature_id, $_POST['feat_cats']);
				}
				
				// Save the product association
				// -----------------------------------------------------------
				if( isset($_POST['products']) && !empty($_POST['products']) ){
					$ProdFeats->save($_POST['products'], $new_feature_id);
				}

				$status = array('status'=>'success', 'message'=>'Feature saved successfully.');

				wp_redirect( admin_url() . 'admin.php?page=codeqr-list_feats&feature_id='.$new_feature_id, $status = 302 ); 
				exit();

			// Error message
			// -----------------------------------------------------------
			} else {

				$status = array('status'=>'failed', 'message'=>$new_feature_id);

				$this->response->redirect(admin_url().'admin.php?page=codeqr-add_feat', array('data'=>$_POST, 'message'=>'Unable to save feature', 'status'=>'failed'), false);
				
			}
		}
	}

	protected function validate(){

		$error = array();

		if( isset($_POST['name']) && strlen($_POST['name']) < 1 ){
			$error['fname'] = 'Provide a nickname.';
		}
		
		if( isset($_POST['sdesc']) && strlen($_POST['sdesc']) < 1 ){
			$error['sdesc'] = 'Provide a short description.';
		}

		if( isset($_POST['ldesc']) && strlen($_POST['ldesc']) < 1 ){
			$error['ldesc'] = 'Provide a long description.';
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


	public function updatePOST(){

		global $wpdb;

		$options = get_option('codedocs');

		$Featcats = new \CODEQR\FeatcatsModel;
		
		$Features = new \CODEQR\FeatureModel;
		
		$ProdFeats = new \CODEQR\ProdFeatsModel;

		// Check for any validation issues.
		// -----------------------------------------------------------
		$errors = validate_feature_post($_POST);

		// If Errors than show fail message over the edit screen
		// -----------------------------------------------------------
		if( !empty($errors) ){

			$status = array('status'=>'failed', 'message'=>'Feature not saved successfully.', 'errors' => $errors);

			$this->response->redirect(admin_url().'admin.php?page=codeqr-add_feat', array('errors' => $errors, 'message'=>'Unable to save feature', 'status'=>'failed'));

			return;
		}

		// This is Upload image logic
		// -----------------------------------------------------------
		$this->uploadImage($_POST['feature_id']);
		
		$rows_affected = $Features->update($_POST['feature_id'], $_POST);

		if( is_numeric($rows_affected) ){

			// Save the Categories that go along with the features.
			// -----------------------------------------------------------
			
			$Featcats->update($_POST['feature_id'], $_POST['feature_categories']);

			wp_redirect( admin_url() . 'admin.php?page=codeqr-list_feats&feature_id='.$_POST['feature_id'], $status = 302 ); exit;

		} else {

			$this->response->redirect(admin_url().'admin.php?page=codeqr-list_feats&feature_id='.$_POST['feature_id'], 
				array('data'=>$_POST, 'message'=>'Unable to save feature', 'status'=>'failed'), false);
			
		}
	}

	private function uploadImage($feature_id){

		if( isset( $_FILES['feature_qr_upload'] ) && strlen($_FILES['feature_qr_upload']['name']) ){

			$file = $_FILES['feature_qr_upload']['name'];

			$path_parts = pathinfo($file);

			$fext = $path_parts['extension'];

			$fname = $path_parts['filename']; // since PHP 5.2.0

			$fname = $fname.rand(1,9999).time().'.'.$fext;

			if( $_FILES['feature_qr_upload']['size'] > 5000000 ){

				$this->response->json(array('status'=>'failed', 'message'=>'Upload an image that is less than 5MB'));
			}

			$destination = $destination = rtrim(dirname(QRROOT_PATH), "/") . '/' . 'qr_codes';

			$moved = move_uploaded_file($_FILES['feature_qr_upload']['tmp_name'], rtrim($destination, "/") . '/'. $fname);

			global $wpdb;

			$wpdb->update('wp_codeqr_features', array('feature_image'=>$fname), array('id'=>$feature_id));

		} 
		else {

			if( !isset( $_FILES['feature_qr_upload'] ) ){

				global $wpdb;

				$wpdb->update('wp_codeqr_features', array('feature_image'=>NULL), array('id'=>$new_feature_id));
			}

		}
	}
}