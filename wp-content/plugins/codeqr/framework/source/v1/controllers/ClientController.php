<?php

namespace CODEQR;

class ClientController extends Controller {

	public function __construct(){

		parent::__construct();

	}

	public function searchPOST(){
		$data = $this->clean_query($_POST);
		wp_redirect(get_admin_url().'admin.php?page=codeqr-list_feats&'.http_build_query($data));exit;
	}


	public function featurePOST(){

		$data = $this->clean_query($_POST);
		wp_redirect(get_admin_url().'admin.php?page=codeqr-list_feats&'.http_build_query($data));exit;
	}
	public function productPOST(){

		$data = $this->clean_query($_POST);
		wp_redirect(get_admin_url().'admin.php?page=codeqr-products&'.http_build_query($data));exit;
	}

	public function updatePOST(){

		$data = $_POST;

		$errors = $this->validate();

		if( !empty($errors) ){

			$this->response->fail('There are errors in the submission. Please inputs and try again.', $errors);
		}

		$Clients = new ClientModel;

		$updated = $Clients->update($data);

		if( $updated == true ){

			$this->response->success('Client information updated successfully.');

		} else {

			$this->response->fail('The client information was not able to be updated at this time', $updated);

		}
	}


	protected function validate(){

		$error = array();


		if( isset($_POST['company']) && strlen($_POST['company']) == 0 ){
			$error['company'] = 'Please enter your company.';
		}

		if( isset($_POST['title']) && strlen($_POST['title']) == 0 ){
			$error['title'] = 'Please enter your title.';
		}

		if( isset($_POST['phone']) && strlen($_POST['phone']) == 0 ){
			$error['phone'] = 'Please enter your phone.';
		}


		if( isset($_POST['country']) && strlen($_POST['country']) == 0 ){
			$error['country'] = 'Please enter your country.';
		}

		if( isset($_POST['lead_source']) && strlen($_POST['lead_source']) == 0 ){
			$error['lead_source'] = 'Please select how you heard about us.';
		}

		// if( isset($_POST['ProductInterest__c']) && strlen($_POST['ProductInterest__c']) == 0 ){
		// 	$error['ProductInterest__c'] = 'Please enter your platform.';
		// }

		// if( isset($_POST['Referral__c']) && strlen($_POST['Referral__c']) == 0 ){
		// 	$error['Referral__c'] = 'Please provide how you heard about Tachyon.';
		// }

		if( isset($_POST['email']) && ((strlen($_POST['email']) == 0 || ! filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)))){
			$error['email'] = 'Please provide a valid email address.';
		}

		if( isset($_POST['first_name']) && strlen($_POST['first_name']) == 0 ){
			$error['first_name'] = 'Please enter your first name.';
		}

		if( isset($_POST['last_name']) && strlen($_POST['last_name']) == 0 ){
			$error['last_name'] = 'Please enter your last name.';
		}


		return $error;
	}


	private function clean_query($data){
		foreach($data as $key =>$value){
			if( empty($data[$key]) )
				unset($data[$key]);
		}
		return $data;
	}
}