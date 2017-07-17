<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the slider part of the quick view modal.
 *
 * This template can be overridden by copying it to yourtheme/woo-quick-view/parts/product-slider.php.
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

global $post, $product, $woocommerce, $attachment_ids;

$lightbox_enabled = wooqv_option('modal_slider_lightbox_enabled', '0');
$attachment_ids = $product->get_gallery_attachment_ids();
?>

<div class="wooqv-slider-wrapper" data-attachments="<?php echo count($attachment_ids);?>">

	<ul class="wooqv-slider">
		
		<?php
		if(has_post_thumbnail($post->ID)) {
						
			$light_box_rel = '';
				
			if($lightbox_enabled) {
				
				$light_box_rel = sprintf(
					' data-rel="prettyPhoto%s"',
					$attachment_ids ? '[product-gallery]' : ''
				);
			}
			
			$attachment_id	= get_post_thumbnail_id();
			$props          = wc_get_product_attachment_props($attachment_id, $post );
			$image          = get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ), 0, $props);
			$thumb_image 	= wp_get_attachment_image_src( $attachment_id, apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' ), 0);
			
			echo apply_filters(
				'woocommerce_single_product_image_html',
				sprintf(
					'<li data-thumb="%s" class="selected" itemprop="image" title="%s"><a href="%s"%s>%s</a></li>',
					$thumb_image[0],
					esc_attr( $props['caption'] ),
					esc_url( $props['url'] ),
					$light_box_rel,
					$image
				),
				$post->ID
			);
			
		}else{
			echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<li><img src="%s" alt="%s" /></li>', wc_placeholder_img_src(), __( 'Placeholder', 'woocommerce' ) ), $post->ID );
		}
	
		
		if ( $attachment_ids ) {
	
			$light_box_rel = '';
				
			if($lightbox_enabled) {
				$light_box_rel = ' data-rel="prettyPhoto[product-gallery]"';
			}
			
			foreach ( $attachment_ids as $attachment_id ) {
				
				$props            = wc_get_product_attachment_props( $attachment_id, $post );
				$image            = wp_get_attachment_image( $attachment_id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ), 0, $props );
				$thumb_image 	  = wp_get_attachment_image_src( $attachment_id, apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' ), 0);
			
				echo apply_filters(
					'woocommerce_single_product_image_html',
					sprintf(
						'<li data-thumb="%s" itemprop="image" title="%s"><a href="%s"%s>%s</a></li>',
						$thumb_image[0],
						esc_attr( $props['caption'] ),
						esc_url( $props['url'] ),
						$light_box_rel,
						$image
					),
					$post->ID
				);
	
			}
				
		}
		?>
	</ul> <!-- wooqv-slider -->
	
</div> 
<!-- wooqv-slider-wrapper -->