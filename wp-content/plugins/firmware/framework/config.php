<?php
/**
 * The VERSION is the folder which your 
 * Controllers, Models, and Views live
 * in the source folder
 */
define('FWVERSION', 'v1');

/**
 * The ROUTE is the query variable
 * Wordpress is set to match in the rewrite rule
 */
define('FWROUTE', 'fw_route');

/**
 * The PATHNAME is the subpath of the domain 
 * and base path of our endpoints such as route/index/test
 */
define('FWPATHNAME', 'firmware');


if(in_array($_SERVER['REMOTE_ADDR'],array('127.0.0.1', '::1'))){
  define('FWDEBUG', TRUE);
	define('FWSITE_MODE', 'TEST');
} else {
	define('FWDEBUG', FALSE);
	define('FWSITE_MODE', 'LIVE');
}


define('FWROOT_PATH', __DIR__ . '/');
define('FWLOGS_PATH', __DIR__ . '/logs');
define('FWPUPLOADS_PATH', __DIR__ . '/public/uploads');
define('FWPUBLIC_PATH', __DIR__ . '/public');
define('FWVIEW_PATH', __DIR__ . '/source/'.VERSION.'/views');
//define('SECURITY_NONCE', wp_create_nonce('security-nonce' ));