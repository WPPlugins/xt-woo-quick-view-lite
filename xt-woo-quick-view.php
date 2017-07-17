<?php
/*
 * Woo Quick View Lite
 *	
 * Plugin Name: XT Woo Quick View Lite
 * Plugin URI: http://xplodedthemes.com/products/woo-quick-view/
 * Description: An interactive product quick view modal for WooCommerce that provides the user a quick access to the main product information with smooth animation.
 * Version: 1.0.2
 * Author: XplodedThemes
 * Author URI: http://www.xplodedthemes.com
 * Requires at least: 3.8
 * Tested up to: 4.6
 *
 * Text Domain: xt-woo-quick-view
 * Domain Path: /languages/
 *
 * @package	Woo_Quick_View
 * @author  XplodedThemes <helpdesk@xplodedthemes.com>
 * @since 	1.0.0
 *
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */
 
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
	
if ( ! defined( 'WOOQV_VERSION' ) ) {
	define('WOOQV_VERSION', '1.0.2');
}	


define('WOOQV_LITE', true);
define('WOOQV_LITE_PLUGIN', __FILE__);


// Load plugin if no other instances are loaded
if(!class_exists('Woo_Quick_View')) {

	/**
	 * The code that runs during plugin activation.
	 * This action is documented in includes/class-activator.php
	 */
	
	function woo_quick_view_activate() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-activator.php';
		Woo_Quick_View_Activator::activate();
	}
	register_activation_hook( __FILE__, 'woo_quick_view_activate' );
	
	
	/**
	 * The code that runs during plugin deactivation.
	 * This action is documented in includes/class-deactivator.php
	 */
	
	function woo_quick_view_deactivate() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-deactivator.php';
		Woo_Quick_View_Deactivator::deactivate();
	}
	register_deactivation_hook( __FILE__, 'woo_quick_view_deactivate' );
	

	/**
	 * Deactivate the lite version if activated along the full version.
	 */
	 	
	function woo_quick_view_check_upgraded() {
	
	  	if ( defined('WOOQV_LITE') && defined('WOOQV_PRO')) {
		  	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	     	deactivate_plugins( plugin_basename( WOOQV_LITE_PLUGIN ));
	  	}
	}
	add_action( 'plugins_loaded', 'woo_quick_view_check_upgraded', 1 );
	
	/**
	 * Global functions used to access multiple class public methods.
	 */
	require plugin_dir_path( __FILE__ ) . 'includes/global-functions.php';

	/**
	 * Woocommerce hooks.
	 */
	require plugin_dir_path( __FILE__ ) . 'includes/woocommerce.php';
			
	/**
	 * The core plugin class that is used to define internationalization,
	 * admin-specific hooks, and public-facing site hooks.
	 */
	require plugin_dir_path( __FILE__ ) . 'includes/class-core.php';
	
	/**
	 * Begins execution of the plugin.
	 *
	 * Since everything within the plugin is registered via hooks,
	 * then kicking off the plugin from this point in the file does
	 * not affect the page life cycle.
	 *
	 * @since    1.0.0
	 */
	function woo_quick_view() {
		
		return Woo_Quick_View::instance(__FILE__, WOOQV_VERSION);
	}
	woo_quick_view();
}
