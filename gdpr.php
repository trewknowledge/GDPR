<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://trewknowledge.com
 * @since             1.0.0
 * @package           GDPR
 *
 * @wordpress-plugin
 * Plugin Name:       GDPR
 * Plugin URI:        https://trewknowledge.com
 * Description:       This plugin is meant to assist a Controller, Data Processor, and Data Protection Officer (DPO) with efforts to meet the obligations and rights enacted under the GDPR.
 * Version:           1.4.0
 * Author:            Trew Knowledge
 * Author URI:        https://trewknowledge.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       gdpr
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
define( 'GDPR_VERSION', '1.4.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-gdpr-activator.php
 */
function activate_gdpr() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-gdpr-activator.php';
	GDPR_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-gdpr-deactivator.php
 */
function deactivate_gdpr() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-gdpr-deactivator.php';
	GDPR_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_gdpr' );
register_deactivation_hook( __FILE__, 'deactivate_gdpr' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-gdpr.php';
require plugin_dir_path( __FILE__ ) . 'includes/helper-functions.php';


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since 1.0.0
 */
new GDPR();
