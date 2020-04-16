<?php 

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://domain.com
 * @since             1.0.0
 * @package           firmware
 *
 * @wordpress-plugin
 * Plugin Name:       Code Firmware
 * Plugin URI:        http://domain.com
 * Description:       This is the plugin that handles the firmware organization
 * Version:           1.2.4
 * Author:            Syndicate Strategies
 * Author URI:        http://domain.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       firmware
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

function activate_firmware() {
	$Setup = new \FIRMWARE_framework\Setup;
	$Setup->addDocsTable();
}

function deactivate_firmware() {}
register_activation_hook( __FILE__, 'activate_firmware' );
register_deactivation_hook( __FILE__, 'deactivate_firmware' );

function run_firmware() {}
add_action( 'plugins_loaded', 'run_firmware' );
