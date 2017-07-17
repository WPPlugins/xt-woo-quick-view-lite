<?php

function woo_quick_view_template($slug, $vars = array(), $return = false) {

	$plugin = woo_quick_view();	
	$plugin_path = $plugin->plugin_path('public');
	$template_path = $plugin->template_path();
	$debug_mode = defined('WOOQV_TEMPLATE_DEBUG_MODE') && WOOQV_TEMPLATE_DEBUG_MODE;
	
	$template = '';

	// Look in yourtheme/woo-quick-view/slug.php
	if ( empty($template) && ! $debug_mode ) {
		
		$template = locate_template( array( $template_path . "{$slug}.php" ) );
	}
	
	// Get default slug.php
	if ( empty($template) && file_exists( $plugin_path . "templates/{$slug}.php" ) ) {
		$template = $plugin_path . "templates/{$slug}.php";
	}
	
	// Allow 3rd party plugins to filter template file from their plugin.
	$template = apply_filters( 'woo_quick_view_template', $template, $slug );

	if ( $template ) {
		extract($vars);
		
		if(!$return) {
			require($template);
		}else{
			ob_start();
			require($template);
			$output = ob_get_contents(); 
			ob_end_clean();
			return $output;
		}	
	}
}

function wooqv_class() {
	
	$classes = array('woo-quick-view woocommerce');
	
	$gallery_enabled = wooqv_option('modal_slider_thumb_gallery_enabled', 0);
	$gallery_visible_onhover = wooqv_option('modal_slider_thumb_gallery_visible_hover', 0);
	
	if(!empty($gallery_enabled) && !empty($gallery_visible_onhover)) {
		$classes[] = 'wooqv-thumbs-visible-onhover';
	}
	
	$slider_grayscale_transition = wooqv_option('wqv_', '0');
	if(!empty($slider_grayscale_transition)) {
		$classes[] = 'wooqv-grayscale-transition';
	}

	$classes = apply_filters('wooqv_modal_class', $classes);
	
	echo implode(' ', $classes);
}

function wooqv_attributes() {
	
	$attributes = array(
		'wooqv-close-on-added' 			=> wooqv_option('close_modal_on_added', '0'),
		'wooqv-mobile-slider-width' 	=> wooqv_option('modal_slider_width_mobile', 350),
		'wooqv-mobile-slider-height' 	=> wooqv_option('modal_slider_height_mobile', 350),
		'wooqv-desktop-slider-width' 	=> wooqv_option('modal_slider_width_desktop', 400),
		'wooqv-desktop-slider-height' 	=> wooqv_option('modal_slider_height_desktop', 400),
		'wooqv-box-shadow-blur' 		=> wooqv_option('modal_box_shadow_blur', '30'),
		'wooqv-box-shadow-spread' 		=> wooqv_option('modal_box_shadow_spread', '0'),
		'wooqv-box-shadow-color' 		=> wooqv_option('modal_box_shadow_color', 'rgba(0,0,0,0.3)'),
		
	);
	
	$attributes = apply_filters('wooqv_modal_attributes', $attributes);

	$data_string = '';
	foreach($attributes as $key => $value) {
		$data_string .= ' '.$key.'="'.esc_attr($value).'"';
	}

	echo $data_string;
}

function wooqv_trigger_cart_icon_class() {
	
	$classes = array('wooqv-trigger-icon');
	
	$icon_type = wooqv_option('trigger_icon_type', 'font');
	
	if(empty($icon_type)) {
		return '';
	}
	
	if($icon_type == 'font') {
		
		$icon = wooqv_option('trigger_icon_font');
		
		if(!empty($icon)) {
			$classes[] = $icon;
		}
	}

	$classes = apply_filters('wooqv_trigger_cart_icon_class', $classes);
	
	return implode(' ', $classes);	
}


function wooqv_modal_close_icon_class() {

	$classes = array('wooqv-close-icon');
	
	$close_button_enabled = wooqv_option('modal_close_enabled', '1');
	
	if(!empty($close_button_enabled)) {
		$icon = wooqv_option('modal_close_icon');
		
		if(!empty($icon)) {
			$classes[] = $icon;
		}
	}

	$classes = apply_filters('wooqv_modal_close_icon_class', $classes);
	
	return implode(' ', $classes);	
}

function wooqv_get_spinner() {

	return wooqv_option('modal_overlay_spinner', '7-three-bounce');	
}

function wooqv_spinner_html($return = false, $wrapSpinner = true) {
	
	$spinner_class = 'wooqv-spinner';
	$spinner_type = wooqv_get_spinner();
	
	if(empty($spinner_type)) {
		if($return) {
			return "";
		}	
	}

	switch($spinner_type) {

		case '7-three-bounce':
		
			$spinner = '
			<div class="'.esc_attr($spinner_class).' wooqv-spinner-three-bounce">
		        <div class="wooqv-spinner-child wooqv-spinner-bounce1"></div>
		        <div class="wooqv-spinner-child wooqv-spinner-bounce2"></div>
		        <div class="wooqv-spinner-child wooqv-spinner-bounce3"></div>
		    </div>';
			break;

	}
	
	$spinner = '<div class="wooqv-spinner-inner">'.$spinner.'</div>';
	
	if($wrapSpinner) {
		$spinner = '<div class="wooqv-spinner-wrap">'.$spinner.'</div>';
	}	
	
	if($return) {
		return $spinner;
	}
	
	echo $spinner;
}

function wooqv_option($id, $default = '') {
	
	if(!class_exists('Woo_Quick_View_Customizer')) {
		return $default;
	}
	return Woo_Quick_View_Customizer::get_option($id);
}

function wooqv_is_action($action) {
	
	if(!empty($_GET['wooqvaction']) && $_GET['wooqvaction'] == $action) {
		return true;
	}
	return false;
}

