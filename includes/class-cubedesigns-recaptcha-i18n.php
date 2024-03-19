<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://cubedesigns.gr
 * @since      1.0.0
 *
 * @package    Cubedesigns_Recaptcha
 * @subpackage Cubedesigns_Recaptcha/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Cubedesigns_Recaptcha
 * @subpackage Cubedesigns_Recaptcha/includes
 * @author     Apostolos Gouvalas <apo.gouv@gmail.com>
 */
class Cubedesigns_Recaptcha_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'cubedesigns-recaptcha',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
