<?php

namespace FIRMWARE_framework;

class Setup {

	public $options;

	public function __construct() {
		
		global $FIRMWARE_main;

		wp_enqueue_style( 'font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.6.2/css/font-awesome.min.css' );
	}

	public function addDocsTable(){

			global $wpdb;

			$wpdb->query("
				CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}firmware` (
				    `id` int NOT NULL auto_increment,
				    `name` varchar(255) DEFAULT NULL,
				    `sdesc` varchar(255) DEFAULT NULL,
				    `ldesc` varchar(255) DEFAULT NULL,
				    `product` int(11) DEFAULT NULL,
				    `folder` varchar(255) DEFAULT NULL,
				    `filename` varchar(255) DEFAULT NULL,
				    `nicename` varchar(255) DEFAULT NULL,
				    `created_at` TIMESTAMP DEFAULT now(),
				    PRIMARY KEY  (`id`)
				)
			");

			$wpdb->query("
				CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}fwprodfirmware` (
				    `id` int NOT NULL auto_increment,
				    `firmware` int(11) DEFAULT NULL,
				    `product_id` int(11) DEFAULT NULL,
				    `created_at` TIMESTAMP DEFAULT now(),
				    PRIMARY KEY  (`id`)
				)
			");

			$wpdb->query("
				CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}fwsort` (
				  `id` int NOT NULL auto_increment,
				  `sort_type` varchar(255) NOT NULL,
				  `type_id` int(11) NOT NULL,
				  `doc_array` varchar(255) DEFAULT NULL,
				  PRIMARY KEY  (`id`)
				)
			");
	}

	
	public function redirect_404() {
	  	global $wp_query;
	    $wp_query->set_404();
	    status_header(404);
	}

}