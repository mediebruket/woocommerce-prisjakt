<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @wordpress-plugin
 * Plugin Name:       Woocommerce Prisjakt
 * Description:       Export product CSV readable for prisjakt.
 * Version:           1.0.10
 * Author:            Mediebruket
 * Author URI:        http://mediebruket.no
 * Text Domain:       woocommerce-prisjakt
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define('WCP_SLUG', 'woocommerce-prisjakt');
define('WCP_VERSION', '1.0.9');

include('includes/class-wc-prisjakt-options.php');
include('includes/class-wc-prisjakt-updater.php');
include('includes/class-wc-prisjakt-html-builder.php');
include('includes/utils.php');

$plugin_file = __FILE__;
global $plugin_path;
$plugin_path = plugin_dir_path( __FILE__ );
define('WC_Prisjakt_Admin', 'wc-prisjakt-admin');
new WC_PrisjaktUpdater( __FILE__ );


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-woocommerce-prisjakt-activator.php
 */
function activate_woocommerce_prisjakt() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-prisjakt-activator.php';
	Woocommerce_Prisjakt_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-woocommerce-prisjakt-deactivator.php
 */
function deactivate_woocommerce_prisjakt() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-prisjakt-deactivator.php';
	Woocommerce_Prisjakt_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_woocommerce_prisjakt' );
register_deactivation_hook( __FILE__, 'deactivate_woocommerce_prisjakt' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-prisjakt.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_woocommerce_prisjakt() {
	$plugin = new Woocommerce_Prisjakt();
	$plugin->run();

}
run_woocommerce_prisjakt();
