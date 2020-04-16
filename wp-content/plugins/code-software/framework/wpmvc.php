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

function run_software_mvc() {
	
	add_rewrite_rule( '^'.SWPATHNAME.'/?$','index.php?'.SWROUTE.'=/','top' );
	add_rewrite_rule( '^'.SWPATHNAME.'(.*)?', 'index.php?'.SWROUTE.'=$matches[1]','top' );

	new \SOFTWARE_framework\Route;
	new \SOFTWARE_framework\Setup;
	new \SOFTWARE_framework\Autoload;
}

add_action('init', 'run_software_mvc');
