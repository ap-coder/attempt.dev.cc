<?php

namespace WPMVC;

class CronController extends Controller {
	
	public function __construct(){ }

	public function pagePOST(){
		$data = $this->clean_query($_POST);
		wp_redirect(get_admin_url().'admin.php?page='.$_POST['page'].'&'.http_build_query($data));exit;
	}

	public function clean_query($data){
		foreach($data as $key =>$value){
			if( empty($data[$key]) )
				unset($data[$key]);
		}
		return $data;
	}

}