<?php

namespace WPMVC;

use Urlcrypt\Urlcrypt;

class AuthController extends Controller {
	
	public function __construct(){

		parent::__construct();

		$this->nonced = array(
			'reset',
			'register',
			'login',
			'validate',
			'password',
		);

		$this->nonce_check();
	}

	public function indexGET(){

		wp_redirect( route('account/dashboard') );

		exit;
	}

	public function successGET(){

		$this->response->view('auth.success');
	}

	public function resetGET(){

		if( isset($this->request->params[0]) ){

			$Users = new UserModel;
			
			if( $token = $Users->fetchPasswordResetToken($this->request->params[0]) ){

				$decrypted = Urlcrypt::decode($token);

				if(isset($_SESSION['error'])){
					$data = $_SESSION;
					unset($_SESSION['error']);
					$this->response->view('auth.password_reset2', array_merge($data, array('token'=>$token, 'email'=>$decrypted)));
				}

				$this->response->view('auth.password_reset2', array('token'=>$token, 'email'=>$decrypted));

			} else {
				
				$this->response->view('auth.password_reset', array('error'=>'The link you were provided has expired. Please try again above.'));
			}
		}

		// Send response based on mailer.
		if( isset($_GET['status']) ){

			switch ($_GET['status']) {
				case 'success':
					$this->response->view('auth.password_reset', array('success'=>'Your email was sent successfully.'));
					break;

				case 'reset':
					$this->response->view('auth.password_reset3');
					break;
				
				default:
					$this->response->view('auth.password_reset', array('error'=>'Your email was not set. Support has been notified. We\'ll contact you with a resolution.'));
					break;
			}
		}

		$error = $this->validate();

		// Load normally
		if( !isset($_GET['email']) ){

			$this->response->view('auth.password_reset');
		}

		// Validate Email
		if( !email_exists($_GET['email']) || !empty($error) ){

			$this->response->view('auth.password_reset', array('error'=>'The email you entered doesn\'t exist.'));
		}
	
		// Send email, check if successful
		$mailed = $this->password_reset_email();
	}

	public function logoutGET(){
		wp_logout();
		wp_redirect(site_url());exit;
	}


	public function resetPOST(){
		
		$error = $this->validate();

		if(!empty($error)){
			$_SESSION['error'] = $error;
			wp_redirect(route('auth/reset/') . $_POST['token'] ); exit;
		}

		$Users = new UserModel;

		if( $token = $Users->fetchPasswordResetToken($_POST['token'], $_POST['email']) ){

      $user = get_user_by('email', $_POST['email'] );

			$response = wp_update_user( array(
				'ID' => $user->ID,
				'user_pass' => $_POST['password']
			) );

			if ( is_wp_error( $response ) ) {

				error_log('['.date('Y-m-d H:i:s').'] - Password reset attempt failed : ' .print_r($_POST,1) . "\r\n", 3, ROOT_PATH . 'logs/pwreset.log');

				$this->response->view('auth.password2', array('error'=>array('message'=>"Your password was not reset. Support has been notified. We will contact you with resolution.")));

			} else {
				
				$this->password_reset_email2();

			}
		}
	}

	public function registerPOST(){

		$_SESSION['prospect'] = $_POST;

		$error = $this->validate();
		
		if( !empty($error) ){

			$this->response->view( 'account.register', array('error'=>$error) );
		}

		$user_id = $this->create_account($_SESSION['prospect']);

		$_SESSION['prospect']['user_id'] 		 = $user_id;

		$_SESSION['success'] = true;

		if ( is_wp_error( $user_id ) ) {

			$error_string = $user_id->get_error_message();
   		
			$this->response->view( 'account.register', array('error'=>$error_string) );
		}
		
		$hash = Urlcrypt::encode($_POST['email']);

		$message  = '<p>Thank you for registering for your customer portal. Please click the link below or copy and paste it to your web browser to confirm your email address.</p>';
		$message .= '<p><a href="'.route('auth/confirm').'/'.$hash.'">'.route('auth/confirm').'/'.$hash.'</a></p>';
		
		$this->send_email($_POST['email'], 'Registration Confirmation', $message);

		$this->response->redirect('auth/success', array('message'=>$success_string));		
	}

	public function loginPOST(){

	  if( is_user_logged_in() ){
	  	
	  	if( isset($_POST['ref']) ){

	  		$this->response->redirect($_POST['ref']);
	  	}

	  	if(current_user_can( 'manage_options' ))
	  	$this->response->redirect('admin/dashboard');
	  
	  	$this->response->redirect('account/dashboard');

	  } else {

	  	$this->response->redirect('account/login', array('error'=>'Your account seems to be inactive. Please re-register or <a href="'.site_url('/').'#home-contact-section">contact support</a>.'));
	  }
	}

	public function validatePOST(){
		
		$error = $this->validate();

		if( $_POST['action'] == 'newsletter'){
			$this->handleNewsletter($error);
		}

		if( $_POST['action'] == 'register'){
			$this->handleRegistration($error);
		}

		if( $_POST['action'] == 'password'){
			$this->handlePasswordReset($error);
		}

		if( $_POST['action'] == 'create_account'){
			$this->handleAccountCreation($error);
		}

		if( $_POST['action'] == 'authenticate'){
			$this->handleAuthentication($error);
		}
	}	

	public function passwordPOST(){
		
		$this->response->redirect('account/settings', array('message'=>'Password changed successfully'));
	}

	protected function validate(){

		$error = array();

		if( isset($_POST['email']) && ((strlen($_POST['email']) == 0 || ! filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)))){
			$error['email'] = 'Please provide a valid email address.';
		}

		if( isset($_POST['fname']) && strlen($_POST['fname']) == 0 ){
			$error['fname'] = 'Please enter your first name.';
		}

		if( isset($_POST['lname']) && strlen($_POST['lname']) == 0 ){
			$error['lname'] = 'Please enter your last name.';
		}

		if( isset($_POST['address_city']) && strlen($_POST['address_city']) == 0 ){
			$error['address_city'] = 'Please enter your city.';
		}

		if( isset($_POST['address_state']) && strlen($_POST['address_state']) == 0 ){
			$error['address_state'] = 'Please enter your state.';
		}

		if( isset($_POST['address_zip']) && strlen($_POST['address_zip']) < 5 ){
			$error['address_zip'] = 'Please enter a valid zip code.';
		}

		if( isset($_POST['current_password']) && strlen($_POST['current_password']) < 8 ){
			$error['current_password'] = 'Please enter your current password.';
		}

		if( isset($_POST['password']) && strlen($_POST['password']) < 8 ){
			$error['password'] = 'Please enter a password of at least 8 characters.';
		}

		if( (isset($_POST['password2']) && ($_POST['password'] != $_POST['password2'])) ){
			$error['password'] = 'You passwords did not match, please input two matching passwords.';
		}

		return $error;
	}

	protected function handleAuthentication($error){

		if(session_id())
			unset($_SESSION['error']);
		
		// Is there is an error out the gate?
		// --------------------------------------------------------

		if( !empty($error) ){

			$this->response->json( array('status'=>'fail', 'error'=>$error) );
		}


		// Get the user by email and check if the email exists
		// --------------------------------------------------------

		$user = get_user_by('email', $_POST['email'] );

		if(!$user){

			$error['password'] = "You entered an email or password that does not match our records.";

			$this->response->json( array('status'=>'fail', 'error'=>$error) );

		}

		// Check if the password matches the email
		// --------------------------------------------------------

		$response = wp_authenticate($user->user_email, $_POST['password']);

		if ( is_wp_error( $response ) ) {

			$error['password'] = "You entered an email or password that does not match our records.";		

			$this->response->json( array('status'=>'fail', 'error'=>$error) );			

		}

		// All is good and we are ready to move on to the profile
		// --------------------------------------------------------

		$response = $response->data;
    $set_current_user = wp_set_current_user( $response->ID, $response->user_login );
    wp_set_auth_cookie( $response->ID, isset($_POST['remember']) );    
    do_action( 'wp_login', $response->user_login );
		$this->response->json( array('status'=>'success'));
	}

	protected function handleRegistration($error){

		if( !empty($error) ){

			$this->response->json( array('status'=>'fail', 'error'=>$error) );
		}

		if( email_exists($_POST['email']) ){

			$this->response->json( array('status'=>'fail', 'error'=>'This email has already been registered. Please use another.') );
		}

		$this->response->json( array('status'=>'success'));
	}

	protected function handlePasswordReset($error){

		if( !empty($error) ){

			$this->response->json( array('status'=>'fail', 'error'=>$error) );
		}

		// Check if the password matches the email
		// --------------------------------------------------------

		$response = wp_authenticate($_POST['email'], $_POST['current_password']);

		if ( is_wp_error( $response ) ) {

			$error['current_password'] = "The current password is incorrect.";		

			$this->response->json( array('status'=>'fail', 'error'=>$error) );			

		}

		// All is good and we are ready to move on to the profile
		// --------------------------------------------------------
		$response = $response->data;
    $set_current_user = wp_set_current_user( $response->ID, $response->user_login );
    wp_set_auth_cookie( $response->ID, isset($_POST['remember']) );    
    do_action( 'wp_login', $response->user_login );
		$this->response->json( array('status'=>'success'));
	}

	private function create_account($data){
		
		$exloded = explode('@',$data['email']);

		$user_id = wp_insert_user( array(
			'user_email' => $data['email'],
			'user_pass' => $data['password'],
			'user_login' => $data['email'],
			'user_nicename'=> array_shift($exloded)
		));
		
		\Stripe\Stripe::setApiKey(STRIPE_SEC);
		
		$customer = \Stripe\Customer::create(array(
		  "email" => $data['email']
		));

		update_user_meta($user_id, 'customer_id', $customer->id);

		$_SESSION['prospect']['first_name'] = $_SESSION['prospect']['fname'];
		$_SESSION['prospect']['last_name'] 	= $_SESSION['prospect']['lname'];

		unset($_SESSION['prospect']['fname']);
		unset($_SESSION['prospect']['lname']);
		unset($_SESSION['prospect']['password']);
		unset($_SESSION['prospect']['password2']);

		update_usermeta( $user_id, 'confirmed', 'false' );
		foreach( $_SESSION['prospect'] as $key => $value )
	  	update_user_meta( $user_id, $key, $value );

		return $user_id;
	}

	private function password_reset_email(){

			$User = new UserModel();
			$token = $User->getPasswordResetToken($_GET['email']);

      $user = get_user_by('email', $_GET['email'] );
      $user_id = $user->ID;

      $updated = update_user_meta( $user_id, 'reset_password', $token );

			$link 	 = route('auth/reset/').$token;
			$subject = 'Password Reset Request';
			$message = '<p>We have received a request to reset your password.</p>';
			$message .= '<p>Your password link is <a href="'.$link.'">'.$link.'</a></p>';
			$message .= '<p>If you didn\'t try to reset your password, you can ignore this email. No further action is required.</p>';

			if( $this->send_email($_GET['email'], $subject, $message) ){
				wp_redirect(route('auth/reset?status=success'));exit;
			}
	}
		
	private function password_reset_email2(){

			$subject = 'Password Reset Successful';
			$message = '<p>Your password was successfully reset to: '.$_POST['password'].'</p>';

			if( $this->send_email($_POST['email'], $subject, $message) ){
				wp_redirect(route('auth/reset?status=reset'));exit;
			}
	}


}