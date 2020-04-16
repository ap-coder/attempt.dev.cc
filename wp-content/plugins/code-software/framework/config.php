<?php
/**
 * The VERSION is the folder which your 
 * Controllers, Models, and Views live
 * in the source folder
 */
define('SWVERSION', 'v1');

/**
 * The ROUTE is the query variable
 * Wordpress is set to match in the rewrite rule
 */
//define('SWROUTE', 'sw_route');
define('SWROUTE', 'swroute');

define( 'SOFTWAREUPLOADS', 'wp-content/'.'softwares' ); 

/**
 * The PATHNAME is the subpath of the domain 
 * and base path of our endpoints such as route/index/test
 */
define('SWPATHNAME', 'software');
    
 
if(in_array($_SERVER['REMOTE_ADDR'],array('127.0.0.1', '::1'))){
  	define('SWDEBUG', TRUE);
	define('SWSITE_MODE', 'TEST');
} else {
	define('SWDEBUG', FALSE);
	define('SWSITE_MODE', 'LIVE');
}


define('SWROOT_PATH', __DIR__ . '/');
define('SWLOGS_PATH', __DIR__ . '/logs');
define('SWPUPLOADS_PATH', __DIR__ . '/public/uploads');
define('SWPUBLIC_PATH', __DIR__ . '/public');
define('SWVIEW_PATH', __DIR__ . '/source/'.SWVERSION.'/views');
//define('SECURITY_NONCE', wp_create_nonce('security-nonce' ));
 

// if (!function_exists('d')) {function d() {call_user_func_array('dump', func_get_args()); } }
// if (!function_exists('dd')) {function dd() {call_user_func_array('dump', func_get_args()); die(); } }

 

// https://attempt.dev.cc/wp-content/plugins/code-software/framework/config.php