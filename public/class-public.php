<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://xplodedthemes.com
 * @since      1.0.0
 *
 * @package    Woo_Quick_View
 * @subpackage Woo_Quick_View/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woo_Quick_View
 * @subpackage Woo_Quick_View/public
 * @author     XplodedThemes <helpdesk@xplodedthemes.com>
 */
class Woo_Quick_View_Public {

	/**
	 * Core class reference.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      obj    core    Core Class
	 */
	private $core;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    obj    $core    Plugin core class
	 */
	public function __construct( &$core ) {

		$this->core = $core;
		
		$this->load();
	}
	
	public function load() {
		
		$loader = $this->core->plugin_loader();
		
		$loader->add_action( 'wp_enqueue_scripts', $this, 'enqueue_styles' );
		$loader->add_action( 'wp_enqueue_scripts', $this, 'enqueue_scripts' );
			
		// Quick View Ajax		
		$loader->add_action( 'wp_ajax_wooqv_quick_view', $this, 'ajax_quick_view' );
		$loader->add_action( 'wp_ajax_nopriv_wooqv_quick_view', $this, 'ajax_quick_view' );
		
		// Add Quick View Button
		$loader->add_filter( 'woocommerce_loop_add_to_cart_link', $this, 'add_quick_view_trigger', 15 );
		
		// Add Quick View Button Over Image
		$loader->add_action( 'wooqv_after_product_image', $this, 'add_quick_view_trigger_over_image', 1 );
		
		// Add More Info Button
		$loader->add_action( 'woocommerce_after_add_to_cart_button', $this, 'add_more_info_button', 15 );
		
		
		$loader->add_action( 'wp_footer', $this, 'render' );
		
		$this->action_template();
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woo_Quick_View_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woo_Quick_View_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		global $woocommerce;
		
		$suffix      = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$lightbox_en = (bool)wooqv_option('modal_slider_lightbox_enabled', '0');
		
		if ( $lightbox_en ) {
			wp_enqueue_script( $this->core->plugin_slug('prettyPhoto'), $woocommerce->plugin_url() . '/assets/js/prettyPhoto/jquery.prettyPhoto' . $suffix . '.js', array( 'jquery' ), '3.1.5', true );
			wp_enqueue_script( $this->core->plugin_slug('prettyPhoto-init'), $woocommerce->plugin_url() . '/assets/js/prettyPhoto/jquery.prettyPhoto.init' . $suffix . '.js', array( 'jquery' ), $woocommerce->version, true );
			wp_enqueue_style( $this->core->plugin_slug('woocommerce_prettyPhoto_css'), $woocommerce->plugin_url() . '/assets/css/prettyPhoto.css' );
		}
		
		if(!defined('WOOFC_VERSION')) {
			wp_enqueue_script( $this->core->plugin_slug('jquery.serializejson'), $this->core->plugin_url( 'public' ) . 'assets/vendors/jquery.serializejson'.$this->core->script_suffix.'.js', array( 'jquery' ), $this->core->plugin_version(), false );
		}
		
		wp_enqueue_script( 'wc-add-to-cart-variation', $woocommerce->plugin_url() . '/assets/js/frontend/add-to-cart-variation' . $suffix . '.js',  array( 'jquery', 'wp-util' ), $woocommerce->version, true );

		wp_register_style('wooqvicons', $this->core->plugin_url( 'public' ) . 'assets/css/wooqvicons.css', array(), $this->core->plugin_version(), 'all' );
		wp_enqueue_style('wooqvicons');

		wp_enqueue_style($this->core->plugin_slug('lightslider'), $this->core->plugin_url( 'public' ) . 'assets/vendors/lightslider/css/lightslider'.$suffix.'.css', array(), $this->core->plugin_version(), 'all' );

		wp_register_style( $this->core->plugin_slug(), $this->core->plugin_url( 'public' ) . 'assets/css/woo-quick-view.css', array(), $this->core->plugin_version(), 'all' );
		wp_enqueue_style( $this->core->plugin_slug() );

		if(is_rtl()) {
			wp_register_style( $this->core->plugin_slug('rtl'), $this->core->plugin_url( 'public' ) . 'assets/css/rtl.css', array($this->core->plugin_slug()), $this->core->plugin_version(), 'all' );
			wp_enqueue_style( $this->core->plugin_slug('rtl'));
		}
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woo_Quick_View_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woo_Quick_View_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		
		// LOAD VENDORS
		wp_enqueue_script("jquery-effects-core");
		wp_enqueue_script( $this->core->plugin_slug('modernizr'), $this->core->plugin_url( 'public' ) . 'assets/vendors/modernizr.custom'.$this->core->script_suffix.'.js', array( 'jquery' ), $this->core->plugin_version(), false );
		wp_enqueue_script( $this->core->plugin_slug('jquery.touch'), $this->core->plugin_url( 'public' ) . 'assets/vendors/jquery.touch'.$this->core->script_suffix.'.js', array( 'jquery' ), $this->core->plugin_version(), false );
		wp_enqueue_script( $this->core->plugin_slug('velocity'), $this->core->plugin_url( 'public' ) . 'assets/vendors/velocity'.$this->core->script_suffix.'.js', array( 'jquery' ), $this->core->plugin_version(), false );
		wp_enqueue_script( $this->core->plugin_slug('lightslider'), $this->core->plugin_url( 'public' ) . 'assets/vendors/lightslider/js/lightslider'.$this->core->script_suffix.'.js', array( 'jquery' ), $this->core->plugin_version(), false );

		if(is_customize_preview()) {
			wp_enqueue_script( $this->core->plugin_slug('jquery.attrchange'), $this->core->plugin_url( 'public' ) . 'assets/vendors/jquery.attrchange'.$this->core->script_suffix.'.js', array( 'jquery' ), $this->core->plugin_version(), false );
		}
		
		wp_register_script( $this->core->plugin_slug(), $this->core->plugin_url( 'public' ) . 'assets/js/woo-quick-view'.$this->core->script_suffix.'.js', array( 'jquery' ), $this->core->plugin_version(), false );

		$vars = array(
			'ajaxurl' => admin_url('admin-ajax.php'),
			'litemode' => defined('WOOQV_LITE')
		);
		
		wp_localize_script( $this->core->plugin_slug(), 'WOOQV', $vars );
		wp_enqueue_script($this->core->plugin_slug());
	}

	public function ajax_quick_view() {
		
		$error = false;
		$product_id = intval($_REQUEST['id']);

		// set the main wp query for the product
		wp( 'p=' . $product_id . '&post_type=product' );

		// remove product thumbnails gallery
		remove_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20 );
				
		$product_item = woo_quick_view_template('parts/product', array(), true);
		
		$response = array(
			'error' => $error,
			'product_item' => $product_item	
		);

		wp_send_json($response);
	}

	/**
	 * Add quick view button in wc product loop
	 *
	 * @access public
	 * @return void
	 * @since  1.0.0
	 */	
	public function add_quick_view_trigger($link) {
		
		global $product;
		
		$position = wooqv_option('trigger_position', 'before');
		
		if($position == 'over-image') {
			return $link;
		}
		
		$quickViewButton = $this->trigger_button($product->get_id());
		
		if($position == 'before' || $position == 'above') {
			$link = $quickViewButton.$link;
		}else{
			$link = $link.$quickViewButton;
		}
		
		return $link;
	}
	
	public function add_quick_view_trigger_over_image() {
		
		$product_id = get_the_ID();
		
		if(empty($product_id)) {
			return false;
		}
	
		$position = wooqv_option('trigger_position', 'before');
		
		if($position != 'over-image') {
			return false;
		}
		
		echo $this->trigger_button($product_id, 'span');
	}

	public function trigger_button($product_id, $tag = 'a') {

		$classes = array(
			'wooqv-trigger',
			'button',
			'alt' 
		);
		
		$position = wooqv_option('trigger_position', 'before');
		$icon_type = wooqv_option('trigger_icon_type', 'font');
		$icon_only = wooqv_option('trigger_icon_only', '0');
		if(!empty($icon_only) && empty($icon_type)) {
			$icon_only = false;
		}

		$classes[] =  'wooqv-'.esc_attr($position);
		
		if(!empty($icon_type)) {
			$classes[] = 'wooqv-icontype-'.$icon_type;
		}
		
		if(!empty($icon_only)) {
			$classes[] = 'wooqv-icon-only';
		}
			
		$classes = apply_filters('wooqv_trigger_classes', $classes, $product_id);
		$classes = implode(' ', $classes);
			
		$button  = '<'.$tag.' href="#" class="'.esc_attr($classes).'" data-id="' . $product_id . '">';
		if(!empty($icon_type)) {
			$button .= '<span class="'.wooqv_trigger_cart_icon_class().'"></span>';
		}
		if(empty($icon_only)) {
			$button .= esc_html__('Quick View', 'woo-quick-view');
		}
		$button .= '</'.$tag.'>';
		
		return $button;
		
	}
			
	public function add_more_info_button() {

		$classes = array(
			'wooqv-button',
			'wooqv-more-info',
			'button' 
		);

		$classes = apply_filters('wooqv_more_info_button_classes', $classes, get_the_ID());
		$classes = implode(' ', $classes);
				
		?>
		<button type="button" class="<?php echo esc_attr($classes); ?>" onclick="location.href='<?php the_permalink(); ?>'"><?php esc_html_e('More info', 'woo-quick-view');?></button>
		<?php
	}

	public function action_template() {

		add_action( 'wooqv_product_summary', 'woocommerce_template_single_title', 5 );
		add_action( 'wooqv_product_summary', 'woocommerce_template_single_rating', 10 );
		add_action( 'wooqv_product_summary', 'woocommerce_template_single_price', 15 );
		add_action( 'wooqv_product_summary', 'woocommerce_template_single_excerpt', 20 );
		add_action( 'wooqv_product_summary', 'woocommerce_template_single_meta', 25 );
		add_action( 'wooqv_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
					
	}
	
	public function render() {

		woo_quick_view_template('quickview');
	}	
}
