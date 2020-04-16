<?php

namespace SOFTWARE_framework;

class Route 
{

	public $json_endpoints;
 
	    public function __construct(){
  
		global $wp;

		$wp->add_query_var(SWROUTE);

		
		$this->json_endpoints = array();

	}
	
}