<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://xplodedthemes.com
 * @since      1.0.0
 *
 * @package    Woo_Quick_View
 * @subpackage Woo_Quick_View/admin/partials
 */
?>

<div class="wrap about-wrap <?php echo $this->core->plugin_slug("welcome-wrap"); ?>">
	
	<div class="wooqv-version">
		V.<?php echo $this->core->plugin_version(); ?>
		<em>LITE VERSION</em>
	</div>
	
	<div class="about-text">
		<img class="wooqv-logo" src="<?php echo esc_url( $this->core->plugin_url('admin/assets/images', 'logo.png' )); ?>" class="image-50" />
	</div>

	<script type="text/javascript" src="//xplodedthemes.com/widgets/xt-follow/xt-follow-min.js"></script> 
	<script type="text/javascript">
		XT_FOLLOW.init();
	</script> 	
		
	<?php $this->show_nav(); ?>
		
	<div class="wooqv-welcome-section wooqv-<?php echo $this->get_section_id(); ?>-section">
		
		<?php $this->show_section(); ?>
		
	</div>

</div>
