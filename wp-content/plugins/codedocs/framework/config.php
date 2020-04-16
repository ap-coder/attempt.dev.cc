<?php
/**
 * The VERSION is the folder which your 
 * Controllers, Models, and Views live
 * in the source folder
 */
define('VERSION', 'v1');

/**
 * The ROUTE is the query variable
 * Wordpress is set to match in the rewrite rule
 */
define('ROUTE', 'api_route');

/**
 * The PATHNAME is the subpath of the domain 
 * and base path of our endpoints such as route/index/test
 */
define('PATHNAME', 'portal');


if(in_array($_SERVER['REMOTE_ADDR'],array('127.0.0.1', '::1'))){
  define('DEBUG', TRUE);
	define('SITE_MODE', 'TEST');
} else {
	define('DEBUG', FALSE);
	define('SITE_MODE', 'LIVE');
}


define('ROOT_PATH', __DIR__ . '/');
define('LOGS_PATH', __DIR__ . '/logs');
define('PUPLOADS_PATH', __DIR__ . '/public/uploads');
define('PUBLIC_PATH', __DIR__ . '/public');
define('VIEW_PATH', __DIR__ . '/source/'.VERSION.'/views');
//define('SECURITY_NONCE', wp_create_nonce('security-nonce' ));