<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://cubedesigns.gr
 * @since             1.0.0
 * @package           Cubedesigns_Recaptcha
 *
 * @wordpress-plugin
 * Plugin Name:       CubeDesigns - reCAPTCHA
 * Plugin URI:        https://cubedesigns.gr
 * Description:       Easily enable/ disable Google reCAPTCHA v2 on login, registration, reset password and commend forms.
 * Version:           1.3.0
 * Author:            Apostolos Gouvalas
 * Author URI:        https://cubedesigns.gr/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cubedesigns-recaptcha
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
define( 'CUBEDESIGNS_RECAPTCHA_VERSION', '1.3.0' );

define( 'CUBEDESIGNS_RECAPTCHA_DEVELOPMENT', true );

// Load WP pluggable so we can use functions like is_user_logged_in()
include_once(ABSPATH . 'wp-includes/pluggable.php');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-cubedesigns-recaptcha-activator.php
 */
function activate_cubedesigns_recaptcha() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cubedesigns-recaptcha-activator.php';
	Cubedesigns_Recaptcha_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-cubedesigns-recaptcha-deactivator.php
 */
function deactivate_cubedesigns_recaptcha() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cubedesigns-recaptcha-deactivator.php';
	Cubedesigns_Recaptcha_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_cubedesigns_recaptcha' );
register_deactivation_hook( __FILE__, 'deactivate_cubedesigns_recaptcha' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-cubedesigns-recaptcha.php';



/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_cubedesigns_recaptcha() {

	$plugin = new Cubedesigns_Recaptcha();
	$plugin->run();

}
run_cubedesigns_recaptcha();
