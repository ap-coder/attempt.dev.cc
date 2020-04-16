<?php

namespace CODEQR;

class CronController extends Controller {
	
	public function __construct(){ }

	public function testGET(){
		die('fire');
	}

	public function encryptPOST(){
		require QRROOT_PATH . 'include/encrypt.php';
		die;
	}
}