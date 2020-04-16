<?php

if ( ! defined( 'WPINC' ) ) {
	die;
}

global $wpmvc_main;
global $path_to_error;
global $framework_routes;

$wpmvc_main = __FILE__;
$path_to_error = __DIR__ . '/error/';

include 'config.php';
include 'include/helpers.php';
include 'classes/Autoload.php';
include 'classes/Route.php';
include 'classes/Encrypt.php';
include 'classes/Response.php';
include 'classes/Setup.php';
include 'classes/Validate.php';

function run_firmware_mvc() {
	
	add_rewrite_rule( '^'.FWPATHNAME.'/?$','index.php?'.FWROUTE.'=/','top' );
	add_rewrite_rule( '^'.FWPATHNAME.'(.*)?', 'index.php?'.FWROUTE.'=$matches[1]','top' );

	new \FIRMWARE_framework\Route;
	new \FIRMWARE_framework\Setup;
	new \FIRMWARE_framework\Autoload;
}

add_action('init', 'run_firmware_mvc');
