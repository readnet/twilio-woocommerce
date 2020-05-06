<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://giannisftaras.dev/
 * @since             1.0.0
 * @package           Rtwilio
 *
 * @wordpress-plugin
 * Plugin Name:       Readnet Twilio Integration
 * Plugin URI:        https://www.readnet.gr/
 * Description:       Send SMS messages to customers using the Twilio API.
 * Version:           1.0.0
 * Author:            Giannis Ftaras (@readnet-publications)
 * Author URI:        https://giannisftaras.dev/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       rtwilio
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'RTWILIO_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-rtwilio-activator.php
 */
function activate_rtwilio() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-rtwilio-activator.php';
	Rtwilio_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-rtwilio-deactivator.php
 */
function deactivate_rtwilio() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-rtwilio-deactivator.php';
	Rtwilio_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_rtwilio' );
register_deactivation_hook( __FILE__, 'deactivate_rtwilio' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-rtwilio.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_rtwilio() {

	$plugin = new Rtwilio();
	$plugin->run();

}
run_rtwilio();
