<?php 
if(!session_id() ) session_start();
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://syndtech.com
 * @since             1.0.0
 * @package           codeqr
 *
 * @wordpress-plugin
 * Plugin Name:       Code QR
 * Plugin URI:        http://syndtech.com
 * Description:       This is the plugin that manages the QR codes.
 * Version:           1.6.0
 * Author:            Syndicated Technologies
 * Author URI:        http://syndtech.com/
 * Text Domain:       codeqr
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once plugin_dir_path( __FILE__ ) . 'lib/wp-package-updater/class-wp-package-updater.php';

$firmware_updater = new WP_Package_Updater(
  'https://jacobrossdev.com',
  wp_normalize_path( __FILE__ ),
  wp_normalize_path( plugin_dir_path( __FILE__ ) )
);

include 'include/actions.php';
include 'framework/wpmvc.php';

function activate_codeqr() {
	$Setup = new \CODEQR_framework\Setup;
	$Setup->addCodeQRTable();
}

function deactivate_codeqr() {}
register_activation_hook( __FILE__, 'activate_codeqr' );
register_deactivation_hook( __FILE__, 'deactivate_codeqr' );

function run_codeqr() {}
add_action( 'plugins_loaded', 'run_codeqr' );

add_image_size( 'product-result', 300,300,true );

function add_roles_on_plugin_activation() {
	add_role( 'associate', 'Associate', array( 'read' => true, 'edit_posts' => false, 'delete_posts' => false ) );
}

register_activation_hook( __FILE__, 'add_roles_on_plugin_activation' );

	$subs = get_role('administrator');
	$subs->add_cap('custom_menu_access');

	// $editor = get_role('associate');
	// $editor->add_cap('custom_menu_access');