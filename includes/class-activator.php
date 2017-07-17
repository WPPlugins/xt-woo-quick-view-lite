<?php

/**
 * Fired during plugin activation
 *
 * @link       http://xplodedthemes.com
 * @since      1.0.0
 *
 * @package    Woo_Quick_View
 * @subpackage Woo_Quick_View/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Woo_Quick_View
 * @subpackage Woo_Quick_View/includes
 * @author     XplodedThemes <helpdesk@xplodedthemes.com>
 */
class Woo_Quick_View_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		
		add_option('wooqv_activation_redirect', true);
	}

}
