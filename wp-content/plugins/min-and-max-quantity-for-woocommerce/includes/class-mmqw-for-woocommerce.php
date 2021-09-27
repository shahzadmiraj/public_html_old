<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://www.multidots.com
 * @since      1.0.0
 *
 * @package    Min_Max_Quantity_For_WooCommerce
 * @subpackage Min_Max_Quantity_For_WooCommerce/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Min_Max_Quantity_For_WooCommerce
 * @subpackage Min_Max_Quantity_For_WooCommerce/includes
 * @author     thedotstore <hello@thedotstore.com>
 */
class Min_Max_Quantity_For_WooCommerce {

	const WCPFC_VERSION = MMQW_PLUGIN_VERSION;
	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Min_Max_Quantity_For_WooCommerce_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;
	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;
	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'min-and-max-quantity-for-woocommerce';
		$this->version     = MMQW_PLUGIN_VERSION;

		$this->mmqw_load_dependencies();
		$this->mmqw_set_locale();

		$this->mmqw_init();

		$this->mmqw_define_admin_hooks();
		$this->mmqw_define_public_hooks();

		$prefix = is_network_admin() ? 'network_admin_' : '';
		add_filter( "{$prefix}plugin_action_links_" . MMQW_PLUGIN_BASENAME, array( $this, 'plugin_action_links' ), 10, 4 );
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Min_Max_Quantity_For_WooCommerce_Loader. Orchestrates the hooks of the plugin.
	 * - Min_Max_Quantity_For_WooCommerce_i18n. Defines internationalization functionality.
	 * - MMQW_Min_Max_Quantity_For_WooCommerce_Admin. Defines all hooks for the admin area.
	 * - MMQW_Min_Max_Quantity_For_WooCommerce_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function mmqw_load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mmqw-for-woocommerce-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mmqw-for-woocommerce-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-min-max-quantity-for-woocommerce-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-mmqw-for-woocommerce-public.php';

		/**
		 * Review us admin notice section added
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mmqw-for-woocommerce-user-feedback.php';

		
		$this->loader = new Min_Max_Quantity_For_WooCommerce_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Min_Max_Quantity_For_WooCommerce_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function mmqw_set_locale() {

		$plugin_i18n = new Min_Max_Quantity_For_WooCommerce_i18n();
		$plugin_i18n->set_domain( $this->mmqw_get_plugin_name() );
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function mmqw_get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Init.
	 *
	 * Initialize plugin parts.
	 *
	 * @since 3.0.0
	 */
	public function mmqw_init() {
		// Initialize shipping method class
		add_action( 'woocommerce_shipping_init', array( $this, 'mmqw_init_shipping_method' ) );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function mmqw_define_admin_hooks() {
		$page         = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING );
		$plugin_admin = new MMQW_Min_Max_Quantity_For_WooCommerce_Admin( $this->mmqw_get_plugin_name(), $this->mmqw_get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'mmqw_enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'mmqw_enqueue_scripts' );

		$this->loader->add_action( 'init', $plugin_admin, 'mmqw_redirect_shipping_function' );

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'mmqw_dot_store_menu_shipping_method_pro' );

		$this->loader->add_action( 'admin_init', $plugin_admin, 'mmqw_welcome_shipping_method_screen_do_activation_redirect' );

		$this->loader->add_action( 'admin_head', $plugin_admin, 'mmqw_remove_admin_submenus' );

		$this->loader->add_action( 'mmqw_condition_match_rules', $plugin_admin, 'mmqw_condition_match_rules', 10, 2 );

		$this->loader->add_action( 'wp_ajax_mmqw_sm_sort_order', $plugin_admin, 'mmqw_sm_sort_order' );
		$this->loader->add_action( 'wp_ajax_nopriv_mmqw_sm_sort_order', $plugin_admin, 'mmqw_sm_sort_order' );

		$this->loader->add_action( 'wp_ajax_mmqw_product_fees_conditions_variable_values_product_ajax', $plugin_admin, 'mmqw_product_fees_conditions_variable_values_product_ajax' );
		$this->loader->add_action( 'wp_ajax_nopriv_mmqw_product_fees_conditions_variable_values_product_ajax', $plugin_admin, 'mmqw_product_fees_conditions_variable_values_product_ajax' );

		$this->loader->add_action( 'wp_ajax_mmqw_product_fees_conditions_values_product_ajax', $plugin_admin, 'mmqw_product_fees_conditions_values_product_ajax' );
		$this->loader->add_action( 'wp_ajax_nopriv_mmqw_product_fees_conditions_values_product_ajax', $plugin_admin, 'mmqw_product_fees_conditions_values_product_ajax' );

		$this->loader->add_action( 'wp_ajax_mmqw_wc_multiple_delete_shipping_method', $plugin_admin, 'mmqw_wc_multiple_delete_shipping_method' );
		$this->loader->add_action( 'wp_ajax_nopriv_mmqw_wc_multiple_delete_shipping_method', $plugin_admin, 'mmqw_wc_multiple_delete_shipping_method' );

		if ( ! empty( $page ) && ( false !== strpos( $page, 'mmqw' ) ) ) {
			$this->loader->add_filter( 'admin_footer_text', $plugin_admin, 'mmqw_admin_footer_review' );
		}

		$this->loader->add_action( 'wp_ajax_mmqw_clone_rule', $plugin_admin, 'mmqw_clone_rule' );
		$this->loader->add_action( 'wp_ajax_mmqw_change_status_from_list_section', $plugin_admin, 'mmqw_change_status_from_list_section' );
		$this->loader->add_action( 'wp_ajax_mmqw_change_status_of_advance_pricing_rules', $plugin_admin, 'mmqw_change_status_of_advance_pricing_rules' );

		$this->loader->add_action( 'wp_ajax_mmqw_simple_and_variation_product_list_ajax', $plugin_admin, 'mmqw_simple_and_variation_product_list_ajax' );
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function mmqw_get_version() {
		return $this->version;
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function mmqw_define_public_hooks() {

		$plugin_public = new MMQW_Min_Max_Quantity_For_WooCommerce_Public( $this->mmqw_get_plugin_name(), $this->mmqw_get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'mmqw_enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'mmqw_enqueue_scripts' );
		$this->loader->add_action( 'woocommerce_quantity_input_args', $plugin_public, 'mmqw_quantity_input_args', 10, 2 );
		$this->loader->add_action( 'woocommerce_loop_add_to_cart_args', $plugin_public, 'mmqw_quantity_input_args', 10, 2 );
		$this->loader->add_action( 'woocommerce_available_variation', $plugin_public, 'mmqw_woocommerce_quantity_min_max_variation', 9999, 3 );
		$this->loader->add_action( 'woocommerce_check_cart_items', $plugin_public, 'mmqw_min_max_quantities_proceed_to_checkout_conditions', 10, 2 );

	}

	/**
	 * Allowed html tags used for wp_kses function
	 *
	 * @since     1.0.0
	 *
	 * @param array add custom tags
	 *
	 * @return array
	 */
	public static function mmqw_allowed_html_tags() {
		$allowed_tags = array(
			'a'        => array(
				'href'         => array(),
				'title'        => array(),
				'class'        => array(),
				'target'       => array(),
				'data-tooltip' => array(),
			),
			'ul'       => array( 'class' => array() ),
			'li'       => array( 'class' => array() ),
			'div'      => array( 'class' => array(), 'id' => array() ),
			'select'   => array( 'rel-id' => array(), 'id' => array(), 'name' => array(), 'class' => array(), 'multiple' => array(), 'style' => array() ),
			'input'    => array( 'id' => array(), 'value' => array(), 'name' => array(), 'class' => array(), 'type' => array(), 'data-index' => array() ),
			'textarea' => array( 'id' => array(), 'name' => array(), 'class' => array() ),
			'option'   => array( 'id' => array(), 'selected' => array(), 'name' => array(), 'value' => array() ),
			'br'       => array(),
			'p'        => array(),
			'b'        => array( 'style' => array() ),
			'em'       => array(),
			'strong'   => array(),
			'i'        => array( 'class' => array() ),
			'span'     => array( 'class' => array() ),
			'small'    => array( 'class' => array() ),
			'label'    => array( 'class' => array(), 'id' => array(), 'for' => array() ),
		);

		return $allowed_tags;
	}

	/**
	 * Initialize shipping method.
	 *
	 * Configure and add all the shipping methods available.
	 *
	 * @since 3.0.0
	 *
	 * @uses  MMQW_Shipping_Method class
	 * @uses  MMQW_Forceall_Shipping_Method
	 */
	public function mmqw_init_shipping_method() {
	}

	/**
	 * Return the plugin action links.  This will only be called if the plugin
	 * is active.
	 *
	 * @since 1.0.0
	 *
	 * @param array $actions associative array of action names to anchor tags
	 *
	 * @return array associative array of plugin action links
	 */
	public function plugin_action_links( $actions ) {
		$custom_actions = array(
			'configure' => sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( array( 'page' => 'mmqw-rules-list' ), admin_url( 'admin.php' ) ) ), __( 'Settings', $this->plugin_name ) ),
			'docs'      => sprintf( '<a href="%s" target="_blank">%s</a>', esc_url( '#' ), __( 'Docs', $this->plugin_name ) ),
			'support'   => sprintf( '<a href="%s" target="_blank">%s</a>', esc_url( 'www.thedotstore.com/support' ), __( 'Support', $this->plugin_name ) ),
		);

		// add the links to the front of the actions list
		return array_merge( $custom_actions, $actions );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Min_Max_Quantity_For_WooCommerce_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}
}