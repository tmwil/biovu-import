<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://vumc.org
 * @since             1.0.0
 * @package           Biovu_Import
 *
 * @wordpress-plugin
 * Plugin Name:       BioVU Import
 * Plugin URI:        https://vumc.org
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Travis Wilson
 * Author URI:        https://vumc.org
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       biovu-import
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'BIOVU_IMPORT_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-biovu-import-activator.php
 */
function activate_biovu_import() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-biovu-import-activator.php';
	Biovu_Import_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-biovu-import-deactivator.php
 */
function deactivate_biovu_import() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-biovu-import-deactivator.php';
	Biovu_Import_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_biovu_import' );
register_deactivation_hook( __FILE__, 'deactivate_biovu_import' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-biovu-import.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_biovu_import() {

	$plugin = new Biovu_Import();
	$plugin->run();

}
run_biovu_import();
