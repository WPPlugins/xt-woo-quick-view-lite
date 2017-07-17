<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the product info part of the quick view modal.
 *
 * This template can be overridden by copying it to yourtheme/woo-quick-view/parts/product-info.php.
 *
 * HOWEVER, on occasion we will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @link       http://xplodedthemes.com
 * @since      1.0.0
 *
 * @package    Woo_Quick_View
 * @subpackage Woo_Quick_View/public/templates/parts
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
} 
?>

<div class="wooqv-item-info">

	<?php do_action('wooqv_product_summary'); ?>	
	
</div> <!-- wooqv-item-info -->
	