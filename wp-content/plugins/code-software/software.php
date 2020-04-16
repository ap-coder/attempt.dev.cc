<?php 
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://codecorp.com
 * @since             1.0.0
 * @package           software
 *
 * @wordpress-plugin
 * Plugin Name:       Code software
 * Plugin URI:        http://codecorp.com
 * Description:       This is the plugin that handles the software organization
 * Version:           1.2.4
 * Author:            Phillip Madsen
 * Author URI:        http://codecorp.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       software
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
// require_once plugin_dir_path( __FILE__ ) . 'lib/wp-package-updater/class-wp-package-updater.php';

// $software_updater = new WP_Package_Updater('https://codecorp.com',
//   wp_normalize_path( __FILE__ ),
//   wp_normalize_path( plugin_dir_path( __FILE__ ) )
// );

include 'include/actions.php';
include 'framework/wpmvc.php';

function activate_software() {
	$Setup = new \software_framework\Setup;
	$Setup->addSoftwareTable();
}
 
function deactivate_software() {}

register_activation_hook( __FILE__, 'activate_software' );
register_deactivation_hook( __FILE__, 'deactivate_software' );



function run_software() {}

add_action( 'plugins_loaded', 'run_software' );



// if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
//     require_once dirname(__FILE__) . '/vendor/autoload.php';
// }

// if (!function_exists('d')) {function d() {call_user_func_array('dump', func_get_args()); } }
// if (!function_exists('dd')) {function dd() {call_user_func_array('dump', func_get_args()); die(); } }


