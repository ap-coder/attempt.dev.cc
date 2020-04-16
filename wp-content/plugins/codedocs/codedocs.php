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
 * @package           codedocs
 *
 * @wordpress-plugin
 * Plugin Name:       Code Documentation
 * Plugin URI:        http://domain.com
 * Description:       This is the plugin that handles the documentation organization
 * Version:           1.2.16
 * Author:            Syndicate Strategies
 * Author URI:        http://domain.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       codedocs
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once plugin_dir_path( __FILE__ ) . 'lib/wp-package-updater/class-wp-package-updater.php';

$codedocs_updater = new WP_Package_Updater(
  'https://jacobrossdev.com',
  wp_normalize_path( __FILE__ ),
  wp_normalize_path( plugin_dir_path( __FILE__ ) )
);

include 'include/actions.php';
include 'framework/wpmvc.php';

function activate_codedocs() {

	$Setup = new \Framework\Setup;
	$Setup->addDocsTable();
	$Setup->addFilesTable();

}
function deactivate_codedocs() {}
register_activation_hook( __FILE__, 'activate_codedocs' );
register_deactivation_hook( __FILE__, 'deactivate_codedocs' );

function run_codedocs() {}
add_action( 'plugins_loaded', 'run_codedocs' );
