<?php
/**
 * The VERSION is the folder which your 
 * Controllers, Models, and Views live
 * in the source folder
 */

define('QRVERSION', 'v1');

/**
 * The ROUTE is the query variable
 * Wordpress is set to match in the rewrite rule
 */
define('QRROUTE', 'qr_route');

/**
 * The PATHNAME is the subpath of the domain 
 * and base path of our endpoints such as route/index/test
 */
define('QRPATHNAME', 'codeqr');

if(in_array($_SERVER['REMOTE_ADDR'],array('127.0.0.1', '::1'))){

  	define('QRDEBUG', TRUE);
	define('QRSITE_MODE', 'TEST');

} else {

	define('QRDEBUG', FALSE);
	define('QRSITE_MODE', 'LIVE');

}

define('QRROOT_PATH', __DIR__ . '/');
define('QRLOGS_PATH', __DIR__ . '/logs');
define('QRPUPLOADS_PATH', __DIR__ . '/public/uploads');
define('QRPUBLIC_PATH', __DIR__ . '/public');
define('QRVIEW_PATH', __DIR__ . '/source/'.QRVERSION.'/views');

//define('SECURITY_NONCE', wp_create_nonce('security-nonce' ));