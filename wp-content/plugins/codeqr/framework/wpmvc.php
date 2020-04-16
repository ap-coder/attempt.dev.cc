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
require_once 'pdf_libraries/vendor/autoload.php';
require('pdf_libraries/pdf_parser/src/autoload.php');

include 'include/helpers.php';
include 'classes/Autoload.php';
include 'classes/Route.php';
include 'classes/Encrypt.php';
include 'classes/Response.php';
include 'classes/Setup.php';
include 'classes/Validate.php';
include 'include/phpqrcode/qrlib.php';

function run_codeqr_mvc() {
	
	add_rewrite_rule( '^'.QRPATHNAME.'/?$','index.php?'.QRROUTE.'=/','top' );
	add_rewrite_rule( '^'.QRPATHNAME.'(.*)?', 'index.php?'.QRROUTE.'=$matches[1]','top' );

	new \CODEQR_framework\Route;
	new \CODEQR_framework\Setup;
	new \CODEQR_framework\Autoload;
}

add_action('init', 'run_codeqr_mvc');
