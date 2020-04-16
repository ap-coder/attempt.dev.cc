<?php

namespace CODEQR_framework;

class Setup {

	public $options;

	public function __construct() {
		
		global $FIRMWARE_main;

		wp_enqueue_style( 'font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.6.2/css/font-awesome.min.css' );
	}

	public function addCodeQRTable(){

			global $wpdb;

			$wpdb->query("
				CREATE TABLE IF NOT EXISTS `wp_codeqr_categories` (
				    `id` int NOT NULL auto_increment,
				    `name` varchar(255) DEFAULT NULL,
				    `slug` varchar(255) DEFAULT NULL,
				    `order` int(11) DEFAULT NULL,
				    `created_at` TIMESTAMP DEFAULT now(),
				    PRIMARY KEY  (`id`)
				)
			");
			$wpdb->query("
				CREATE TABLE IF NOT EXISTS `wp_codeqr_features` (
				    `id` int NOT NULL auto_increment,
				    `name` varchar(255) DEFAULT NULL,
				    `code` varchar(255) DEFAULT NULL,
				    `category` varchar(255) DEFAULT NULL,
				    `template` text DEFAULT NULL,
				    `created_at` TIMESTAMP DEFAULT now(),
				    `updated_at` TIMESTAMP NOT NULL DEFAULT NOW() ON UPDATE NOW(),
				    PRIMARY KEY  (`id`)
				)
			");

			$wpdb->query("ALTER TABLE `wp_codeqr_features` ADD `feature_name` varchar(255) DEFAULT NULL");
			$wpdb->query("ALTER TABLE `wp_codeqr_features` ADD `feature_code` varchar(255) DEFAULT NULL");
			$wpdb->query("ALTER TABLE `wp_codeqr_features` ADD `description` TEXT DEFAULT NULL");
			$wpdb->query("ALTER TABLE `wp_codeqr_features` ADD `feature_image` varchar(255) DEFAULT NULL");

			$wpdb->query("
				CREATE TABLE IF NOT EXISTS  `wp_codeqr_product_features` (
				  `id` int(11) NOT NULL,
				  `features` varchar(255) DEFAULT NULL,
				  `product_id` int(11) DEFAULT NULL,
				  `model_number` varchar(255) DEFAULT NULL,
				  `product_image` varchar(255) DEFAULT NULL,
				  `visible` tinyint(1) NOT NULL DEFAULT '0',
				  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
				)
			");
			$wpdb->query("ALTER TABLE `wp_codeqr_product_features` ADD `pdf_cover` varchar(255) DEFAULT NULL");
			$wpdb->query("ALTER TABLE `wp_codeqr_product_features` ADD `pdf_nicename` varchar(255) DEFAULT NULL");

			$wpdb->query("
				CREATE TABLE IF NOT EXISTS `wp_codeqr_feat_cats` (
			  `id` int(11) NOT NULL,
			  `feature_id` int(11) NOT NULL,
			  `sort` int(11) NOT NULL,
			  `categories` varchar(255) DEFAULT NULL
			)");


			$wpdb->query("
				CREATE TABLE IF NOT EXISTS  `wp_codeqr_products` (
				  `id` int NOT NULL auto_increment,
				  `product_id` int(11) DEFAULT NULL,
				  `pdf_cover` varchar(255) DEFAULT NULL,
				  `pdf_nicename` varchar(255) DEFAULT NULL,
				  `product_image` varchar(255) DEFAULT NULL,
				  `model_number` varchar(255) DEFAULT NULL,
				  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				  `visible` tinyint(1) NOT NULL DEFAULT '0',
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