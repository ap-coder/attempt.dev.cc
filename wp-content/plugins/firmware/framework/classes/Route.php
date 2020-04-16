<?php

namespace FIRMWARE_framework;

class Route {

	public $json_endpoints;

	public function __construct(){

		global $wp;

		$wp->add_query_var( FWROUTE );

		$this->json_endpoints = array();

	}
	
}