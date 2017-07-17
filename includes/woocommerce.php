<?php

/* This snippet removes the action that inserts thumbnails to products in teh loop
 * and re-adds the function customized with our wrapper in it.
 * It applies to all archives with products.
 *
 * @original plugin: WooCommerce
 * @author of snippet: Brian Krogsard
 */

remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10);
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10);

/**
 * WooCommerce Loop Product Thumbs
 **/

 if ( ! function_exists( 'woocommerce_template_loop_product_thumbnail' ) ) {

	function woocommerce_template_loop_product_thumbnail() {
		echo woocommerce_get_product_thumbnail();
	} 
 }


/**
 * WooCommerce Product Thumbnail
 **/
 if ( ! function_exists( 'woocommerce_get_product_thumbnail' ) ) {
	
	function woocommerce_get_product_thumbnail( $size = 'shop_catalog', $placeholder_width = 0, $placeholder_height = 0  ) {
		global $post;
		
		$output = '<div class="wooqv-image-wrapper">';
		
		ob_start();
		do_action('wooqv_before_product_image');
		$output .= ob_get_clean();
		
		if ( has_post_thumbnail() ) {
			
			$output .= get_the_post_thumbnail( $post->ID, $size ); 
			
		} else {
		
			$output .= '<img src="'. woocommerce_placeholder_img_src() .'" alt="Placeholder" width="'.$placeholder_width.' height="'.$placeholder_height.'" />';
		
		}
		
		ob_start();
		do_action('wooqv_after_product_image');
		$output .= ob_get_clean();
		
		$output .= '</div>';
		
		return $output;
	}	
	
 }