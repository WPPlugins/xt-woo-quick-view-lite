<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the quick view modal.
 *
 * This template can be overridden by copying it to yourtheme/woo-quick-view/quickview.php.
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
 * @subpackage Woo_Quick_View/public/templates
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
} 
?>

<div class="wooqv-overlay"></div>
<div class="<?php wooqv_class(); ?>" <?php wooqv_attributes();?>>
	
	<div class="wooqv-product"></div>

	<span class="<?php echo wooqv_modal_close_icon_class();?>"><span>

</div>
<?php wooqv_spinner_html(); ?>
