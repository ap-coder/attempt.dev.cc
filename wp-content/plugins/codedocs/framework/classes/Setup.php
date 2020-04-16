<?php

namespace Framework;

class Setup {

	public $options;

	public function __construct() {
		
		global $wpmvc_main;

		wp_enqueue_style( 'font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.6.2/css/font-awesome.min.css' );
	}


	public function addDocsTable(){

			global $wpdb;

			$wpdb->query("
				CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}docs` (
				    id int NOT NULL auto_increment,
				    name varchar(255) DEFAULT NULL,
				    industry int(11) DEFAULT NULL,
				    product int(11) DEFAULT NULL,
				    language varchar(255) DEFAULT NULL,
				    created_at TIMESTAMP DEFAULT now(),
				    PRIMARY KEY  (`id`)
				)
			");
			
			
			$wpdb->query("
				CREATE TABLE `wp_sort` (
				  `id` int NOT NULL auto_increment,
				  `sort_type` varchar(255) NOT NULL,
				  `type_id` int(11) NOT NULL,
				  `doc_array` varchar(255) DEFAULT NULL,
				  PRIMARY KEY  (`id`)
				)
			");

	}


	public function addFilesTable(){

			global $wpdb;

			$wpdb->query("
				CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}files` (
				    id int NOT NULL auto_increment,
				    folder varchar(255) DEFAULT NULL,
				    filename varchar(255) DEFAULT NULL,
				    nicename varchar(255) DEFAULT NULL,
				    language varchar(255) DEFAULT NULL,
				    industry varchar(255) DEFAULT NULL,
				    product varchar(255) DEFAULT NULL,
				    parent_id int(11) DEFAULT NULL,
				    `set` int(11) DEFAULT NULL,
				    created_at TIMESTAMP DEFAULT now(),
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