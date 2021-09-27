<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.multidots.com
 * @since      1.0.0
 *
 * @package    Min_Max_Quantity_For_WooCommerce
 * @subpackage Min_Max_Quantity_For_WooCommerce/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Min_Max_Quantity_For_WooCommerce
 * @subpackage Min_Max_Quantity_For_WooCommerce/admin
 * @author     thedotstore <hello@thedotstore.com>
 */
class MMQW_Min_Max_Quantity_For_WooCommerce_Admin {

	const min_max_quantity_post_type = 'wc_mmqw';

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 *
	 * @since    1.0.0
	 *
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->mmqw_load_dependencies();
	}

	/**
	 * Load zone section
	 *
	 * @since    1.0.0
	 */
	private function mmqw_load_dependencies() {
	}

	/**
	 * Shipping zone page
	 *
	 * @uses     MMQW_Shipping_Zone class
	 * @uses     MMQW_Shipping_Zone::output()
	 *
	 * @since    1.0.0
	 */
	public static function mmqw_shipping_zone_page() {
		require_once( plugin_dir_path( __FILE__ ) . 'partials/mmqw-checkout-settings-page.php' );
	}

	/**
	 * Mange Messages
	 *
	 * @since    1.0.0
	 */
	public static function mmqw_manage_messages_page() {
		require_once( plugin_dir_path( __FILE__ ) . 'partials/mmqw-manage-message-page.php' );
	}

	/**
	 * Get MMQW shipping method
	 *
	 * @param string $args
	 *
	 * @return string $default_lang
	 *
	 * @since  3.4
	 *
	 */
	public static function mmqw_get_all_rule_list( $args ) {
		global $sitepress;

		if ( ! empty( $sitepress ) ) {
			$default_lang = $sitepress->get_current_language();
		} else {
			$get_site_language = get_bloginfo( 'language' );
			if ( false !== strpos( $get_site_language, '-' ) ) {
				$get_site_language_explode = explode( '-', $get_site_language );
				$default_lang              = $get_site_language_explode[0];
			} else {
				$default_lang = $get_site_language;
			}
		}
		$sm_args = array(
			'post_type'        => self::min_max_quantity_post_type,
			'posts_per_page'   => - 1,
			'orderby'          => 'menu_order',
			'order'            => 'ASC',
			'suppress_filters' => false,
		);

		if ( 'not_list' === $args ) {
			$sm_args['post_status'] = 'publish';
		}

		$get_all_shipping = new WP_Query( $sm_args );
		$get_all_sm       = $get_all_shipping->get_posts();

		$sort_order   = array();
		$getSortOrder = get_option( 'sm_sortable_order_' . $default_lang );
		if ( isset( $getSortOrder ) && ! empty( $getSortOrder ) ) {
			foreach ( $getSortOrder as $sort ) {
				$sort_order[ $sort ] = array();
			}
		}

		foreach ( $get_all_sm as $carrier_id => $carrier ) {
			$carrier_name = $carrier->ID;

			if ( array_key_exists( $carrier_name, $sort_order ) ) {
				$sort_order[ $carrier_name ][ $carrier_id ] = $get_all_sm[ $carrier_id ];
				unset( $get_all_sm[ $carrier_id ] );
			}
		}

		foreach ( $sort_order as $carriers ) {
			$get_all_sm = array_merge( $get_all_sm, $carriers );
		}

		return $get_all_sm;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @param string $hook display current page name
	 *
	 * @since    1.0.0
	 *
	 */
	public function mmqw_enqueue_styles( $hook ) {
		if ( false !== strpos( $hook, 'dotstore-plugins_page_mmqw' ) || false !== strpos( $hook, 'dotstore-plugins_page_mmqw' ) ) {
			wp_enqueue_style( $this->plugin_name . 'select2-min', plugin_dir_url( __FILE__ ) . 'css/select2.min.css', array(), 'all' );
			wp_enqueue_style( $this->plugin_name . '-jquery-ui-css', plugin_dir_url( __FILE__ ) . 'css/jquery-ui.min.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name . '-timepicker-min-css', plugin_dir_url( __FILE__ ) . 'css/jquery.timepicker.min.css', $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name . 'font-awesome', plugin_dir_url( __FILE__ ) . 'css/font-awesome.min.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name . 'main-style', plugin_dir_url( __FILE__ ) . 'css/style.css', array(), 'all' );
			wp_enqueue_style( $this->plugin_name . 'media-css', plugin_dir_url( __FILE__ ) . 'css/media.css', array(), 'all' );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @param string $hook display current page name
	 *
	 * @since    1.0.0
	 *
	 */
	public function mmqw_enqueue_scripts( $hook ) {
		global $wp;
		wp_enqueue_style( 'wp-jquery-ui-dialog' );
		wp_enqueue_script( 'jquery-ui-accordion' );
		wp_enqueue_script( 'jquery-ui-datepicker' );
		if ( false !== strpos( $hook, 'dotstore-plugins_page_mmqw' ) || false !== strpos( $hook, 'dotstore-plugins_page_mmqw' ) ) {

			wp_enqueue_script( $this->plugin_name . '-select2-full-min', plugin_dir_url( __FILE__ ) . 'js/select2.full.min.js', array(
				'jquery',
				'jquery-ui-datepicker',
			), $this->version, false );
			wp_enqueue_script( $this->plugin_name . '-tablesorter-js', plugin_dir_url( __FILE__ ) . 'js/jquery.tablesorter.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( $this->plugin_name . '-timepicker-js', plugin_dir_url( __FILE__ ) . 'js/jquery.timepicker.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/min-max-quantity-for-woocommerce-admin.js', array(
				'jquery',
				'jquery-ui-dialog',
				'jquery-ui-accordion',
				'jquery-ui-sortable',
				'select2',
			), $this->version, false );
			$current_url = home_url( add_query_arg( $wp->query_vars, $wp->request ) );
			wp_localize_script( $this->plugin_name, 'coditional_vars', array(
					'ajaxurl'                    => admin_url( 'admin-ajax.php' ),
					'ajax_icon'                  => esc_url( plugin_dir_url( __FILE__ ) . '/images/ajax-loader.gif' ),
					'plugin_url'                 => plugin_dir_url( __FILE__ ),
					'dsm_ajax_nonce'             => wp_create_nonce( 'dsm_nonce' ),
					'country'                    => esc_html__( 'Country', 'min-and-max-quantity-for-woocommerce' ),
					'min_quantity'               => esc_html__( 'Min quantity', 'min-and-max-quantity-for-woocommerce' ),
					'max_quantity'               => esc_html__( 'Max quantity', 'min-and-max-quantity-for-woocommerce' ),
					'amount'                     => esc_html__( 'Amount', 'min-and-max-quantity-for-woocommerce' ),
					'equal_to'                   => esc_html__( 'Equal to ( = )', 'min-and-max-quantity-for-woocommerce' ),
					'not_equal_to'               => esc_html__( 'Not Equal to ( != )', 'min-and-max-quantity-for-woocommerce' ),
					'less_or_equal_to'           => esc_html__( 'Less or Equal to ( <= )', 'woocommerce-conditional-product-fees-for-checkout' ),
					'less_than'                  => esc_html__( 'Less then ( < )', 'woocommerce-conditional-product-fees-for-checkout' ),
					'greater_or_equal_to'        => esc_html__( 'greater or Equal to ( >= )', 'woocommerce-conditional-product-fees-for-checkout' ),
					'greater_than'               => esc_html__( 'greater then ( > )', 'woocommerce-conditional-product-fees-for-checkout' ),
					'validation_length1'         => esc_html__( 'Please enter 3 or more characters', 'min-and-max-quantity-for-woocommerce' ),
					'select_category'            => esc_html__( 'Select Category', 'min-and-max-quantity-for-woocommerce' ),
					'delete'                     => esc_html__( 'Delete', 'min-and-max-quantity-for-woocommerce' ),
					'cart_qty'                   => esc_html__( 'Cart Qty', 'min-and-max-quantity-for-woocommerce' ),
					'cart_weight'                => esc_html__( 'Cart Weight', 'min-and-max-quantity-for-woocommerce' ),
					'min_weight'                 => esc_html__( 'Min Weight', 'min-and-max-quantity-for-woocommerce' ),
					'max_weight'                 => esc_html__( 'Max Weight', 'min-and-max-quantity-for-woocommerce' ),
					'cart_subtotal'              => esc_html__( 'Cart Subtotal', 'min-and-max-quantity-for-woocommerce' ),
					'min_subtotal'               => esc_html__( 'Min Quantity', 'min-and-max-quantity-for-woocommerce' ),
					'max_subtotal'               => esc_html__( 'Max Quantity', 'min-and-max-quantity-for-woocommerce' ),
					'validation_length2'         => esc_html__( 'Please enter', 'min-and-max-quantity-for-woocommerce' ),
					'validation_length3'         => esc_html__( 'or more characters', 'min-and-max-quantity-for-woocommerce' ),
					'product_specific'           => esc_html__( 'Product Specific', 'min-and-max-quantity-for-woocommerce' ),
					'cart_specific'              => esc_html__( 'Cart Specific', 'min-and-max-quantity-for-woocommerce' ),
					'min_max_qty_error'          => esc_html__( 'Max qty should greater then min qty on Product tab', 'min-and-max-quantity-for-woocommerce' ),
					'min_max_weight_error'       => esc_html__( 'Max qty should greater then min qry on Category tab', 'min-and-max-quantity-for-woocommerce' ),
					'min_max_subtotal_error'     => esc_html__( 'Max qty should greater then min qty on Variable Product Tab', 'min-and-max-quantity-for-woocommerce' ),
					'min_max_country_error'      => esc_html__( 'Max qty should greater then min qty on Country Tab', 'min-and-max-quantity-for-woocommerce' ),
					'success_msg1'               => esc_html__( 'Order saved successfully', 'min-and-max-quantity-for-woocommerce' ),
					'success_msg2'               => esc_html__( 'Your settings successfully saved.', 'min-and-max-quantity-for-woocommerce' ),
					'warning_msg1'               => sprintf( __( '<p><b style="color: red;">Note: </b>If entered price is more than total shipping price than Message looks like: <b>Shipping Method Name: Curreny Symbole like($) -60.00 Price </b> and if shipping minus price is more than total price than it will set Total Price to Zero(0).</p>', 'min-and-max-quantity-for-woocommerce' ) ),
					'warning_msg2'               => esc_html__( 'Please disable Advance Pricing Rule if you dont need because you have not created rule there.', 'min-and-max-quantity-for-woocommerce' ),
					'warning_msg3'               => esc_html__( 'You need to select product specific option in Shipping Method Rules for product based option', 'min-and-max-quantity-for-woocommerce' ),
					'warning_msg4'               => esc_html__( 'If you active Apply Per Quantity option then Advance Pricing Rule will be disable and not working.', 'min-and-max-quantity-for-woocommerce' ),
					'warning_msg5'               => esc_html__( 'Please fill some required field in Advanced Rules for Min/Max Quantities section', 'min-and-max-quantity-for-woocommerce' ),
					'note'                       => esc_html__( 'Note: ', 'min-and-max-quantity-for-woocommerce' ),
					'click_here'                 => esc_html__( 'Click Here', 'min-and-max-quantity-for-woocommerce' ),
					'weight_msg'                 => esc_html__( 'Please make sure that when you add rules in Advanced Pricing > Cost per weight Section It contains in 
                                                                        above entered weight, otherwise it may be not apply proper shipping charges. For more detail please view 
                                                                        our documentation at ', 'min-and-max-quantity-for-woocommerce' ),
					'cart_contains_product_msg'  => esc_html__( 'Please make sure that when you add rules in Advanced Pricing > Cost per product Section It contains in 
                                                                        above selected product list, otherwise it may be not apply proper shipping charges. For more detail please view 
                                                                        our documentation at ', 'min-and-max-quantity-for-woocommerce' ),
					'cart_contains_category_msg' => esc_html__( 'Please make sure that when you add rules in Advanced Pricing > Cost per category Section It contains in 
                                                                        above selected category list, otherwise it may be not apply proper shipping charges. For more detail please view 
                                                                        our documentation at ', 'min-and-max-quantity-for-woocommerce' ),
					'current_url'                => $current_url,
					'doc_url'                    => "#",
					'list_page_url'              => add_query_arg( array( 'page' => 'mmqw-start-page' ), admin_url( 'admin.php' ) ),
				)
			);
		}
	}

	public function mmqw_dot_store_menu_shipping_method_pro() {
		global $GLOBALS;
		if ( empty( $GLOBALS['admin_page_hooks']['dots_store'] ) ) {
			add_menu_page( 'DotStore Plugins', __( 'DotStore Plugins' ), 'null', 'dots_store', array(
				$this,
				'dot_store_menu_page',
			), MMQW_PLUGIN_URL . 'admin/images/menu-icon.png', 25 );
		}

		add_submenu_page( 'dots_store', 'Minimum and Maximum Quantity for WooCommerce', 'Minimum and Maximum Quantity for WooCommerce', 'manage_options', 'mmqw-rules-list', array(
			$this,
			'mmqw_fee_list_page',
		) );
		add_submenu_page( 'dots_store', 'Add Shipping Method', 'Add Shipping Method', 'manage_options', 'mmqw-add-rules', array( $this, 'mmqw_add_new_fee_page' ) );
		add_submenu_page( 'dots_store', 'Edit Min/Max rule', 'Edit Min/Max rule', 'manage_options', 'mmqw-edit-rule', array( $this, 'mmqw_edit_fee_page' ) );
		add_submenu_page( 'dots_store', 'Checkout Settings', 'Checkout Settings', 'manage_options', 'mmqw-checkout-settings', array( $this, 'mmqw_shipping_zone_page' ) );
		add_submenu_page( 'dots_store', 'Manage Messages', 'Manage Messages', 'manage_options', 'mmqw-manage-messages', array( $this, 'mmqw_manage_messages_page' ) );
		add_submenu_page( 'dots_store', 'Getting Started', 'Getting Started', 'manage_options', 'mmqw-get-started', array( $this, 'mmqw_get_started_page' ) );
		add_submenu_page( 'dots_store', 'Quick info', 'Quick info', 'manage_options', 'mmqw-information', array( $this, 'mmqw_information_page' ) );
	}

	/**
	 * Shipping List Page
	 *
	 * @since    1.0.0
	 */
	public function mmqw_fee_list_page() {
		require_once( plugin_dir_path( __FILE__ ) . 'partials/mmqw-list-rules-page.php' );
	}

	/**
	 * Add New Min/Max Rule Page
	 *
	 * @since    1.0.0
	 */
	public function mmqw_add_new_fee_page() {
		require_once( plugin_dir_path( __FILE__ ) . 'partials/mmqw-add-new-rule-page.php' );
	}

	/**
	 * Edit Min/Max rule Page
	 *
	 * @since    1.0.0
	 */
	public function mmqw_edit_fee_page() {
		require_once( plugin_dir_path( __FILE__ ) . 'partials/mmqw-add-new-rule-page.php' );
	}

	/**
	 * Quick guide page
	 *
	 * @since    1.0.0
	 */
	public function mmqw_get_started_page() {
		require_once( plugin_dir_path( __FILE__ ) . 'partials/mmqw-get-started-page.php' );
	}

	/**
	 * Plugin information page
	 *
	 * @since    1.0.0
	 */
	public function mmqw_information_page() {
		require_once( plugin_dir_path( __FILE__ ) . 'partials/mmqw-information-page.php' );
	}

	/**
	 * Redirect to shipping list page
	 *
	 * @since    1.0.0
	 */
	public function mmqw_redirect_shipping_function() {
		$get_section = filter_input( INPUT_GET, 'section', FILTER_SANITIZE_STRING );
		if ( ( isset( $get_section ) && ! empty( $get_section ) ) && 'mmqw' === $get_section ) {
			wp_safe_redirect( add_query_arg( array( 'page' => 'mmqw-rules-list' ), admin_url( 'admin.php' ) ) );
			exit;
		}
	}

	/**
	 * Redirect to quick start guide after plugin activation
	 *
	 * @uses     mmqw_register_post_type()
	 *
	 * @since    1.0.0
	 */
	public function mmqw_welcome_shipping_method_screen_do_activation_redirect() {
		$this->mmqw_register_post_type();

		// if no activation redirect
		if ( ! get_transient( '_welcome_screen_mmqw_mode_activation_redirect_data' ) ) {
			return;
		}

		// Delete the redirect transient
		delete_transient( '_welcome_screen_mmqw_mode_activation_redirect_data' );

		// if activating from network, or bulk
		$activate_multi = filter_input( INPUT_GET, 'activate-multi', FILTER_SANITIZE_STRING );
		if ( is_network_admin() || isset( $activate_multi ) ) {
			return;
		}
		// Redirect to extra cost welcome  page
		wp_safe_redirect( add_query_arg( array( 'page' => 'mmqw-get-started' ), admin_url( 'admin.php' ) ) );
		exit;
	}

	/**
	 * Register post type
	 *
	 * @since    1.0.0
	 */
	public function mmqw_register_post_type() {
		register_post_type( self::min_max_quantity_post_type, array(
			'labels' => array(
				'name'          => __( 'Advance Shipping Method', 'min-and-max-quantity-for-woocommerce' ),
				'singular_name' => __( 'Advance Shipping Method', 'min-and-max-quantity-for-woocommerce' ),
			),
		) );
	}

	/**
	 * Remove submenu from admin screeen
	 *
	 * @since    1.0.0
	 */
	public function mmqw_remove_admin_submenus() {
		remove_submenu_page( 'dots_store', 'mmqw-add-rules' );
		remove_submenu_page( 'dots_store', 'mmqw-edit-rule' );
		remove_submenu_page( 'dots_store', 'mmqw-checkout-settings' );
		remove_submenu_page( 'dots_store', 'mmqw-manage-messages' );
		remove_submenu_page( 'dots_store', 'mmqw-get-started' );
		remove_submenu_page( 'dots_store', 'mmqw-information' );
	}

	/**
	 * Match condition based on shipping list
	 *
	 * @param int          $sm_post_id
	 * @param array|object $package
	 *
	 * @return bool True if $final_condition_flag is 1, false otherwise. if $sm_status is off then also return false.
	 * @since    1.0.0
	 */
	public function mmqw_condition_match_rules( $sm_post_id, $package = array() ) {

		if ( empty( $sm_post_id ) ) {
			return false;
		}

		global $sitepress;

		$default_lang = $this->mmqw_get_default_langugae_with_sitpress();

		if ( ! empty( $sitepress ) ) {
			$sm_post_id = apply_filters( 'wpml_object_id', $sm_post_id, 'wc_mmqw', true, $default_lang );
		} else {
			$sm_post_id = $sm_post_id;
		}

		$is_passed                    = array();
		$final_is_passed_general_rule = array();
		$new_is_passed                = array();
		$final_condition_flag         = array();

		$cart_array                  = $this->mmqw_get_cart();
		$cart_main_product_ids_array = $this->mmqw_get_main_prd_id( $sitepress, $default_lang );
		$cart_product_ids_array      = $this->mmqw_get_prd_var_id( $sitepress, $default_lang );

		$sm_status     = get_post_status( $sm_post_id );
		$sm_start_date = get_post_meta( $sm_post_id, 'sm_start_date', true );
		$sm_end_date   = get_post_meta( $sm_post_id, 'sm_end_date', true );

		$get_condition_array = get_post_meta( $sm_post_id, 'sm_metabox', true );

		$cost_rule_match = get_post_meta( $sm_post_id, 'cost_rule_match', true );
		if ( ! empty( $cost_rule_match ) ) {
			if ( is_serialized( $cost_rule_match ) ) {
				$cost_rule_match = maybe_unserialize( $cost_rule_match );
			} else {
				$cost_rule_match = $cost_rule_match;
			}

			if ( array_key_exists( 'general_rule_match', $cost_rule_match ) ) {
				$general_rule_match = $cost_rule_match['general_rule_match'];
			} else {
				$general_rule_match = 'all';
			}
		} else {
			$general_rule_match = 'all';
		}

		if ( isset( $sm_status ) && 'off' === $sm_status ) {
			return false;
		}

		if ( ! empty( $get_condition_array ) || '' !== $get_condition_array || null !== $get_condition_array ) {

			$country_array         = array();
			$product_array         = array();
			$variableproduct_array = array();
			$category_array        = array();
			$quantity_array        = array();

			foreach ( $get_condition_array as $key => $value ) {
				if ( array_search( 'country', $value, true ) ) {
					$country_array[ $key ] = $value;
				}
				if ( array_search( 'product', $value, true ) ) {
					$product_array[ $key ] = $value;
				}
				if ( array_search( 'variableproduct', $value, true ) ) {
					$variableproduct_array[ $key ] = $value;
				}
				if ( array_search( 'category', $value, true ) ) {
					$category_array[ $key ] = $value;
				}
				if ( array_search( 'quantity', $value, true ) ) {
					$quantity_array[ $key ] = $value;
				}
				//Check if is country exist
				if ( is_array( $country_array ) && isset( $country_array ) && ! empty( $country_array ) && ! empty( $cart_product_ids_array ) ) {
					$country_passed = $this->mmqw_match_country_rules( $country_array, $general_rule_match );
					if ( 'yes' === $country_passed ) {
						$is_passed['has_fee_based_on_country'] = 'yes';
					} else {
						$is_passed['has_fee_based_on_country'] = 'no';
					}
				}
				//Check if is variable product exist
				if ( is_array( $variableproduct_array ) && isset( $variableproduct_array ) && ! empty( $variableproduct_array ) && ! empty( $cart_product_ids_array ) ) {
					$variable_prd_passed = $this->mmqw_match_variable_products_rule( $cart_product_ids_array, $variableproduct_array, $general_rule_match );
					if ( 'yes' === $variable_prd_passed ) {
						$is_passed['has_fee_based_on_variable_prd'] = 'yes';
					} else {
						$is_passed['has_fee_based_on_variable_prd'] = 'no';
					}
				}
				//Check if is product exist
				if ( is_array( $product_array ) && isset( $product_array ) && ! empty( $product_array ) && ! empty( $cart_product_ids_array ) ) {
					$product_passed = $this->mmqw_match_simple_products_rule( $cart_product_ids_array, $product_array, $general_rule_match );
					if ( 'yes' === $product_passed ) {
						$is_passed['has_fee_based_on_product'] = 'yes';
					} else {
						$is_passed['has_fee_based_on_product'] = 'no';
					}
				}
				//Check if is Category exist
				if ( is_array( $category_array ) && isset( $category_array ) && ! empty( $category_array ) && ! empty( $cart_main_product_ids_array ) ) {
					$category_passed = $this->mmqw_match_category_rule( $cart_main_product_ids_array, $category_array, $general_rule_match );
					if ( 'yes' === $category_passed ) {
						$is_passed['has_fee_based_on_category'] = 'yes';
					} else {
						$is_passed['has_fee_based_on_category'] = 'no';
					}
				}
				//Check if is quantity exist
				if ( is_array( $quantity_array ) && isset( $quantity_array ) && ! empty( $quantity_array ) && ! empty( $cart_product_ids_array ) ) {
					$quantity_passed = $this->mmqw_match_cart_total_cart_qty_rule( $cart_array, $quantity_array, $general_rule_match );
					if ( 'yes' === $quantity_passed ) {
						$is_passed['has_fee_based_on_quantity'] = 'yes';
					} else {
						$is_passed['has_fee_based_on_quantity'] = 'no';
					}
				}
			}

			if ( isset( $is_passed ) && ! empty( $is_passed ) && is_array( $is_passed ) ) {
				$fnispassed = array();
				foreach ( $is_passed as $val ) {
					if ( '' !== $val ) {
						$fnispassed[] = $val;
					}
				}
				if ( 'all' === $general_rule_match ) {
					if ( in_array( 'no', $fnispassed, true ) ) {
						$final_is_passed_general_rule['passed'] = 'no';
					} else {
						$final_is_passed_general_rule['passed'] = 'yes';
					}
				} else {
					if ( in_array( 'yes', $fnispassed, true ) ) {
						$final_is_passed_general_rule['passed'] = 'yes';
					} else {
						$final_is_passed_general_rule['passed'] = 'no';
					}
				}
			}
		}

		if ( empty( $final_is_passed_general_rule ) || '' === $final_is_passed_general_rule || null === $final_is_passed_general_rule ) {
			$new_is_passed['passed'] = 'no';
		} else if ( ! empty( $final_is_passed_general_rule ) && in_array( 'no', $final_is_passed_general_rule, true ) ) {
			$new_is_passed['passed'] = 'no';
		} else if ( empty( $final_is_passed_general_rule ) && in_array( '', $final_is_passed_general_rule, true ) ) {
			$new_is_passed['passed'] = 'no';
		} else if ( ! empty( $final_is_passed_general_rule ) && in_array( 'yes', $final_is_passed_general_rule, true ) ) {
			$new_is_passed['passed'] = 'yes';
		}

		if ( isset( $new_is_passed ) && ! empty( $new_is_passed ) && is_array( $new_is_passed ) ) {
			if ( ! in_array( 'no', $new_is_passed, true ) ) {
				$current_date  = strtotime( date( 'd-m-Y' ) );
				$sm_start_date = ( isset( $sm_start_date ) && ! empty( $sm_start_date ) ) ? strtotime( $sm_start_date ) : '';
				$sm_end_date   = ( isset( $sm_end_date ) && ! empty( $sm_end_date ) ) ? strtotime( $sm_end_date ) : '';
				/*Check for date*/
				if ( ( $current_date >= $sm_start_date || '' === $sm_start_date ) && ( $current_date <= $sm_end_date || '' === $sm_end_date ) ) {
					$final_condition_flag['date'] = 'yes';
				} else {
					$final_condition_flag['date'] = 'no';
				}
			} else {
				$final_condition_flag[] = 'no';
			}
		}

		if ( empty( $final_condition_flag ) && $final_condition_flag === '' ) {
			return false;
		} else if ( ! empty( $final_condition_flag ) && in_array( 'no', $final_condition_flag, true ) ) {
			return false;
		} else if ( empty( $final_condition_flag ) && in_array( '', $final_condition_flag, true ) ) {
			return false;
		} else if ( ! empty( $final_condition_flag ) && in_array( 'yes', $final_condition_flag, true ) ) {
			return true;
		}
	}

	/**
	 * Get default site language
	 *
	 * @return string $default_lang
	 *
	 * @since  3.4
	 *
	 */
	public function mmqw_get_default_langugae_with_sitpress() {
		global $sitepress;

		if ( ! empty( $sitepress ) ) {
			$default_lang = $sitepress->get_current_language();
		} else {
			$default_lang = $this->mmqw_get_current_site_language();
		}

		return $default_lang;
	}

	/**
	 * Get current site langugae
	 *
	 * @return string $default_lang
	 * @since 1.0.0
	 *
	 */
	public function mmqw_get_current_site_language() {
		$get_site_language = get_bloginfo( 'language' );
		if ( false !== strpos( $get_site_language, '-' ) ) {
			$get_site_language_explode = explode( '-', $get_site_language );
			$default_lang              = $get_site_language_explode[0];
		} else {
			$default_lang = $get_site_language;
		}

		return $default_lang;
	}

	/**
	 * Get product id and variation id from cart
	 *
	 * @return array $cart_array
	 * @since 1.0.0
	 *
	 */
	public function mmqw_get_cart() {
		$cart_array = WC()->cart->get_cart();

		return $cart_array;
	}

	/**
	 * Get product id and variation id from cart
	 *
	 * @param string $sitepress
	 * @param string $default_lang
	 *
	 * @return array $cart_main_product_ids_array
	 * @uses  mmqw_get_cart();
	 *
	 * @since 1.0.0
	 *
	 */
	public function mmqw_get_main_prd_id( $sitepress, $default_lang ) {
		$cart_array                  = $this->mmqw_get_cart();
		$cart_main_product_ids_array = array();
		foreach ( $cart_array as $woo_cart_item ) {
			$_product = wc_get_product( $woo_cart_item['product_id'] );
			if ( ! ( $_product->is_virtual( 'yes' ) ) ) {
				if ( ! empty( $sitepress ) ) {
					$cart_main_product_ids_array[] = apply_filters( 'wpml_object_id', $woo_cart_item['product_id'], 'product', true, $default_lang );
				} else {
					$cart_main_product_ids_array[] = $woo_cart_item['product_id'];
				}
			}
		}

		return $cart_main_product_ids_array;
	}

	/**
	 * Get product id and variation id from cart
	 *
	 * @param string $sitepress
	 * @param string $default_lang
	 *
	 * @return array $cart_product_ids_array
	 * @uses  mmqw_get_cart();
	 *
	 * @since 1.0.0
	 *
	 */
	public function mmqw_get_prd_var_id( $sitepress, $default_lang ) {
		$cart_array             = $this->mmqw_get_cart();
		$cart_product_ids_array = array();
		foreach ( $cart_array as $woo_cart_item ) {
			$_product = wc_get_product( $woo_cart_item['product_id'] );
			if ( ! ( $_product->is_virtual( 'yes' ) ) ) {
				if ( $_product->is_type( 'variable' ) ) {
					if ( ! empty( $sitepress ) ) {
						$cart_product_ids_array[] = apply_filters( 'wpml_object_id', $woo_cart_item['variation_id'], 'product', true, $default_lang );
					} else {
						$cart_product_ids_array[] = $woo_cart_item['variation_id'];
					}
				}
				if ( $_product->is_type( 'simple' ) ) {
					if ( ! empty( $sitepress ) ) {
						$cart_product_ids_array[] = apply_filters( 'wpml_object_id', $woo_cart_item['product_id'], 'product', true, $default_lang );
					} else {
						$cart_product_ids_array[] = $woo_cart_item['product_id'];
					}
				}
			}
		}

		return $cart_product_ids_array;
	}

	/**
	 * Match country rules
	 *
	 * @param array  $country_array
	 * @param string $general_rule_match
	 *
	 * @return string $main_is_passed
	 *
	 * @uses     WC_Customer::get_shipping_country()
	 *
	 * @since    3.4
	 *
	 */
	public function mmqw_match_country_rules( $country_array, $general_rule_match ) {
		$selected_country = WC()->customer->get_shipping_country();
		$is_passed        = array();
		foreach ( $country_array as $key => $country ) {
			if ( 'is_equal_to' === $country['product_fees_conditions_is'] ) {
				if ( ! empty( $country['product_fees_conditions_values'] ) ) {
					if ( in_array( $selected_country, $country['product_fees_conditions_values'], true ) ) {
						$is_passed[ $key ]['has_fee_based_on_country'] = 'yes';
					} else {
						$is_passed[ $key ]['has_fee_based_on_country'] = 'no';
					}
				}
				if ( empty( $country['product_fees_conditions_values'] ) ) {
					$is_passed[ $key ]['has_fee_based_on_country'] = 'yes';
				}
			}
			if ( 'not_in' === $country['product_fees_conditions_is'] ) {
				if ( ! empty( $country['product_fees_conditions_values'] ) ) {
					if ( in_array( $selected_country, $country['product_fees_conditions_values'], true )
					     || in_array( 'all', $country['product_fees_conditions_values'], true ) ) {
						$is_passed[ $key ]['has_fee_based_on_country'] = 'no';
					} else {
						$is_passed[ $key ]['has_fee_based_on_country'] = 'yes';
					}
				}
			}
		}

		$main_is_passed = $this->mmqw_check_all_passed_general_rule( $is_passed, 'has_fee_based_on_country', $general_rule_match );

		return $main_is_passed;
	}

	/**
	 * Find unique id based on given array
	 *
	 * @param array  $is_passed
	 * @param string $has_fee_based
	 * @param string $general_rule_match
	 *
	 * @return string $main_is_passed
	 * @since    3.6
	 *
	 */
	public function mmqw_check_all_passed_general_rule( $is_passed, $has_fee_based, $general_rule_match ) {
		$main_is_passed = 'no';
		$flag           = array();
		if ( ! empty( $is_passed ) ) {
			foreach ( $is_passed as $key => $is_passed_value ) {
				if ( 'yes' === $is_passed_value[ $has_fee_based ] ) {
					$flag[ $key ] = true;
				} else {
					$flag[ $key ] = false;
				}
			}
			if ( 'any' === $general_rule_match ) {
				if ( in_array( true, $flag, true ) ) {
					$main_is_passed = 'yes';
				} else {
					$main_is_passed = 'no';
				}
			} else {
				if ( in_array( false, $flag, true ) ) {
					$main_is_passed = 'no';
				} else {
					$main_is_passed = 'yes';
				}
			}
		}

		return $main_is_passed;
	}

	/**
	 * Match variable products rules
	 *
	 * @param array  $cart_product_ids_array
	 * @param array  $variableproduct_array
	 * @param string $general_rule_match
	 *
	 * @return string $main_is_passed
	 * @since    3.4
	 *
	 */
	public function mmqw_match_variable_products_rule( $cart_product_ids_array, $variableproduct_array, $general_rule_match ) {
		$is_passed      = array();
		$passed_product = array();
		foreach ( $variableproduct_array as $key => $product ) {
			if ( 'is_equal_to' === $product['product_fees_conditions_is'] ) {
				if ( ! empty( $product['product_fees_conditions_values'] ) ) {
					foreach ( $product['product_fees_conditions_values'] as $product_id ) {
						settype( $product_id, 'integer' );
						$passed_product[] = $product_id;
						if ( in_array( $product_id, $cart_product_ids_array, true ) ) {
							$is_passed[ $key ]['has_fee_based_on_product'] = 'yes';
							break;
						} else {
							$is_passed[ $key ]['has_fee_based_on_product'] = 'no';
						}
					}
				}
			}
			if ( 'not_in' === $product['product_fees_conditions_is'] ) {
				if ( ! empty( $product['product_fees_conditions_values'] ) ) {
					foreach ( $product['product_fees_conditions_values'] as $product_id ) {
						settype( $product_id, 'integer' );
						if ( in_array( $product_id, $cart_product_ids_array, true ) ) {
							$is_passed[ $key ]['has_fee_based_on_product'] = 'no';
							break;
						} else {
							$is_passed[ $key ]['has_fee_based_on_product'] = 'yes';
						}
					}
				}
			}
		}

		$main_is_passed = $this->mmqw_check_all_passed_general_rule( $is_passed, 'has_fee_based_on_product', $general_rule_match );

		return $main_is_passed;
	}

	/**
	 * Match simple products rules
	 *
	 * @param array  $cart_product_ids_array
	 * @param array  $product_array
	 * @param string $general_rule_match
	 *
	 * @return string $main_is_passed
	 * @since    3.4
	 *
	 */
	public function mmqw_match_simple_products_rule( $cart_product_ids_array, $product_array, $general_rule_match ) {
		$is_passed = array();
		foreach ( $product_array as $key => $product ) {
			if ( 'is_equal_to' === $product['product_fees_conditions_is'] ) {
				if ( ! empty( $product['product_fees_conditions_values'] ) ) {
					foreach ( $product['product_fees_conditions_values'] as $product_id ) {
						settype( $product_id, 'integer' );
						if ( in_array( $product_id, $cart_product_ids_array, true ) ) {
							$is_passed[ $key ]['has_fee_based_on_product'] = 'yes';
							break;
						} else {
							$is_passed[ $key ]['has_fee_based_on_product'] = 'no';
						}
					}
				}
			}
			if ( 'not_in' === $product['product_fees_conditions_is'] ) {
				if ( ! empty( $product['product_fees_conditions_values'] ) ) {
					foreach ( $product['product_fees_conditions_values'] as $product_id ) {
						settype( $product_id, 'integer' );
						if ( in_array( $product_id, $cart_product_ids_array, true ) ) {
							$is_passed[ $key ]['has_fee_based_on_product'] = 'no';
							break;
						} else {
							$is_passed[ $key ]['has_fee_based_on_product'] = 'yes';
						}
					}
				}
			}
		}

		$main_is_passed = $this->mmqw_check_all_passed_general_rule( $is_passed, 'has_fee_based_on_product', $general_rule_match );

		return $main_is_passed;
	}

	/**
	 * Match category rules
	 *
	 * @param array  $cart_product_ids_array
	 * @param array  $category_array
	 * @param string $general_rule_match
	 *
	 * @return string $main_is_passed
	 * @since    3.4
	 * @uses     WC_Product class
	 * @uses     WC_Product::is_virtual()
	 * @uses     wp_get_post_terms()
	 * @uses     mmqw_array_flatten()
	 *
	 */
	public function mmqw_match_category_rule( $cart_product_ids_array, $category_array, $general_rule_match ) {
		$is_passed              = array();
		$cart_category_id_array = array();
		foreach ( $cart_product_ids_array as $product ) {
			$cart_product_category = wp_get_post_terms( $product, 'product_cat', array( 'fields' => 'ids' ) );
			if ( isset( $cart_product_category ) && ! empty( $cart_product_category ) && is_array( $cart_product_category ) ) {
				$cart_category_id_array[] = $cart_product_category;
			}
		}
		$get_cat_all = array_unique( $this->mmqw_array_flatten( $cart_category_id_array ) );

		foreach ( $category_array as $key => $category ) {
			if ( 'is_equal_to' === $category['product_fees_conditions_is'] ) {
				if ( ! empty( $category['product_fees_conditions_values'] ) ) {
					foreach ( $category['product_fees_conditions_values'] as $category_id ) {
						settype( $category_id, 'integer' );
						if ( in_array( $category_id, $get_cat_all, true ) ) {
							$is_passed[ $key ]['has_fee_based_on_category'] = 'yes';
							break;
						} else {
							$is_passed[ $key ]['has_fee_based_on_category'] = 'no';
						}
					}
				}
			}
			if ( 'not_in' === $category['product_fees_conditions_is'] ) {
				if ( ! empty( $category['product_fees_conditions_values'] ) ) {
					foreach ( $category['product_fees_conditions_values'] as $category_id ) {
						settype( $category_id, 'integer' );
						if ( in_array( $category_id, $get_cat_all, true ) ) {
							$is_passed[ $key ]['has_fee_based_on_category'] = 'no';
							break;
						} else {
							$is_passed[ $key ]['has_fee_based_on_category'] = 'yes';
						}
					}
				}
			}
		}

		$main_is_passed = $this->mmqw_check_all_passed_general_rule( $is_passed, 'has_fee_based_on_category', $general_rule_match );

		return $main_is_passed;
	}

	/**
	 * Find unique id based on given array
	 *
	 * @param array $array
	 *
	 * @return array $result if $array is empty it will return false otherwise return array as $result
	 * @since    1.0.0
	 *
	 */
	public function mmqw_array_flatten( $array ) {
		if ( ! is_array( $array ) ) {
			return false;
		}
		$result = array();
		foreach ( $array as $key => $value ) {
			if ( is_array( $value ) ) {
				$result = array_merge( $result, $this->mmqw_array_flatten( $value ) );
			} else {
				$result[ $key ] = $value;
			}
		}

		return $result;
	}

	/**
	 * Match rule based on total cart quantity
	 *
	 * @param array  $cart_array
	 * @param array  $quantity_array
	 * @param string $general_rule_match
	 *
	 * @return string $main_is_passed
	 * @since    3.4
	 *
	 * @uses     WC_Cart::get_cart()
	 *
	 */
	public function mmqw_match_cart_total_cart_qty_rule( $cart_array, $quantity_array, $general_rule_match ) {
		$quantity_total = 0;

		foreach ( $cart_array as $woo_cart_item ) {
			$quantity_total += $woo_cart_item['quantity'];
		}

		$is_passed = array();
		foreach ( $quantity_array as $key => $quantity ) {
			settype( $quantity['product_fees_conditions_values'], 'integer' );
			if ( 'is_equal_to' === $quantity['product_fees_conditions_is'] ) {
				if ( ! empty( $quantity['product_fees_conditions_values'] ) ) {
					if ( $quantity_total === $quantity['product_fees_conditions_values'] ) {
						$is_passed[ $key ]['has_fee_based_on_quantity'] = 'yes';
					} else {
						$is_passed[ $key ]['has_fee_based_on_quantity'] = 'no';
					}
				}
			}
			if ( 'less_equal_to' === $quantity['product_fees_conditions_is'] ) {
				if ( ! empty( $quantity['product_fees_conditions_values'] ) ) {
					if ( $quantity['product_fees_conditions_values'] >= $quantity_total ) {
						$is_passed[ $key ]['has_fee_based_on_quantity'] = 'yes';
					} else {
						$is_passed[ $key ]['has_fee_based_on_quantity'] = 'no';
					}
				}
			}
			if ( 'less_then' === $quantity['product_fees_conditions_is'] ) {
				if ( ! empty( $quantity['product_fees_conditions_values'] ) ) {
					if ( $quantity['product_fees_conditions_values'] > $quantity_total ) {
						$is_passed[ $key ]['has_fee_based_on_quantity'] = 'yes';
					} else {
						$is_passed[ $key ]['has_fee_based_on_quantity'] = 'no';
					}
				}
			}
			if ( 'greater_equal_to' === $quantity['product_fees_conditions_is'] ) {
				if ( ! empty( $quantity['product_fees_conditions_values'] ) ) {
					if ( $quantity['product_fees_conditions_values'] <= $quantity_total ) {
						$is_passed[ $key ]['has_fee_based_on_quantity'] = 'yes';
					} else {
						$is_passed[ $key ]['has_fee_based_on_quantity'] = 'no';
					}
				}
			}
			if ( 'greater_then' === $quantity['product_fees_conditions_is'] ) {
				if ( ! empty( $quantity['product_fees_conditions_values'] ) ) {
					if ( $quantity['product_fees_conditions_values'] < $quantity_total ) {
						$is_passed[ $key ]['has_fee_based_on_quantity'] = 'yes';
					} else {
						$is_passed[ $key ]['has_fee_based_on_quantity'] = 'no';
					}
				}
			}
			if ( 'not_in' === $quantity['product_fees_conditions_is'] ) {
				if ( ! empty( $quantity['product_fees_conditions_values'] ) ) {
					if ( $quantity_total === $quantity['product_fees_conditions_values'] ) {
						$is_passed[ $key ]['has_fee_based_on_quantity'] = 'yes';
					} else {
						$is_passed[ $key ]['has_fee_based_on_quantity'] = 'no';
					}
				}
			}
		}

		$main_is_passed = $this->mmqw_check_all_passed_general_rule( $is_passed, 'has_fee_based_on_quantity', $general_rule_match );

		return $main_is_passed;
	}

	function mmqw_get_woo_version_number() {
		// If get_plugins() isn't available, require it
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		// Create the plugins folder and file variables
		$plugin_folder = get_plugins( '/' . 'woocommerce' );
		$plugin_file   = 'woocommerce.php';

		// If the plugin version number is set, return it
		if ( isset( $plugin_folder[ $plugin_file ]['Version'] ) ) {
			return $plugin_folder[ $plugin_file ]['Version'];
		} else {
			return null;
		}
	}

	/**
	 * Get variation name from cart
	 *
	 * @param string $sitepress
	 * @param string $default_lang
	 *
	 * @return array $cart_product_ids_array
	 * @uses  mmqw_get_cart();
	 *
	 * @since 1.0.0
	 *
	 */
	public function mmqw_get_var_name( $sitepress, $default_lang ) {
		$cart_array             = $this->mmqw_get_cart();
		$cart_product_ids_array = array();
		foreach ( $cart_array as $woo_cart_item ) {
			$_product = wc_get_product( $woo_cart_item['product_id'] );
			if ( ! ( $_product->is_virtual( 'yes' ) ) ) {
				if ( $_product->is_type( 'variable' ) ) {
					if ( ! empty( $sitepress ) ) {
						$cart_product_ids_array[] = apply_filters( 'wpml_object_id', $woo_cart_item['variation_id'], 'product', true, $default_lang );
					} else {
						$cart_product_ids_array[] = $woo_cart_item['variation_id'];
					}
				}
			}
		}

		$variation_cart_product = array();
		foreach ( $cart_product_ids_array as $variation_id ) {
			$variation                = new WC_Product_Variation( $variation_id );
			$variation_cart_product[] = $variation->get_variation_attributes();
		}

		$variation_cart_products_array = array();
		if ( isset( $variation_cart_product ) && ! empty( $variation_cart_product ) ) {
			foreach ( $variation_cart_product as $cart_product_id ) {
				if ( isset( $cart_product_id ) && ! empty( $cart_product_id ) ) {
					foreach ( $cart_product_id as $v ) {
						$variation_cart_products_array[] = $v;
					}
				}
			}
		}

		return $variation_cart_products_array;
	}

	/**
	 * Save shipping order in shipping list section
	 *
	 * @since 1.0.0
	 */
	public function mmqw_sm_sort_order() {
		$default_lang = $this->mmqw_get_default_langugae_with_sitpress();

		$get_smOrderArray = filter_input( INPUT_GET, 'smOrderArray', FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY );
		$smOrderArray     = ! empty( $get_smOrderArray ) ? array_map( 'sanitize_text_field', wp_unslash( $get_smOrderArray ) ) : '';
		if ( isset( $smOrderArray ) && ! empty( $smOrderArray ) ) {
			update_option( 'sm_sortable_order_' . $default_lang, $smOrderArray );
		}
		wp_die();
	}

	/**
	 * Get country list
	 *
	 * @param string $count
	 * @param array  $selected
	 *
	 * @return string $html
	 * @uses   WC_Countries() class
	 *
	 * @since  1.0.0
	 *
	 */
	public function mmqw_get_country_list( $count = '', $selected = array(), $json = false ) {
		$countries_obj = new WC_Countries();
		$getCountries  = $countries_obj->__get( 'countries' );
		$html          = '<select name="fees[product_fees_conditions_values][value_' . esc_attr( $count ) . '][]" class="min_max_select product_fees_conditions_values multiselect2 product_fees_conditions_values_country" multiple="multiple">';
		if ( ! empty( $getCountries ) ) {
			foreach ( $getCountries as $code => $country ) {
				$selectedVal = is_array( $selected ) && ! empty( $selected ) && in_array( $code, $selected, true ) ? 'selected=selected' : '';
				$html        .= '<option value="' . esc_attr( $code ) . '" ' . esc_attr( $selectedVal ) . '>' . esc_html( $country ) . '</option>';
			}
		}

		$html .= '</select>';
		if ( $json ) {
			return $this->mmqw_convert_array_to_json( $getCountries );
		}

		return $html;
	}

	/**
	 * Convert array to json
	 *
	 * @param array $arr
	 *
	 * @return array $filter_data
	 * @since 1.0.0
	 *
	 */
	public function mmqw_convert_array_to_json( $arr ) {
		$filter_data = [];
		foreach ( $arr as $key => $value ) {
			$option                        = [];
			$option['name']                = $value;
			$option['attributes']['value'] = $key;
			$filter_data[]                 = $option;
		}

		return $filter_data;
	}

	/**
	 * Get variable product list in Advance pricing rules
	 *
	 * @param string $count
	 * @param array  $selected
	 *
	 * @return string $html
	 * @uses   WC_Product::is_type()
	 *
	 * @since  3.4
	 *
	 * @uses   mmqw_get_default_langugae_with_sitpress()
	 * @uses   wc_get_product()
	 */
	public function mmqw_get_product_options( $count = '', $selected = array(), $with_variable = false ) {
		global $sitepress;
		$default_lang = $this->mmqw_get_default_langugae_with_sitpress();

		$get_all_products = new WP_Query( array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => - 1,
		) );

		$baselang_variation_product_ids = array();
		$defaultlang_simple_product_ids = array();

		$html = '';
		if ( isset( $get_all_products->posts ) && ! empty( $get_all_products->posts ) ) {
			foreach ( $get_all_products->posts as $get_all_product ) {
				$_product = wc_get_product( $get_all_product->ID );
				if ( $_product->is_type( 'variable' ) ) {
					$variations = $_product->get_available_variations();
					foreach ( $variations as $value ) {
						if ( ! empty( $sitepress ) ) {
							$defaultlang_variation_product_id = apply_filters( 'wpml_object_id', $value['variation_id'], 'product', true, $default_lang );
						} else {
							$defaultlang_variation_product_id = $value['variation_id'];
						}
						$baselang_variation_product_ids[] = $defaultlang_variation_product_id;
					}
				}
				if ( $_product->is_type( 'simple' ) ) {
					if ( ! empty( $sitepress ) ) {
						$defaultlang_simple_product_id = apply_filters( 'wpml_object_id', $get_all_product->ID, 'product', true, $default_lang );
					} else {
						$defaultlang_simple_product_id = $get_all_product->ID;
					}
					$defaultlang_simple_product_ids[] = $defaultlang_simple_product_id;
				}
			}
		}

		if ( true === $with_variable ) {
			$baselang_product_ids = array_merge( $baselang_variation_product_ids, $defaultlang_simple_product_ids );
		} else {
			$baselang_product_ids = $defaultlang_simple_product_ids;
		}

		if ( isset( $baselang_product_ids ) && ! empty( $baselang_product_ids ) ) {
			foreach ( $baselang_product_ids as $baselang_product_id ) {
				$selected    = array_map( 'intval', $selected );
				$selectedVal = is_array( $selected ) && ! empty( $selected ) && in_array( $baselang_product_id, $selected, true ) ? 'selected=selected' : '';
				if ( '' !== $selectedVal ) {
					$html .= '<option value="' . esc_attr( $baselang_product_id ) . '" ' . esc_attr( $selectedVal ) . '>' . '#' . esc_html( $baselang_product_id ) . ' - ' . esc_html( get_the_title( $baselang_product_id ) ) . '</option>';
				}
			}
		}

		return $html;
	}

	/**
	 * Get category list in Advance pricing rules
	 *
	 * @param array $selected
	 *
	 * @return string $html
	 * @since  3.4
	 *
	 * @uses   mmqw_get_default_langugae_with_sitpress()
	 *
	 */
	public function mmqw_get_category_options( $selected = array(), $json ) {
		global $sitepress;
		$default_lang         = $this->mmqw_get_default_langugae_with_sitpress();
		$filter_category_list = [];

		$taxonomy     = 'product_cat';
		$post_status  = 'publish';
		$orderby      = 'name';
		$hierarchical = 1;
		$empty        = 0;

		$args               = array(
			'post_type'      => 'product',
			'post_status'    => $post_status,
			'taxonomy'       => $taxonomy,
			'orderby'        => $orderby,
			'hierarchical'   => $hierarchical,
			'hide_empty'     => $empty,
			'posts_per_page' => - 1,
		);
		$get_all_categories = get_categories( $args );
		$html               = '';
		if ( isset( $get_all_categories ) && ! empty( $get_all_categories ) ) {
			foreach ( $get_all_categories as $get_all_category ) {

				if ( ! empty( $sitepress ) ) {
					$new_cat_id = apply_filters( 'wpml_object_id', $get_all_category->term_id, 'product_cat', true, $default_lang );
				} else {
					$new_cat_id = $get_all_category->term_id;
				}
				$category        = get_term_by( 'id', $new_cat_id, 'product_cat' );
				$parent_category = get_term_by( 'id', $category->parent, 'product_cat' );

				if ( ! empty( $selected ) ) {
					$selected    = array_map( 'intval', $selected );
					$selectedVal = is_array( $selected ) && ! empty( $selected ) && in_array( $new_cat_id, $selected, true ) ? 'selected=selected' : '';

					if ( $category->parent > 0 ) {
						$html .= '<option value=' . $category->term_id . ' ' . $selectedVal . '>' . '' . $parent_category->name . '->' . $category->name . '</option>';
					} else {
						$html .= '<option value=' . $category->term_id . ' ' . $selectedVal . '>' . $category->name . '</option>';
					}
				} else {
					if ( $category->parent > 0 ) {
						$filter_category_list[ $category->term_id ] = $parent_category->name . '->' . $category->name;
					} else {
						$filter_category_list[ $category->term_id ] = $category->name;
					}
				}
			}
		}
		if ( true === $json ) {
			return wp_json_encode( $this->mmqw_convert_array_to_json( $filter_category_list ) );
		} else {
			return $html;
		}

	}

	/**
	 * Get country list
	 *
	 * @param string $count
	 * @param array  $selected
	 *
	 * @return string $html
	 * @uses   WC_Countries() class
	 *
	 * @since  1.0.0
	 *
	 */
	public function mmqw_pro_get_country_list( $selected = array(), $json = false ) {
		$countries_obj = new WC_Countries();
		$getCountries  = $countries_obj->__get( 'countries' );
		$html          = '';
		if ( ! empty( $getCountries ) ) {
			foreach ( $getCountries as $code => $country ) {
				$selectedVal = is_array( $selected ) && ! empty( $selected ) && in_array( $code, $selected, true ) ? 'selected=selected' : '';
				$html        .= '<option value="' . esc_attr( $code ) . '" ' . esc_attr( $selectedVal ) . '>' . esc_html( $country ) . '</option>';
			}
		}
		if ( $json ) {
			return wp_json_encode( $this->mmqw_convert_array_to_json( $getCountries ) );
		}

		return $html;
	}

	/**
	 * Display product list based product specific option
	 *
	 * @return string $html
	 * @uses   mmqw_get_default_langugae_with_sitpress()
	 * @uses   Min_Max_Quantity_For_WooCommerce::mmqw_allowed_html_tags()
	 *
	 * @since  1.0.0
	 *
	 */
	public function mmqw_product_fees_conditions_values_product_ajax() {
		global $sitepress;

		$json                = true;
		$filter_product_list = [];

		$default_lang = $this->mmqw_get_default_langugae_with_sitpress();

		$request_value = filter_input( INPUT_GET, 'value', FILTER_SANITIZE_STRING );
		$post_value    = isset( $request_value ) ? sanitize_text_field( $request_value ) : '';

		$baselang_product_ids = array();

		function mmqw_posts_where( $where, $wp_query ) {
			global $wpdb;
			$search_term = $wp_query->get( 'search_pro_title' );
			if ( ! empty( $search_term ) ) {
				$search_term_like = $wpdb->esc_like( $search_term );
				$where            .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( $search_term_like ) . '%\'';
			}

			return $where;
		}

		$product_args = array(
			'post_type'        => 'product',
			'posts_per_page'   => - 1,
			'search_pro_title' => $post_value,
			'post_status'      => 'publish',
			'orderby'          => 'title',
			'order'            => 'ASC',
		);

		add_filter( 'posts_where', 'mmqw_posts_where', 10, 2 );
		$get_wp_query = new WP_Query( $product_args );
		remove_filter( 'posts_where', 'mmqw_posts_where', 10, 2 );

		$get_all_products = $get_wp_query->posts;

		if ( isset( $get_all_products ) && ! empty( $get_all_products ) ) {
			foreach ( $get_all_products as $get_all_product ) {
				if ( ! empty( $sitepress ) ) {
					$defaultlang_product_id = apply_filters( 'wpml_object_id', $get_all_product->ID, 'product', true, $default_lang );
				} else {
					$defaultlang_product_id = $get_all_product->ID;
				}
				$baselang_product_ids[] = $defaultlang_product_id;
			}
		}

		$html = '';
		if ( isset( $baselang_product_ids ) && ! empty( $baselang_product_ids ) ) {
			foreach ( $baselang_product_ids as $baselang_product_id ) {
				$html                  .= '<option value="' . esc_attr( $baselang_product_id ) . '">' . '#' . esc_html( $baselang_product_id ) . ' - ' . esc_html( get_the_title( $baselang_product_id ) ) . '</option>';
				$filter_product_list[] = array( $baselang_product_id, get_the_title( $baselang_product_id ) );
			}
		}
		if ( $json ) {
			echo wp_json_encode( $filter_product_list );
			wp_die();
		}
		echo wp_kses( $html, Min_Max_Quantity_For_WooCommerce::mmqw_allowed_html_tags() );
		wp_die();
	}

	/**
	 * Display variable product list based product specific option
	 *
	 * @return string $html
	 * @uses   mmqw_get_default_langugae_with_sitpress()
	 * @uses   wc_get_product()
	 * @uses   WC_Product::is_type()
	 * @uses   Min_Max_Quantity_For_WooCommerce::mmqw_allowed_html_tags()
	 *
	 * @since  1.0.0
	 *
	 */
	public function mmqw_product_fees_conditions_variable_values_product_ajax() {
		global $sitepress;
		$default_lang                 = $this->mmqw_get_default_langugae_with_sitpress();
		$json                         = true;
		$filter_variable_product_list = [];

		$request_value = filter_input( INPUT_GET, 'value', FILTER_SANITIZE_STRING );
		$post_value    = isset( $request_value ) ? sanitize_text_field( $request_value ) : '';

		$baselang_product_ids = array();

		function wcpfc_posts_wheres( $where, &$wp_query ) {
			global $wpdb;
			$search_term = $wp_query->get( 'search_pro_title' );
			if ( ! empty( $search_term ) ) {
				$search_term_like = $wpdb->esc_like( $search_term );
				$where            .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( $search_term_like ) . '%\'';
			}

			return $where;
		}

		$product_args     = array(
			'post_type'        => 'product',
			'posts_per_page'   => - 1,
			'search_pro_title' => $post_value,
			'post_status'      => 'publish',
			'orderby'          => 'title',
			'order'            => 'ASC',
		);
		$get_all_products = new WP_Query( $product_args );

		if ( ! empty( $get_all_products ) ) {
			foreach ( $get_all_products->posts as $get_all_product ) {
				$_product = wc_get_product( $get_all_product->ID );
				if ( $_product->is_type( 'variable' ) ) {
					$variations = $_product->get_available_variations();
					foreach ( $variations as $value ) {
						if ( ! empty( $sitepress ) ) {
							$defaultlang_product_id = apply_filters( 'wpml_object_id', $value['variation_id'], 'product', true, $default_lang );
						} else {
							$defaultlang_product_id = $value['variation_id'];
						}
						$baselang_product_ids[] = $defaultlang_product_id;
					}
				}
			}
		}
		$html = '';
		if ( isset( $baselang_product_ids ) && ! empty( $baselang_product_ids ) ) {
			foreach ( $baselang_product_ids as $baselang_product_id ) {
				$html                           .= '<option value="' . esc_attr( $baselang_product_id ) . '">' . '#' . esc_html( $baselang_product_id ) . ' - ' . esc_html( get_the_title( $baselang_product_id ) ) . '</option>';
				$filter_variable_product_list[] = array( $baselang_product_id, get_the_title( $baselang_product_id ) );
			}
		}
		if ( $json ) {
			echo wp_json_encode( $filter_variable_product_list );
			wp_die();
		}
		echo wp_kses( $html, Min_Max_Quantity_For_WooCommerce::mmqw_allowed_html_tags() );
		wp_die();
	}

	/**
	 * Display simple and variable product list based product specific option in Advance Pricing Rules
	 *
	 * @return string $html
	 * @uses   mmqw_get_default_langugae_with_sitpress()
	 * @uses   wc_get_product()
	 * @uses   WC_Product::is_type()
	 * @uses   get_available_variations()
	 * @uses   Min_Max_Quantity_For_WooCommerce::mmqw_allowed_html_tags()
	 *
	 * @since  3.4
	 *
	 */
	public function mmqw_simple_and_variation_product_list_ajax() {
		global $sitepress;
		$default_lang        = $this->mmqw_get_default_langugae_with_sitpress();
		$json                = true;
		$filter_product_list = [];

		$request_value = filter_input( INPUT_GET, 'value', FILTER_SANITIZE_STRING );
		$post_value    = isset( $request_value ) ? sanitize_text_field( $request_value ) : '';

		$request_with_variable = filter_input( INPUT_GET, 'with_variable', FILTER_SANITIZE_STRING );
		$post_with_variable    = isset( $request_with_variable ) ? sanitize_text_field( $request_with_variable ) : '';

		$baselang_simple_product_ids    = array();
		$baselang_variation_product_ids = array();

		function mmqw_posts_where( $where, $wp_query ) {
			global $wpdb;
			$search_term = $wp_query->get( 'search_pro_title' );
			if ( ! empty( $search_term ) ) {
				$search_term_like = $wpdb->esc_like( $search_term );
				$where            .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( $search_term_like ) . '%\'';
			}

			return $where;
		}

		$product_args = array(
			'post_type'        => 'product',
			'posts_per_page'   => - 1,
			'search_pro_title' => $post_value,
			'post_status'      => 'publish',
			'orderby'          => 'title',
			'order'            => 'ASC',
		);

		add_filter( 'posts_where', 'mmqw_posts_where', 10, 2 );
		$get_wp_query = new WP_Query( $product_args );
		remove_filter( 'posts_where', 'mmqw_posts_where', 10, 2 );

		$get_all_products = $get_wp_query->posts;

		if ( isset( $get_all_products ) && ! empty( $get_all_products ) ) {
			foreach ( $get_all_products as $get_all_product ) {
				$_product = wc_get_product( $get_all_product->ID );
				if ( $_product->is_type( 'variable' ) ) {
					$variations = $_product->get_available_variations();
					foreach ( $variations as $value ) {
						if ( ! empty( $sitepress ) ) {
							$defaultlang_variation_product_id = apply_filters( 'wpml_object_id', $value['variation_id'], 'product', true, $default_lang );
						} else {
							$defaultlang_variation_product_id = $value['variation_id'];
						}
						$baselang_variation_product_ids[] = $defaultlang_variation_product_id;
					}
				}
				if ( $_product->is_type( 'simple' ) ) {
					if ( ! empty( $sitepress ) ) {
						$defaultlang_simple_product_id = apply_filters( 'wpml_object_id', $get_all_product->ID, 'product', true, $default_lang );
					} else {
						$defaultlang_simple_product_id = $get_all_product->ID;
					}
					$baselang_simple_product_ids[] = $defaultlang_simple_product_id;
				}
			}
		}

		if ( 'true' === $post_with_variable ) {
			$baselang_product_ids = $baselang_variation_product_ids;
		} else {
			$baselang_product_ids = $baselang_simple_product_ids;
		}

		$html = '';
		if ( isset( $baselang_product_ids ) && ! empty( $baselang_product_ids ) ) {
			foreach ( $baselang_product_ids as $baselang_product_id ) {
				$html                  .= '<option value="' . esc_attr( $baselang_product_id ) . '">' . '#' . esc_html( $baselang_product_id ) . ' - ' . esc_html( get_the_title( $baselang_product_id ) ) . '</option>';
				$filter_product_list[] = array( $baselang_product_id, get_the_title( $baselang_product_id ) );
			}
		}
		if ( $json ) {
			echo wp_json_encode( $filter_product_list );
			wp_die();
		}
		echo wp_kses( $html, Min_Max_Quantity_For_WooCommerce::mmqw_allowed_html_tags() );
		wp_die();
	}

	/**
	 * Delete multiple shipping method
	 *
	 * @return string $result
	 * @uses   wp_delete_post()
	 *
	 * @since  1.0.0
	 *
	 */
	public function mmqw_wc_multiple_delete_shipping_method() {
		check_ajax_referer( 'dsm_nonce', 'nonce' );

		$result      = 0;
		$get_allVals = filter_input( INPUT_GET, 'allVals', FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY );
		$allVals     = ! empty( $get_allVals ) ? array_map( 'sanitize_text_field', wp_unslash( $get_allVals ) ) : array();
		if ( ! empty( $allVals ) ) {
			foreach ( $allVals as $val ) {
				wp_delete_post( $val );
				$result = 1;
			}
		}
		echo (int) $result;
		wp_die();
	}

	/**
	 * Save shipping method
	 * 
	 * @return bool false if post is empty otherwise it will redirect to shipping method list
	 * @since  1.0.0
	 *
	 * @uses   update_post_meta()
	 *
	 */
	function mmqw_rules_conditions_save( ) {
		global $sitepress;

		if ( empty( $_POST['mmqw_conditions_save'] ) ) {
			return false;
		}
		$post_type            = filter_input( INPUT_POST, 'post_type', FILTER_SANITIZE_STRING );
		$mmqw_conditions_save = filter_input( INPUT_POST, 'mmqw_conditions_save', FILTER_SANITIZE_STRING );

		if ( isset( $post_type ) && self::min_max_quantity_post_type === sanitize_text_field( $post_type ) &&
		     wp_verify_nonce( sanitize_text_field( $mmqw_conditions_save ), 'mmqw_save_action' ) ) {

			$method_id                      = filter_input( INPUT_POST, 'fee_post_id', FILTER_SANITIZE_NUMBER_INT );
			$fees                           = filter_input( INPUT_POST, 'fees', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY );
			$sm_status                      = filter_input( INPUT_POST, 'sm_status', FILTER_SANITIZE_STRING );
			$fee_settings_product_fee_title = filter_input( INPUT_POST, 'fee_settings_product_fee_title', FILTER_SANITIZE_STRING );
			$get_condition_key              = filter_input( INPUT_POST, 'condition_key', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY );

			$get_ap_rule_status = filter_input( INPUT_POST, 'ap_rule_status', FILTER_SANITIZE_STRING );

			$get_cost_on_product_status           = filter_input( INPUT_POST, 'cost_on_product_status', FILTER_SANITIZE_STRING );
			$get_cost_on_product_weight_status    = filter_input( INPUT_POST, 'cost_on_product_weight_status', FILTER_SANITIZE_STRING );
			$get_cost_on_product_variation_status = filter_input( INPUT_POST, 'cost_on_product_variation_status', FILTER_SANITIZE_STRING );
			$get_cost_on_category_status          = filter_input( INPUT_POST, 'cost_on_category_status', FILTER_SANITIZE_STRING );
			$get_cost_on_country_status           = filter_input( INPUT_POST, 'cost_on_country_status', FILTER_SANITIZE_STRING );

			$get_cost_on_total_cart_qty_status = filter_input( INPUT_POST, 'cost_on_total_cart_qty_status', FILTER_SANITIZE_STRING );
			$get_cost_rule_match               = filter_input( INPUT_POST, 'cost_rule_match', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY );
			$get_main_rule_condition           = filter_input( INPUT_POST, 'main_rule_condition', FILTER_SANITIZE_STRING );

			$get_fee_settings_unique_shipping_title = filter_input( INPUT_POST, 'fee_settings_unique_shipping_title', FILTER_SANITIZE_STRING );

			$fee_settings_unique_shipping_title = isset( $get_fee_settings_unique_shipping_title ) ? sanitize_text_field( $get_fee_settings_unique_shipping_title ) : '';

			$ap_rule_status                   = isset( $get_ap_rule_status ) ? sanitize_text_field( $get_ap_rule_status ) : "off";
			$cost_on_product_status           = isset( $get_cost_on_product_status ) ? sanitize_text_field( $get_cost_on_product_status ) : 'off';
			$cost_on_product_weight_status    = isset( $get_cost_on_product_weight_status ) ? sanitize_text_field( $get_cost_on_product_weight_status ) : 'off';
			$cost_on_product_variation_status = isset( $get_cost_on_product_variation_status ) ? sanitize_text_field( $get_cost_on_product_variation_status ) : 'off';

			$cost_on_category_status = isset( $get_cost_on_category_status ) ? sanitize_text_field( $get_cost_on_category_status ) : 'off';
			$cost_on_country_status  = isset( $get_cost_on_country_status ) ? sanitize_text_field( $get_cost_on_country_status ) : 'off';

			$cost_on_total_cart_qty_status = isset( $get_cost_on_total_cart_qty_status ) ? sanitize_text_field( $get_cost_on_total_cart_qty_status ) : 'off';
			$cost_rule_match               = isset( $get_cost_rule_match ) ? array_map( 'sanitize_text_field', $get_cost_rule_match ) : array();
			$main_rule_condition           = isset( $get_main_rule_condition ) ? sanitize_text_field( $get_main_rule_condition ) : '';

			$mmqw_rules_count = self::mmqw_sm_count_rules();

			settype( $method_id, 'integer' );

			if ( isset( $sm_status ) ) {
				$post_status = 'publish';
			} else {
				$post_status = 'draft';
			}

			if ( '' !== $method_id && 0 !== $method_id ) {
				$fee_post  = array(
					'ID'          => $method_id,
					'post_title'  => sanitize_text_field( $fee_settings_product_fee_title ),
					'post_status' => $post_status,
					'menu_order'  => $mmqw_rules_count + 1,
					'post_type'   => self::min_max_quantity_post_type,
				);
				$method_id = wp_update_post( $fee_post );
			} else {
				$fee_post  = array(
					'post_title'  => sanitize_text_field( $fee_settings_product_fee_title ),
					'post_status' => $post_status,
					'menu_order'  => $mmqw_rules_count + 1,
					'post_type'   => self::min_max_quantity_post_type,
				);
				$method_id = wp_insert_post( $fee_post );
			}

			if ( '' !== $method_id && 0 !== $method_id ) {
				if ( $method_id > 0 ) {
					$feesArray                      = array();
					$ap_product_arr                 = array();
					$ap_product_weight_arr          = array();
					$ap_product_variation_arr       = array();
					$ap_category_arr                = array();
					$ap_category_weight_arr         = array();
					$ap_country_arr                 = array();
					$ap_total_cart_qty_arr          = array();
					$ap_shipping_class_subtotal_arr = array();
					$conditions_values_array        = array();

					$condition_key     = isset( $get_condition_key ) ? $get_condition_key : array();
					$fees_conditions   = $fees['product_fees_conditions_condition'];
					$conditions_is     = $fees['product_fees_conditions_is'];
					$conditions_values = isset( $fees['product_fees_conditions_values'] ) && ! empty( $fees['product_fees_conditions_values'] ) ? $fees['product_fees_conditions_values'] : array();
					$size              = count( $fees_conditions );

					foreach ( array_keys( $condition_key ) as $key ) {
						if ( ! array_key_exists( $key, $conditions_values ) ) {
							$conditions_values[ $key ] = array();
						}
					}

					uksort( $conditions_values, 'strnatcmp' );
					foreach ( $conditions_values as $v ) {
						$conditions_values_array[] = $v;
					}
					for ( $i = 0; $i < $size; $i ++ ) {
						$feesArray[] = array(
							'product_fees_conditions_condition' => $fees_conditions[ $i ],
							'product_fees_conditions_is'        => $conditions_is[ $i ],
							'product_fees_conditions_values'    => $conditions_values_array[ $i ],
						);
					}

					//qty for Multiple product
					if ( isset( $fees['ap_product_fees_conditions_condition'] ) ) {
						$fees_products         = $fees['ap_product_fees_conditions_condition'];
						$fees_ap_prd_min_qty   = $fees['ap_fees_ap_prd_min_qty'];
						$fees_ap_prd_max_qty   = $fees['ap_fees_ap_prd_max_qty'];
						$fees_ap_price_product = $fees['ap_fees_ap_price_product'];

						$prd_arr = array();
						foreach ( $fees_products as $fees_prd_val ) {
							$prd_arr[] = $fees_prd_val;
						}

						$size_product_cond = count( $fees_products );

						if ( ! empty( $size_product_cond ) && $size_product_cond > 0 ):
							for ( $product_cnt = 0; $product_cnt < $size_product_cond; $product_cnt ++ ) {
								foreach ( $prd_arr as $prd_key => $prd_val ) {
									if ( $prd_key === $product_cnt ) {
										$ap_product_arr[] = array(
											'ap_fees_products'         => $prd_val,
											'ap_fees_ap_prd_min_qty'   => $fees_ap_prd_min_qty[ $product_cnt ],
											'ap_fees_ap_prd_max_qty'   => $fees_ap_prd_max_qty[ $product_cnt ],
											'ap_fees_ap_price_product' => $fees_ap_price_product[ $product_cnt ],
										);
									}
								}
							}
						endif;
					}

					//product subtotal
					if ( isset( $fees['ap_product_variation_fees_conditions_condition'] ) ) {
						$fees_product_variation            = $fees['ap_product_variation_fees_conditions_condition'];
						$fees_ap_product_variation_min_qty = $fees['ap_fees_ap_product_variation_min_qty'];
						$fees_ap_product_variation_max_qty = $fees['ap_fees_ap_product_variation_max_qty'];
						$fees_ap_product_variation_price   = $fees['ap_fees_ap_price_product_variation'];

						$product_variation_arr = array();
						foreach ( $fees_product_variation as $fees_product_variation_val ) {
							$product_variation_arr[] = $fees_product_variation_val;
						}
						$size_product_variation_cond = count( $fees_product_variation );

						if ( ! empty( $size_product_variation_cond ) && $size_product_variation_cond > 0 ):
							for ( $product_variation_cnt = 0; $product_variation_cnt < $size_product_variation_cond; $product_variation_cnt ++ ) {
								if ( ! empty( $product_variation_arr ) && '' !== $product_variation_arr ) {
									foreach ( $product_variation_arr as $product_variation_key => $product_variation_val ) {
										if ( $product_variation_key === $product_variation_cnt ) {
											$ap_product_variation_arr[] = array(
												'ap_fees_product_variation'            => $product_variation_val,
												'ap_fees_ap_product_variation_min_qty' => $fees_ap_product_variation_min_qty[ $product_variation_cnt ],
												'ap_fees_ap_product_variation_max_qty' => $fees_ap_product_variation_max_qty[ $product_variation_cnt ],
												'ap_fees_ap_price_product_variation'   => $fees_ap_product_variation_price[ $product_variation_cnt ],
											);
										}
									}
								}
							}
						endif;
					}

					/**  qty for Multiple category */
					if ( isset( $fees['ap_category_fees_conditions_condition'] ) ) {
						$fees_categories        = $fees['ap_category_fees_conditions_condition'];
						$fees_ap_cat_min_qty    = $fees['ap_fees_ap_cat_min_qty'];
						$fees_ap_cat_max_qty    = $fees['ap_fees_ap_cat_max_qty'];
						$fees_ap_price_category = $fees['ap_fees_ap_price_category'];

						$cat_arr = array();
						foreach ( $fees_categories as $fees_cat_val ) {
							$cat_arr[] = $fees_cat_val;
						}
						$size_category_cond = count( $fees_categories );

						if ( ! empty( $size_category_cond ) && $size_category_cond > 0 ):
							for ( $category_cnt = 0; $category_cnt < $size_category_cond; $category_cnt ++ ) {
								if ( ! empty( $cat_arr ) && '' !== $cat_arr ) {
									foreach ( $cat_arr as $cat_key => $cat_val ) {
										if ( $cat_key === $category_cnt ) {
											$ap_category_arr[] = array(
												'ap_fees_categories'        => $cat_val,
												'ap_fees_ap_cat_min_qty'    => $fees_ap_cat_min_qty[ $category_cnt ],
												'ap_fees_ap_cat_max_qty'    => $fees_ap_cat_max_qty[ $category_cnt ],
												'ap_fees_ap_price_category' => $fees_ap_price_category[ $category_cnt ],
											);
										}
									}
								}
							}
						endif;
					}

					/**  category subtotal */
					if ( isset( $fees['ap_country_fees_conditions_condition'] ) ) {
						$fees_country            = $fees['ap_country_fees_conditions_condition'];
						$fees_ap_country_min_qty = $fees['ap_fees_ap_country_min_subtotal'];
						$fees_ap_country_max_qty = $fees['ap_fees_ap_country_max_subtotal'];
						$fees_ap_price_country   = $fees['ap_fees_ap_price_country'];

						$country_arr = array();
						foreach ( $fees_country as $fees_country_val ) {
							$country_arr[] = $fees_country_val;
						}
						$size_country_cond = count( $fees_country );

						if ( ! empty( $size_country_cond ) && $size_country_cond > 0 ):
							for ( $country_cnt = 0; $country_cnt < $size_country_cond; $country_cnt ++ ) {
								if ( ! empty( $country_arr ) && '' !== $country_arr ) {
									foreach ( $country_arr as $country_key => $country_val ) {
										if ( $country_key === $country_cnt ) {
											$ap_country_arr[] = array(
												'ap_fees_country'                 => $country_val,
												'ap_fees_ap_country_min_subtotal' => $fees_ap_country_min_qty[ $country_cnt ],
												'ap_fees_ap_country_max_subtotal' => $fees_ap_country_max_qty[ $country_cnt ],
												'ap_fees_ap_price_country'        => $fees_ap_price_country[ $country_cnt ],
											);
										}
									}
								}
							}
						endif;
					}

					//qty for total cart qty
					if ( isset( $fees['ap_total_cart_qty_fees_conditions_condition'] ) ) {
						$fees_total_cart_qty            = $fees['ap_total_cart_qty_fees_conditions_condition'];
						$fees_ap_total_cart_qty_min_qty = $fees['ap_fees_ap_total_cart_qty_min_qty'];
						$fees_ap_total_cart_qty_max_qty = $fees['ap_fees_ap_total_cart_qty_max_qty'];
						$fees_ap_price_total_cart_qty   = $fees['ap_fees_ap_price_total_cart_qty'];

						$total_cart_qty_arr = array();
						foreach ( $fees_total_cart_qty as $fees_total_cart_qty_val ) {
							$total_cart_qty_arr[] = $fees_total_cart_qty_val;
						}
						$size_total_cart_qty_cond = count( $fees_total_cart_qty );

						if ( ! empty( $size_total_cart_qty_cond ) && $size_total_cart_qty_cond > 0 ):
							for ( $total_cart_qty_cnt = 0; $total_cart_qty_cnt < $size_total_cart_qty_cond; $total_cart_qty_cnt ++ ) {
								if ( ! empty( $total_cart_qty_arr ) && '' !== $total_cart_qty_arr ) {
									foreach ( $total_cart_qty_arr as $total_cart_qty_key => $total_cart_qty_val ) {
										if ( $total_cart_qty_key === $total_cart_qty_cnt ) {
											$ap_total_cart_qty_arr[] = array(
												'ap_fees_total_cart_qty'            => $total_cart_qty_val,
												'ap_fees_ap_total_cart_qty_min_qty' => $fees_ap_total_cart_qty_min_qty[ $total_cart_qty_cnt ],
												'ap_fees_ap_total_cart_qty_max_qty' => $fees_ap_total_cart_qty_max_qty[ $total_cart_qty_cnt ],
												'ap_fees_ap_price_total_cart_qty'   => $fees_ap_price_total_cart_qty[ $total_cart_qty_cnt ],
											);
										}
									}
								}
							}
						endif;
					}

					//product weight
					if ( isset( $fees['ap_product_weight_fees_conditions_condition'] ) ) {
						$fees_product_weight            = $fees['ap_product_weight_fees_conditions_condition'];
						$fees_ap_product_weight_min_qty = $fees['ap_fees_ap_product_weight_min_weight'];
						$fees_ap_product_weight_max_qty = $fees['ap_fees_ap_product_weight_max_weight'];
						$fees_ap_price_product_weight   = $fees['ap_fees_ap_price_product_weight'];

						$product_weight_arr = array();
						foreach ( $fees_product_weight as $fees_product_weight_val ) {
							$product_weight_arr[] = $fees_product_weight_val;
						}
						$size_product_weight_cond = count( $fees_product_weight );

						if ( ! empty( $size_product_weight_cond ) && $size_product_weight_cond > 0 ):
							for ( $product_weight_cnt = 0; $product_weight_cnt < $size_product_weight_cond; $product_weight_cnt ++ ) {
								if ( ! empty( $product_weight_arr ) && '' !== $product_weight_arr ) {
									foreach ( $product_weight_arr as $product_weight_key => $product_weight_val ) {
										if ( $product_weight_key === $product_weight_cnt ) {
											$ap_product_weight_arr[] = array(
												'ap_fees_product_weight'            => $product_weight_val,
												'ap_fees_ap_product_weight_min_qty' => $fees_ap_product_weight_min_qty[ $product_weight_cnt ],
												'ap_fees_ap_product_weight_max_qty' => $fees_ap_product_weight_max_qty[ $product_weight_cnt ],
												'ap_fees_ap_price_product_weight'   => $fees_ap_price_product_weight[ $product_weight_cnt ],
											);
										}
									}
								}
							}
						endif;
					}
					//Shipping Class subtotal
					if ( isset( $fees['ap_shipping_class_subtotal_fees_conditions_condition'] ) ) {
						$fees_shipping_class_subtotal                 = $fees['ap_shipping_class_subtotal_fees_conditions_condition'];
						$fees_ap_shipping_class_subtotal_min_subtotal = $fees['ap_fees_ap_shipping_class_subtotal_min_subtotal'];
						$fees_ap_shipping_class_subtotal_max_subtotal = $fees['ap_fees_ap_shipping_class_subtotal_max_subtotal'];
						$fees_ap_price_shipping_class_subtotal        = $fees['ap_fees_ap_price_shipping_class_subtotal'];

						$shipping_class_subtotal_arr = array();
						foreach ( $fees_shipping_class_subtotal as $shipping_class_subtotal_key => $shipping_class_subtotal_val ) {
							$shipping_class_subtotal_arr[] = $shipping_class_subtotal_val;
						}
						$size_shipping_class_subtotal_cond = count( $fees_shipping_class_subtotal );

						if ( ! empty( $size_shipping_class_subtotal_cond ) && $size_shipping_class_subtotal_cond > 0 ):
							for ( $shipping_class_subtotal_cnt = 0; $shipping_class_subtotal_cnt < $size_shipping_class_subtotal_cond; $shipping_class_subtotal_cnt ++ ) {
								if ( ! empty( $shipping_class_subtotal_arr ) && $shipping_class_subtotal_arr !== '' ) {
									foreach ( $shipping_class_subtotal_arr as $shipping_class_subtotal_key => $shipping_class_subtotal_val ) {
										if ( $shipping_class_subtotal_key === $shipping_class_subtotal_cnt ) {
											$ap_shipping_class_subtotal_arr[] = array(
												'ap_fees_shipping_class_subtotals'                => $shipping_class_subtotal_val,
												'ap_fees_ap_shipping_class_subtotal_min_subtotal' => $fees_ap_shipping_class_subtotal_min_subtotal[ $shipping_class_subtotal_cnt ],
												'ap_fees_ap_shipping_class_subtotal_max_subtotal' => $fees_ap_shipping_class_subtotal_max_subtotal[ $shipping_class_subtotal_cnt ],
												'ap_fees_ap_price_shipping_class_subtotal'        => $fees_ap_price_shipping_class_subtotal[ $shipping_class_subtotal_cnt ],
											);
										}
									}
								}
							}
						endif;
					}
					update_post_meta( $method_id, 'ap_rule_status', $ap_rule_status );
					update_post_meta( $method_id, 'cost_rule_match', maybe_serialize( $cost_rule_match ) );
					update_post_meta( $method_id, 'main_rule_condition', $main_rule_condition );

					/** Advance Pricing Rules Particular Status */
					update_post_meta( $method_id, 'cost_on_product_status', $cost_on_product_status );
					update_post_meta( $method_id, 'cost_on_product_weight_status', $cost_on_product_weight_status );
					update_post_meta( $method_id, 'cost_on_product_variation_status', $cost_on_product_variation_status );

					update_post_meta( $method_id, 'cost_on_category_status', $cost_on_category_status );
					update_post_meta( $method_id, 'cost_on_country_status', $cost_on_country_status );

					update_post_meta( $method_id, 'cost_on_total_cart_qty_status', $cost_on_total_cart_qty_status );

					if ( isset( $sm_select_day_of_week ) ) {
						update_post_meta( $method_id, 'sm_select_day_of_week', $sm_select_day_of_week );
					}

					if ( isset( $sm_time_from ) ) {
						update_post_meta( $method_id, 'sm_time_from', $sm_time_from );
					}
					if ( isset( $sm_time_to ) ) {
						update_post_meta( $method_id, 'sm_time_to', $sm_time_to );
					}

					update_post_meta( $method_id, 'fee_settings_unique_shipping_title', $fee_settings_unique_shipping_title );
					update_post_meta( $method_id, 'sm_metabox', $feesArray );
					update_post_meta( $method_id, 'sm_metabox_ap_product', $ap_product_arr );
					update_post_meta( $method_id, 'sm_metabox_ap_product_weight', $ap_product_weight_arr );
					update_post_meta( $method_id, 'sm_metabox_ap_product_variation', $ap_product_variation_arr );

					update_post_meta( $method_id, 'sm_metabox_ap_category', $ap_category_arr );
					update_post_meta( $method_id, 'sm_metabox_ap_category_weight', $ap_category_weight_arr );
					update_post_meta( $method_id, 'sm_metabox_ap_country', $ap_country_arr );
					if ( ! empty( $sitepress ) ) {
						do_action( 'wpml_register_single_string', 'min-and-max-quantity-for-woocommerce', sanitize_text_field( $fee_settings_product_fee_title ), sanitize_text_field( $fee_settings_product_fee_title ) );
					}

					$getSortOrder = get_option( 'sm_sortable_order' );
					if ( ! empty( $getSortOrder ) ) {
						foreach ( $getSortOrder as $getSortOrder_id ) {
							settype( $getSortOrder_id, 'integer' );

						}
						array_unshift( $getSortOrder, $method_id );
					}
					update_option( 'sm_sortable_order', $getSortOrder );
				}
			} else {
				echo '<div class="updated error"><p>' . esc_html__( 'Error saving shipping method.', 'min-and-max-quantity-for-woocommerce' ) . '</p></div>';

				return false;
			}

			$mmqwnonce = wp_create_nonce( 'mmqwnonce' );
			wp_safe_redirect( add_query_arg( array( 'page' => 'mmqw-rules-list', '_wpnonce' => esc_attr( $mmqwnonce ) ), admin_url( 'admin.php' ) ) );
			exit();
		}
	}

	/**
	 * Count total shipping method
	 *
	 * @return int $count_method
	 * @since    3.5
	 *
	 */
	public static function mmqw_sm_count_rules() {
		$shipping_method_args = array(
			'post_type'      => self::min_max_quantity_post_type,
			'post_status'    => array( 'publish', 'draft' ),
			'posts_per_page' => - 1,
			'orderby'        => 'ID',
			'order'          => 'DESC',
		);
		$sm_post_query        = new WP_Query( $shipping_method_args );
		$shipping_method_list = $sm_post_query->posts;

		return count( $shipping_method_list );
	}

	/**
	 * Review message in footer
	 *
	 * @return string
	 * @since  1.0.0
	 *
	 */
	public function mmqw_admin_footer_review() {
		echo sprintf( wp_kses( __( 'If you like <strong>Minimum and Maximum Quantity for WooCommerce</strong> plugin, please leave us ★★★★★ ratings on <a href="%1$s" target="_blank">DotStore</a>.', 'min-and-max-quantity-for-woocommerce' ), array(
			'strong' => array(),
			'a'      => array( 'href' => array() ),
		) ), esc_url( 'https://wordpress.org/plugins/min-and-max-quantity-for-woocommerce/#reviews' ) );
	}

	/**
	 * Clone shipping method
	 *
	 * @return string true if current_shipping_id is empty then it will give message.
	 * @uses   get_post()
	 * @uses   wp_get_current_user()
	 * @uses   wp_insert_post()
	 *
	 * @since  3.4
	 *
	 */
	public function mmqw_clone_rule() {
		/* Check for post request */
		$get_current_shipping_id = filter_input( INPUT_GET, 'current_shipping_id', FILTER_SANITIZE_NUMBER_INT );

		$get_post_id = isset( $get_current_shipping_id ) ? absint( $get_current_shipping_id ) : '';

		if ( empty( $get_post_id ) ) {
			echo sprintf( wp_kses( __( '<strong>No post to duplicate has been supplied!</strong>', 'min-and-max-quantity-for-woocommerce' ), array( 'strong' => array() ) ) );
			wp_die();

		}
		/* End of if */
		/* Get the original post id */
		if ( ! empty( $get_post_id ) || '' !== $get_post_id ) {
			/* Get all the original post data */
			$post = get_post( $get_post_id );
			/* Get current user and make it new post user (duplicate post) */
			$current_user    = wp_get_current_user();
			$new_post_author = $current_user->ID;
			/* If post data exists, duplicate the data into new duplicate post */
			if ( isset( $post ) && null !== $post ) {
				/* New post data array */
				$args = array(
					'comment_status' => $post->comment_status,
					'ping_status'    => $post->ping_status,
					'post_author'    => $new_post_author,
					'post_content'   => $post->post_content,
					'post_excerpt'   => $post->post_excerpt,
					'post_name'      => $post->post_name,
					'post_parent'    => $post->post_parent,
					'post_password'  => $post->post_password,
					'post_status'    => 'draft',
					'post_title'     => $post->post_title . '-duplicate',
					'post_type'      => self::min_max_quantity_post_type,
					'to_ping'        => $post->to_ping,
					'menu_order'     => $post->menu_order,
				);
				/* Duplicate the post by wp_insert_post() function */
				$new_post_id = wp_insert_post( $args );
				/* Duplicate all post meta-data */
				$post_meta_data = get_post_meta( $get_post_id );
				if ( 0 !== count( $post_meta_data ) ) {
					foreach ( $post_meta_data as $meta_key => $meta_data ) {
						if ( '_wp_old_slug' === $meta_key ) {
							continue;
						}
						$meta_value = maybe_unserialize( $meta_data[0] );
						update_post_meta( $new_post_id, $meta_key, $meta_value );
					}
				}
			}
			$mmqwnonce    = wp_create_nonce( 'mmqwnonce' );
			$redirect_url = add_query_arg( array(
				'page'     => 'mmqw-edit-rule',
				'id'       => $new_post_id,
				'action'   => 'edit',
				'_wpnonce' => esc_attr( $mmqwnonce ),
			), admin_url( 'admin.php' ) );
			echo wp_json_encode( array( true, $redirect_url ) );
		}
		wp_die();
	}

	/**
	 * Change shipping status from list of shipping method
	 *
	 * @since  3.4
	 *
	 * @uses   update_post_meta()
	 *
	 * if current_shipping_id is empty then it will give message.
	 */
	public function mmqw_change_status_from_list_section() {
		global $sitepress;
		$default_lang = $this->mmqw_get_default_langugae_with_sitpress();
		/* Check for post request */
		$get_current_shipping_id = filter_input( INPUT_GET, 'current_shipping_id', FILTER_SANITIZE_NUMBER_INT );
		if ( ! empty( $sitepress ) ) {
			$get_current_shipping_id = apply_filters( 'wpml_object_id', $get_current_shipping_id, 'product', true, $default_lang );
		} else {
			$get_current_shipping_id = $get_current_shipping_id;
		}

		$get_current_value = filter_input( INPUT_GET, 'current_value', FILTER_SANITIZE_STRING );

		$get_post_id = isset( $get_current_shipping_id ) ? absint( $get_current_shipping_id ) : '';

		if ( empty( $get_post_id ) ) {
			echo '<strong>' . esc_html__( 'Something went wrong', 'min-and-max-quantity-for-woocommerce' ) . '</strong>';
			wp_die();
		}

		$current_value = isset( $get_current_value ) ? sanitize_text_field( $get_current_value ) : '';

		if ( 'true' === $current_value ) {
			$post_args   = array(
				'ID'          => $get_post_id,
				'post_status' => 'publish',
				'post_type'   => self::min_max_quantity_post_type,
			);
			$post_update = wp_update_post( $post_args );
			update_post_meta( $get_post_id, 'sm_status', 'on' );
		} else {
			$post_args   = array(
				'ID'          => $get_post_id,
				'post_status' => 'draft',
				'post_type'   => self::min_max_quantity_post_type,
			);
			$post_update = wp_update_post( $post_args );
			update_post_meta( $get_post_id, 'sm_status', 'off' );
		}
		if ( ! empty( $post_update ) ) {
			echo esc_html__( 'Rule status changed successfully.', 'min-and-max-quantity-for-woocommerce' );
		} else {
			echo esc_html__( 'Something went wrong', 'min-and-max-quantity-for-woocommerce' );
		}
		wp_die();
	}

	/**
	 * Change Advance pricing rules status
	 *
	 * @return string true if current_shipping_id is empty then it will give message.
	 *
	 * @uses   update_post_meta()
	 *
	 * @since  3.4
	 *
	 */
	public function mmqw_change_status_of_advance_pricing_rules() {
		$get_current_shipping_id = filter_input( INPUT_GET, 'current_shipping_id', FILTER_SANITIZE_NUMBER_INT );
		$get_current_value       = filter_input( INPUT_GET, 'current_value', FILTER_SANITIZE_STRING );

		$get_post_id = isset( $get_current_shipping_id ) ? absint( $get_current_shipping_id ) : '';

		if ( empty( $get_post_id ) ) {
			echo '<strong>' . esc_html__( 'Something went wrong', 'min-and-max-quantity-for-woocommerce' ) . '</strong>';
			wp_die();
		}
		$current_value = isset( $get_current_value ) ? sanitize_text_field( $get_current_value ) : '';

		if ( 'true' === $current_value ) {
			update_post_meta( $get_post_id, 'ap_rule_status', 'off' );
			echo esc_html( 'true' );
		}
		wp_die();
	}

	/**
	 * Save all the custom messages
	 *
	 * @param array $custom_messages_array
	 *
	 * @return bool
	 */
	function mmqw_custom_messages_save( $custom_messages_array = array() ) {

		if ( empty( $custom_messages_array ) ) {
			return false;
		}
		$min_order_quantity_reached  = ! empty( $custom_messages_array['min_order_quantity_reached'] ) ? sanitize_textarea_field( wp_unslash( $custom_messages_array['min_order_quantity_reached'] ) ) : '';
		$max_order_quantity_exceeded = ! empty( $custom_messages_array['max_order_quantity_exceeded'] ) ? sanitize_textarea_field( wp_unslash( $custom_messages_array['max_order_quantity_exceeded'] ) ) : '';

		$min_order_value_reached  = ! empty( $custom_messages_array['min_order_value_reached'] ) ? sanitize_textarea_field( wp_unslash( $custom_messages_array['min_order_value_reached'] ) ) : '';
		$max_order_value_exceeded = ! empty( $custom_messages_array['max_order_value_exceeded'] ) ? sanitize_textarea_field( wp_unslash( $custom_messages_array['max_order_value_exceeded'] ) ) : '';

		$min_order_item_reached  = ! empty( $custom_messages_array['min_order_item_reached'] ) ? sanitize_textarea_field( wp_unslash( $custom_messages_array['min_order_item_reached'] ) ) : '';
		$max_order_item_exceeded = ! empty( $custom_messages_array['max_order_item_exceeded'] ) ? sanitize_textarea_field( wp_unslash( $custom_messages_array['max_order_item_exceeded'] ) ) : '';

		update_option( 'min_order_quantity_reached', $min_order_quantity_reached );

		update_option( 'max_order_quantity_exceeded', $max_order_quantity_exceeded );

		update_option( 'min_order_value_reached', $min_order_value_reached );

		update_option( 'max_order_value_exceeded', $max_order_value_exceeded );

		update_option( 'min_order_item_reached', $min_order_item_reached );

		update_option( 'max_order_item_exceeded', $max_order_item_exceeded );

		return true;
	}

	/**
	 * Save custom checkout page settings
	 *
	 * @param array $checkout_settings_array
	 *
	 * @return bool
	 */
	function mmqw_checkout_settings_save( $checkout_settings_array = array() ) {

		if ( empty( $checkout_settings_array ) ) {
			return false;
		}
		$min_order_quantity = ! empty( $checkout_settings_array['min_order_quantity'] ) ? sanitize_text_field( wp_unslash( $checkout_settings_array['min_order_quantity'] ) ) : '';
		$max_order_quantity = ! empty( $checkout_settings_array['max_order_quantity'] ) ? sanitize_text_field( wp_unslash( $checkout_settings_array['max_order_quantity'] ) ) : '';
		$min_order_value    = ! empty( $checkout_settings_array['min_order_value'] ) ? sanitize_text_field( wp_unslash( $checkout_settings_array['min_order_value'] ) ) : '';
		$max_order_value    = ! empty( $checkout_settings_array['max_order_value'] ) ? sanitize_text_field( wp_unslash( $checkout_settings_array['max_order_value'] ) ) : '';
		$min_items_quantity = ! empty( $checkout_settings_array['min_items_quantity'] ) ? sanitize_text_field( wp_unslash( $checkout_settings_array['min_items_quantity'] ) ) : '';
		$max_items_quantity = ! empty( $checkout_settings_array['max_items_quantity'] ) ? sanitize_text_field( wp_unslash( $checkout_settings_array['max_items_quantity'] ) ) : '';

		update_option( 'min_order_quantity', $min_order_quantity );

		update_option( 'max_order_quantity', $max_order_quantity );

		update_option( 'min_order_value', $min_order_value );

		update_option( 'max_order_value', $max_order_value );

		update_option( 'min_items_quantity', $min_items_quantity );

		update_option( 'max_items_quantity', $max_items_quantity );

		return true;
	}
}