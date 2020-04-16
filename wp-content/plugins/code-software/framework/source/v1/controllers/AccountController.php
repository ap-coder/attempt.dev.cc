<?php
namespace SOFTWARE;

class AccountController extends Controller{

	protected $private;

	public function __construct(){
		
		parent::__construct();
		
	 	$this->private = array(
	 		'dashboard',
	 		'settings',
			'upload'
	 	);

		$this->nonced = array(
			'updatecard',
			'updateprofile',
			'validate'
		);

	 	// if a user isn't authorized
	 	// go to route in argument.
	 	$this->secure('account/login');
	 	
		$this->nonce_check();
	}

	// Controller Views

		public function loginGET(){

			if( current_user_can('manage_options') ){
				wp_redirect( route('admin/dashboard') ); exit;
			}

			if( is_user_logged_in() && !current_user_can('manage_options') ){
		  	wp_redirect( route('account/dashboard') ); exit;
			}

			$data = array();
			if( session_id() ){
				$data = $_SESSION;
				unset($_SESSION['error']);
			}

			$this->response->view('account.login', $data);
		}

		public function registerGET(){

			$this->response->view('account.register');
		}

	// Controller Requests

		public function uploadPOST(){

			global $current_user;

			$file = $_FILES['headshot']['name'];

			if( $_FILES['headshot']['size'] > 5000000 ){
				$this->response->json(array('status'=>'failed', 'message'=>'Upload an image that is less than 5MB'));
			}

			$Encrypt = new Encrypt();
			
			$user_folder = str_replace('/', '', $Encrypt->encrypt(AUTH_SALT, urldecode($current_user->user_nicename)));

			if( ! file_exists(SWPUPLOADS_PATH . '/' . $user_folder) ){
				mkdir(SWPUPLOADS_PATH . '/' . $user_folder);
			}

			$user_folder = $user_folder . '/' . $_POST['action'];
			
			if( ! file_exists(SWPUPLOADS_PATH . '/' . $user_folder) ){
				mkdir(SWPUPLOADS_PATH . '/' . $user_folder);
			}

			$destination =  SWPUPLOADS_PATH . '/' . $user_folder . '/' . $file;

			$moved = move_uploaded_file($_FILES['headshot']['tmp_name'], $destination);

			$file = $this->resize_image($destination);

			if( $file ){
				
				update_user_meta( $current_user->ID, 'headshot', $user_folder . '/' . $file );

				$this->response->json(array('status'=>'success','message'=>$user_folder . '/' . $file));

			} else {

				$this->response->json(array('status'=>'fail', 'message'=>'Image wasn\'t created'));
			}
		}

		public function validatePOST(){
			
			$error = $this->validate();

			if( !empty($error) ){

				$this->response->json( array('status'=>'fail', 'error'=>$error) );
			}

			$this->response->json( array('status'=>'success') );
		}	

	// Handlers
		protected function validate(){

			$error = array();

			if( isset($_POST['name']) && strlen($_POST['name']) < 1 ){
				$error['fname'] = 'Provide your name.';
			}
			
			if( isset($_POST['company']) && strlen($_POST['company']) < 1 ){
				$error['lname'] = 'Provide your company.';
			}

			if( isset($_POST['address_street']) && strlen($_POST['address_street']) < 1 ){
				$error['address_street'] = 'Provide your street address.';
			}
			
			if( isset($_POST['address_city']) && strlen($_POST['address_city']) < 1 ){
				$error['address_city'] = 'Provide your city.';
			}
			
			if( isset($_POST['address_state']) && strlen($_POST['address_state']) < 1 ){
				$error['address_state'] = 'Provide your state.';
			}
			
			if( isset($_POST['address_zip']) && (strlen($_POST['address_zip']) == 0 || strlen($_POST['address_zip']) > 5) ){
				$error['address_zip'] = 'Provide a valid zip code.';
			}

			if( isset($_POST['email']) && ((strlen($_POST['email']) == 0 || ! filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)))){
				$error['email'] = 'Please provide a valid email address.';
			}

			if( isset($_POST['number']) && ((strlen($_POST['number']) == 0))){
				$error['number'] = 'Please provide a card number.';
			}

			if( isset($_POST['exp_month']) && ((strlen($_POST['exp_month']) == 0))){
				$error['exp_month'] = 'Please provide an expiration month.';
			}

			if( isset($_POST['exp_year']) && ((strlen($_POST['exp_year']) == 0))){
				$error['exp_year'] = 'Please provide an expiration year.';
			}

			if( isset($_POST['cvc']) && ((strlen($_POST['cvc']) == 0))){
				$error['cvc'] = 'Please provide a cvv number.';
			}

			return $error;
		}

	// Helper Functions

		private function resize_image($filename){

			// Content type
			header('Content-type: image/jpeg');

			// Get new dimensions
			list($width, $height) = getimagesize($filename);
			if($height > $width){
				$new_width 	= 490;
				$new_height = $height * ($new_width / $width);
			} else {
				$new_height 	= 490;
				$new_width = $width * ($new_height / $height);
			}

			// Resample
			$image_p = imagecreatetruecolor($new_width, 490);  // true color for best quality
			$image = imagecreatefromjpeg($filename);

			imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width , $new_height , $width, $height);

			// New Image name
			$pi = pathinfo($filename);

			$filename = $pi['filename'] . '-490x490.' . $pi['extension'];

			$new_file = $pi['dirname'] . '/' . $filename;

			$created = imagejpeg($image_p, $new_file); 

			if( $created ){

				// Output
				imagedestroy ( $image_p );
				imagedestroy ( $image );

				return $filename;
			}

			return false;
		}
}