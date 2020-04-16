<?php
namespace WPMVC;

class AssetsController extends Controller{

	public function __construct(){
		
		parent::__construct();

		/**
	 	- We call the parent construct in
	 	- order to use properties such as
	 	- $this->request
	 	- $this->response()
		 */
	}
	
	public function imageGET(){
		$image = ROOT_PATH . 'public/images/'.$this->request['params'][1];
		$info=getimagesize($image);
		header('Content-Type: image/'.$info['mime']);
		readfile($image);
		die;
	}
	
}